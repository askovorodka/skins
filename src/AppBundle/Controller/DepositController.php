<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Deposit;
use AppBundle\Exception\IncorrectHashException;
use AppBundle\Exception\IntegrationNotFoundException;
use AppBundle\Exception\InvalidTradeUrlException;
use AppBundle\Exception\SteamInventoryLoadException;
use AppBundle\Exception\UnacceptableItemSubmittedException;
use AppBundle\Exception\UnsupportedCurrencyException;
use AppBundle\Service\ItemsPriceService;
use AppBundle\Service\SteamService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfToken;

/**
 * Class DepositController.
 *
 * @Route("/{_locale}")
 */
class DepositController extends Controller
{
    const STATUS_ERROR = 'error';
    const STATUS_SUCCESS = 'success';

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws IncorrectHashException
     * @throws IntegrationNotFoundException
     *
     * @Route("/", name="index")
     */
    public function validateRequestAndCreateDepositAction(Request $request)
    {
        $publicKey = $request->get('public_key');
        $hash = $request->get('sign');
        $orderId = $request->get('order_id');
        $tradeUrl = $request->get('trade_url');

        if (($publicKey == null || $orderId == null || $tradeUrl == null) && !$request->getSession()->has('deposit_id')) {
            return $this->renderError('bad request');
        }

        if ($depositId = $request->getSession()->get('deposit_id')) {
            try {
                $deposit = $this->get('app.deposit_service')->getDepositById($depositId);
                if (!$orderId || $orderId == $deposit->getOrderId()) {
                    $homeUrl = $deposit->getIntegration()->getHomeUrl() ?? $request->getSession()->get('ref_url');

                    //if trade_url expired
                    if ($deposit->getStatus() == Deposit::STATUS_TERMINAL) {
                        return $this->renderError($this->get('translator')->trans('trade_url.expires'));
                    }

                    return $this->render('default/index.html.twig', [
                        'deposit' => $deposit,
                        'home_url' => $homeUrl,
                        'server_time' => new \DateTime(),
                        'success_url' => $deposit->getIntegration()->getSuccessUrl() . '/?order_id=' . $deposit->getOrderId(),
                        'confirm_trade_offer_url' => SteamService::STEAM_TRADE_OFFER_URL . $deposit->getTradeOfferId(),
                        'is_whitelabel' => $deposit->getIntegration()->isWhitelabel(),
                        'logo_url' => $deposit->getIntegration()->getLogoUrl()
                    ]);

                }
            } catch (\Exception $exception) {
                return $this->renderError('deposit_id not found');
            }
        }

        try {
            $integration = $this->get('app.integration_service')->getIntegrationByPublicKey($publicKey);
            $this->get('app.integration_service')->checkHash($integration, $request->query->all(), $hash);
            $currency = $request->get('currency', Deposit::CURRENCY_RUB);
            $deposit = $this->get('app.deposit_service')->newDeposit($integration, $orderId, $tradeUrl, $currency);
            $request->getSession()->set('deposit_id', $deposit->getId());
            $homeUrl = $deposit->getIntegration()->getHomeUrl() ?? $request->getSession()->get('ref_url');

            return $this->render('default/index.html.twig', [
                'deposit' => $deposit,
                'home_url' => $homeUrl,
                'server_time' => new \DateTime(),
                'success_url' => $deposit->getIntegration()->getSuccessUrl().'/?order_id='.$deposit->getOrderId(),
                'confirm_trade_offer_url' => SteamService::STEAM_TRADE_OFFER_URL.$deposit->getTradeOfferId(),
                'is_whitelabel' => $deposit->getIntegration()->isWhitelabel(),
                'logo_url' => $deposit->getIntegration()->getLogoUrl()
            ]);
        } catch (InvalidTradeUrlException $e) {
            return $this->renderError($e->getMessage());
        } catch (IntegrationNotFoundException $e) {
            return $this->renderError($e->getMessage());
        } catch (IncorrectHashException $e) {
            return $this->renderError($e->getMessage());
        }
    }

    /**
     * @Route("/inventory", name="inventory", options={"expose"=true}, condition="request.isXmlHttpRequest()")
     * @Method({"GET"})
     */
    public function loadInventoryAction(Request $request)
    {
        if ($request->getSession()->has('deposit_id')) {
            $deposit = $this->get('app.deposit_service')->getDepositById($request->getSession()->get('deposit_id'));
        } else {
            return $this->json(['status' => self::STATUS_ERROR, ['message' => 'No deposit in session']]);
        }

        try {
            $inventory = $this->get('app.inventory_service')->getInventoryByTradeUrl($deposit, $request->getLocale());

            return $this->json(['status' => self::STATUS_SUCCESS, 'inventory' => $inventory]);
        } catch (SteamInventoryLoadException $e) {
            return $this->json(['status' => self::STATUS_ERROR, 'message' => $this->get('translator')->trans($e->getMessage())]);
        } catch (UnsupportedCurrencyException $e) {
            $this->get('logger')->critical('Unsupported Currency!', [$e]);

            return $this->json(['status' => self::STATUS_ERROR, 'message' => $this->get('translator')->trans('error_inventory')]);
        }
    }

    /**
     * @Route("/inventory/send", name="inventory_submit", options={"expose"=true})
     * @Method("POST")
     */
    public function inventorySubmitAction(Request $request)
    {
        if (!$this->isTokenValid($request)) {
            return new JsonResponse(
                [
                    'status' => self::STATUS_ERROR,
                    'message' => 'CSRF PROTECTION FU',
                ], 400
            );
        }

        $data = json_decode($request->getContent(), true);

        $deposit = $this->get('app.deposit_service')->getDepositById($data['deposit_id']);
        if ($deposit->getStatus() !== Deposit::STATUS_NEW) {
            return $this->json([
                'status' => self::STATUS_ERROR,
                'message' => $this->get('translator')->trans('deposit_is_already_handled'),
                'deposit' => $deposit,
                'server_time' => new \DateTime(),
                'success_url' => $deposit->getIntegration()->getSuccessUrl().'/?order_id='.$deposit->getOrderId(),
                'confirm_trade_offer_url' => SteamService::STEAM_TRADE_OFFER_URL.$deposit->getTradeOfferId(),
            ]);
        }

        try {

            $deposit = $this->get('app.deposit_service')->updateDeposit(
                $deposit,
                $data['items']
            );

            if ($this->get('app.steam_service')->sendTradeOffer($deposit->getItems(), $deposit)) {
                return $this->json([
                    'status' => self::STATUS_SUCCESS,
                    'deposit' => $deposit,
                    'server_time' => new \DateTime(),
                    'success_url' => $deposit->getIntegration()->getSuccessUrl().'/?order_id='.$deposit->getOrderId(),
                    'confirm_trade_offer_url' => SteamService::STEAM_TRADE_OFFER_URL.$deposit->getTradeOfferId(),
                ]);
            } else {
                return $this->json([
                    'status' => self::STATUS_ERROR,
                    'depositId' => $deposit->getId(),
                    'message' => $this->get('translator')->trans('send_offer_error'),
                ]);
            }
        } catch (UnacceptableItemSubmittedException $e) {
            $deposit->setStatus(Deposit::STATUS_ERROR_UNACCEPTABLE_ITEM);
            $this->get('doctrine.orm.default_entity_manager')->flush($deposit);

            return $this->json([
                'status' => self::STATUS_ERROR,
                'message' => $this->get('translator')->trans($e->getMessage()),
            ]);
        }
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @Route("/try-again", name="try_again")
     */
    public function tryAgainAction(Request $request)
    {
        if (!$request->getSession()->has('deposit_id')) {
            return $this->renderError('bad request');
        }

        $depositId = $request->getSession()->remove('deposit_id');
        /** @var Deposit $deposit */
        $deposit = $this->get('app.deposit_service')->getDepositById($depositId);

        if (in_array($deposit->getStatus(), [Deposit::STATUS_ERROR_BOT, Deposit::STATUS_ERROR_UNACCEPTABLE_ITEM])) {
            $deposit = $this->get('app.deposit_service')->newDeposit(
                $deposit->getIntegration(),
                $deposit->getOrderId(),
                $deposit->getTradeUrl(),
                $deposit->getCurrency()
            );
        }
        $this->get('session')->set('deposit_id', $deposit->getId());

        return $this->redirect($this->generateUrl('index', ['_locale' => $request->getLocale()]));
    }

    /**
     * @param $depositId
     *
     * @return JsonResponse
     *
     * @Route("/check-deposit/{depositId}", name="check_deposit", options={"expose"=true}, condition="request.isXmlHttpRequest()")
     */
    public function checkDepositStatusAction($depositId)
    {
        $result = $this->get('app.deposit_service')->getDepositStatus($depositId);
        $result['message'] = $this->get('translator')->trans($result['deposit_status']);

        return $this->json(
            $result
        );
    }

    /**
     * @param $errorMessage
     * @param $successUrl
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function renderError($errorMessage, $successUrl = null)
    {
        return $this->render('default/error.html.twig', [
            'error_message' => $errorMessage,
            'success_url' => $successUrl,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    private function isTokenValid(Request $request): bool
    {
        $tokenManager = $this->get('security.csrf.token_manager');
        $tokenId = $this->container->getParameter('crsf_inscription_inception');
        $tokenValue = $request->get('token');
        $token = new CsrfToken($tokenId, $tokenValue);

        return $tokenManager->isTokenValid($token);
    }
}
