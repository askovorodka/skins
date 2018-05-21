<?php
/**
 * Created by PhpStorm.
 * User: ahimas
 * Date: 23.01.17
 * Time: 12:19.
 */

namespace AppBundle\Controller\REST;

use AppBundle\Entity\IntegrationDebit;
use AppBundle\Entity\User;
use AppBundle\Exception\NotEnoughBalanceException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DashboardController.
 *
 * @Route("/dashboard/api/v1")
 */
class DashboardController extends Controller
{
    /**
     * @return JsonResponse
     * @Method("GET")
     * @Route("/header", name="api_get_header")
     */
    public function headerAction()
    {
        /** @var User $user */
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $integrationBalance = $this->get('app.integration_service')->getIntegrationBalance($user->getIntegration());
        $result = [
            'id' => $user->getId(),
            'integration_name' => $user->getIntegration()->getName(),
            'balance' => $integrationBalance,
            'email' => $user->getEmail(),
        ];

        return new JsonResponse(['status' => 'success', 'result' => $result]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Method("POST")
     * @Route("/debit/request", name="api_get_debit")
     */
    public function getIntegrationDebitAction(Request $request)
    {
        /** @var User $user */
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $requestContent = json_decode($request->getContent(), true);

        $amount = $requestContent['amount'];
        $currency = $requestContent['currency'];
        $note = $requestContent['note'];
        $payment_system = $requestContent['payment_system'] ?? null;
        $payment_destination = $requestContent['payment_destination'] ?? null;

        if (!$amount || !$currency || !$note) {
            return new JsonResponse(['status' => 'fail', 'message' => 'amount, currency and note required']);
        }

        if (!empty($payment_system) && !in_array($payment_system, [
                IntegrationDebit::MONEY_TYPE_WEBMONEY,
                IntegrationDebit::MONEY_TYPE_YANDEX,
                IntegrationDebit::MONEY_TYPE_QIWI,])) {
            return new JsonResponse(['status' => 'fail', 'message' => 'payment system invalidate']);
        }

        if (!empty($payment_system) && !empty($payment_destination)) {
            if ($payment_system == IntegrationDebit::MONEY_TYPE_WEBMONEY) {
                if (!preg_match('/^([A-Z]{1})([0-9]{12})$/i', $payment_destination)) {
                    return new JsonResponse(['status' => 'fail', 'message' => 'payment destination invalidate']);
                }
            } elseif ($payment_system == IntegrationDebit::MONEY_TYPE_YANDEX) {
                if (!preg_match('/^([0-9]{14})$/', $payment_destination)) {
                    return new JsonResponse(['status' => 'fail', 'message' => 'payment destination invalidate']);
                }
            } elseif ($payment_system == IntegrationDebit::MONEY_TYPE_QIWI) {
                if (!preg_match("/^([0-9]{10,}$)/is", $payment_destination)) {
                    return new JsonResponse(['status' => 'fail', 'message' => 'payment destination invalidate']);
                }
            }
        }

        try {
            $debit = $this->get('app.debit_service')->createIntegrationDebit($user->getIntegration(), $amount, $currency, $note, $payment_system, $payment_destination);
            $message = "new debit requested! Integration {$debit->getIntegration()->getName()}, amount = {$debit->getAmount()}";
            $this->get('app.notification_service')->notifyByEmail(
                $this->getParameter('email.debit.subject'),
                $this->getParameter('email.debit.from'),
                $this->getParameter('email.debit.to'),
                $message
            );

            return new JsonResponse(['status' => 'success', 'message' => 'debit request created']);
        } catch (NotEnoughBalanceException $e) {
            return new JsonResponse(['status' => 'fail', 'message' => $e->getMessage()]);
        }
    }

    /**
     * @return JsonResponse
     * @Method("GET")
     * @Route("/debit/list", name="api_get_debit_list")
     */
    public function getIntegrationDebitListAction()
    {
        /** @var User $user */
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $list = $this->get('doctrine.orm.default_entity_manager')->getRepository(IntegrationDebit::class)->getDebitsListByIntegration($user->getIntegration());

        return $this->json(['status' => 'success', 'result' => $list]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Method("POST")
     * @Route("/send-feedback", name="api_send_feedback")
     */
    public function sendFeedBackAction(Request $request): JsonResponse
    {
        $requestContent = json_decode($request->getContent(), true);
        if (!is_string($requestContent['message'])) {
            return $this->json(['status' => 'fail', 'message' => 'need feedback text']);
        }
        $this->get('app.notification_service')->notifyByEmail(
            $this->getParameter('email.feedback.subject'),
            $this->getParameter('email.feedback.from'),
            $this->getParameter('email.feedback.to'),
            $requestContent['message']);

        return $this->json(['status' => 'success']);
    }
}
