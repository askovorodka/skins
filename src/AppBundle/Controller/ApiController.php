<?php
/**
 * Created by PhpStorm.
 * User: ahimas
 * Date: 15.11.16
 * Time: 19:39.
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Deposit;
use AppBundle\Exception\DepositPushBackException;
use AppBundle\Exception\UnacceptableItemSubmittedException;
use AppBundle\Service\SteamService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ApiController.
 *
 * @Route("/api/v2")
 */
class ApiController extends Controller
{
    const STATUS_ERROR = 'error';
    const STATUS_SUCCESS = 'success';

    /**
     * @param $time
     * @param $botSecret
     *
     * @return string
     */
    public static function sign($time, $botSecret): string
    {
        return md5($botSecret.$time);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/trade-accepted", name="trade_accepted")
     * @Method("POST")
     */
    public function tradeAcceptedPushAction(Request $request)
    {
        $time = $request->headers->get('Time');
        $data = $request->request->all();
        $botSecret = $this->getParameter('bot_secret');
        if (!self::isSignCorrect($data['sign'], $time, $botSecret)) {
            return $this->json(['status' => self::STATUS_ERROR, 'message' => 'Invalid sign']);
        }

        if (!isset($data['deposit_id']) || $data['deposit_id'] == null) {
            return $this->json(['status' => self::STATUS_ERROR, 'message' => 'Need deposit Id']);
        }

        if ($data['status'] == SteamService::BOT_RESPONSE_SUCCESS) {
            try {
                $this->get('logger')->crit('bot response', [$data]);
                $this->get('app.deposit_service')->confirmTrade($data['deposit_id'], json_decode($data['items'], true));
            } catch (DepositPushBackException $e) {
                return $this->json(['status' => self::STATUS_ERROR, 'error_message' => $e->getMessage()]);
            } catch (UnacceptableItemSubmittedException $e) {
                return $this->json(['status' => self::STATUS_ERROR, 'error_message' => $e->getMessage()]);
            }
        } else {
            $message = $data['err'] ?? null;
            $this->get('app.deposit_service')->declineTrade($data['deposit_id'], $message);
        }

        return $this->json(['status' => self::STATUS_SUCCESS]);
    }

    public static function isSignCorrect($sign, $time, $botSecret)
    {
        return self::sign($time, $botSecret) === $sign;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @Route("{_locale}/is-steam-rip", name="is_steam_rip", options={"expose"=true})
     */
    public function isSteamRip(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->json(['status' => self::STATUS_ERROR, 'message' => 'only ajax']);
        }

        $steamStatus = $this->get('app.steam_service')->checkSteamStatus();

        return new JsonResponse([
            'status' => strtolower($steamStatus),
            'message' => $this->get('translator')->trans('steam_status_'.strtolower($steamStatus)),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @Route("/whitelist", name="whitelist")
     */
    public function whiteList(Request $request)
    {
        $data = $request->query->all();
        $this->get('logger')->critical('bot asking whitelist', $data);
        if (!isset($data['key']) || $data['key'] != $this->getParameter('bot_secret')) {
            $this->get('logger')->critical('bot invalid key');

            return $this->json(['status' => self::STATUS_ERROR, 'message' => 'Invalid key']);
        }
        $this->get('logger')->critical('bot success response');

        return $this->json($this->get('app.items_price_service')->getItemsWhiteList());
    }

    /**
     * @param Request $request
     * @Route("/deposit-items", name="get_deposit_items")
     * @Method("POST")
     */
    public function getDepositItems(Request $request)
    {
        try {
            $return = ['status' => self::STATUS_SUCCESS, 'data' => []];
            $time = $request->headers->get('Time');
            $botSecret = $this->getParameter('bot_secret');
            $data = $request->request->all();
            $depositService = $this->get('app.deposit_service');

            if (!self::isSignCorrect($data['sign'], $time, $botSecret)) {
                throw new \Exception("Invalid sign");
            }

            if (!isset($data['deposit_id']) || !is_array($data['deposit_id'])) {
                throw new \Exception("Need deposit_id as array");
            }

            $args = [
                'deposit_id' => [
                    'filter' => FILTER_VALIDATE_INT,
                    'flags' => FILTER_FORCE_ARRAY,
                ],
            ];

            $input = filter_var_array($data, $args);
            $ids = array_filter($input['deposit_id'], function ($value) {
                return is_numeric($value);
            });

            if (!empty($ids)) {
                foreach ($ids as $depositId) {
                    $deposit = $depositService->getDepositById($depositId);
                    if ($deposit instanceof Deposit) {
                        $return['data'][$depositId] = [
                            'id' => $deposit->getId(),
                            'partner_name' => $deposit->getIntegration()->getName(),
                            'partner_id' => $deposit->getIntegration()->getId(),
                            'items' => []
                        ];
                        $items = $deposit->getItems();
                        if (!empty($items)) {
                            foreach ($items as $item) {
                                $return['data'][$depositId]['items'][] = [
                                    'id' => $item['id'],
                                    'price' => $item['price'] ?? $item['value'],
                                    'rate' => $item['rate_value'] ?? 0,
                                    'orig_price' => $item['orig_price'] ?? 0,
                                    'currency' => $deposit->getCurrency(),
                                    'app_id' => $item['app_id'] ?? 0,
                                ];
                            }
                        }
                    }

                }
            }
        } catch (\Exception $exception) {
            $return['status'] = self::STATUS_ERROR;
            $return['data'] = $exception->getMessage();
        } finally {
            return $this->json($return);
        }



    }

    /**
     * method show all partners ignore demo
     * @param Request $request
     * @Route("/partners", name="get_partners")
     * @Method("POST")
     *
     */
    public function getPartners(Request $request)
    {
        try {
            $return = ['status' => 'success', 'data' => []];
            $time = $request->headers->get('Time');
            $botSecret = $this->getParameter('bot_secret');
            $data = $request->request->all();
            $integrationService = $this->get('app.integration_service');

            if (!self::isSignCorrect($data['sign'], $time, $botSecret)) {
                throw new \Exception('Invalid sign');
            }

            $allPartners = $integrationService->getAllIntegrations();

            foreach ($allPartners as $partner) {
                array_push($return['data'], [
                    'id' => $partner->getId(),
                    'name' => $partner->getName(),
                ]);
            }

        } catch (\Exception $exception) {
            $return['status'] = 'error';
            $return['data'] = $exception->getMessage();
        } finally {
            return $this->json($return);
        }
    }
}
