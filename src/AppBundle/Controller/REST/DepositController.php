<?php
/**
 * Created by PhpStorm.
 * User: ahimas
 * Date: 18.01.17
 * Time: 16:50.
 */

namespace AppBundle\Controller\REST;

use AppBundle\Entity\Deposit;
use AppBundle\Entity\Integration;
use AppBundle\Entity\IntegrationReports;
use AppBundle\Entity\User;
use AppBundle\Utils\DateTimeFilterTrait;
use AppBundle\Utils\DepositFilterParams;
use AppBundle\Utils\PaginationTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DepositController.
 *
 * @Route("/dashboard/api/v1/deposit")
 */
class DepositController extends Controller
{
    use DateTimeFilterTrait;
    use PaginationTrait;

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Method("GET")
     * @Route("/list", name="get_deposits")
     */
    public function getDepositsAction(Request $request): JsonResponse
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $depositFilterParams = new DepositFilterParams();
        $this->getFilterFromRequest($request);
        $this->getLimitAndOffsetFromRequest($request);
        $orderId = $request->query->get('order_id');
        $statuses = $request->query->get('activeStatuses', []);
        $price = $request->query->get('price');
        $countItems = $request->query->get('countItems');

        $depositFilterParams
            ->setDateTo($this->dateTo)
            ->setDateFrom($this->dateFrom)
            ->setOffset($this->offset)
            ->setLimit($this->limit)
            ->setStatuses($statuses)
            ->setPrice($price)
            ->setCountItems($countItems)
            ->setIntegration($user->getIntegration());

        if ($orderId !== null) {
            $result = $this->get('doctrine.orm.entity_manager')
                ->getRepository(Deposit::class)
                ->findBy(['integration' => $user->getIntegration(), 'orderId' => $orderId])
            ;
            $count = count($result);
        } else {
            $result = $this->get('app.stats_service')->getDeposits($depositFilterParams);
            $count = $this->get('app.stats_service')->getCount($depositFilterParams);
        }

        return new JsonResponse(['status' => 'success', 'result' => ['items' => $result, 'count' => $count]]);
    }

    /**
     * @param $id
     *
     * @return JsonResponse
     * @Method("GET")
     * @Route("/{id}", name="get_deposit", requirements={"id": "\d+"})
     */
    public function getDepositByIdAction($id): JsonResponse
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $deposit = $this->get('app.deposit_service')->getDepositByIntegrationAndId($user->getIntegration(), $id);

        return new JsonResponse(['status' => 'success', 'result' => $deposit]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Method("GET")
     * @Route("/create_report", name="create_report")
     */
    public function createReportAction(Request $request): JsonResponse
    {
        try {
            /**
             * @var User
             */
            $user = $this->get('security.token_storage')->getToken()->getUser();

            $this->getFilterFromRequest($request);
            /**
             * @var Integration $integration
             */
            $integration = $user->getIntegration();

            $depositFilterParams = new DepositFilterParams();
            $depositFilterParams
                ->setDateFrom($this->dateFrom)
                ->setDateTo($this->dateTo)
                ->setIntegrationId($integration->getId());
            if ($request->query->get('status') !== 'all') {
                $depositFilterParams->setStatus($request->query->get('status'));
            }

            $this->get('old_sound_rabbit_mq.depositstatistics_producer')->publish(serialize($depositFilterParams));

            return new JsonResponse([
                'status' => 'success',
                'message' => 'create report success',
            ]);
        } catch (\Exception $exception) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * @param Request $request
     * @Method("GET")
     * @Route("/reports", name="getreports")
     */
    public function reportsAction(Request $request)
    {
        try {
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $integrationReportsService = $this->get('app.integration_reports_service');
            $reports = $integrationReportsService->getByUser($user);

            return new JsonResponse([
                'status' => 'success',
                'result' => [
                    'items' => $reports,
                    'count' => count($reports),
                ],
            ]);
        } catch (\Exception $exception) {
            $this->get('logger')->crit('reports action', [$exception->getMessage()]);

            return new JsonResponse(['status' => 'error', 'message' => $exception->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @Method("GET")
     * @Route("/getfile", name="deposit_getfile")
     */
    public function getFileAction(Request $request)
    {
        try {
            /**
             * @var User
             */
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $filename = filter_var($request->get('file', null), FILTER_SANITIZE_STRING);
            /**
             * @var Integration
             */
            $integration = $user->getIntegration();

            /**
             * @var IntegrationReports
             */
            $report = $this->get('doctrine.orm.entity_manager')->getRepository(IntegrationReports::class)->findOneBy([
                'file' => $filename,
            ]);

            if (!$report instanceof IntegrationReports) {
                throw new \Exception('report not found');
            }

            if ($report->getUser()->getId() !== $user->getId()) {
                throw new \Exception('error access');
            }

            $path = $this->getParameter('reports_path').$report->getFile();
            $content = file_get_contents($path);
            $response = new Response();
            $response->headers->set('Content-Disposition', 'attachment;filename='.$report->getFile());
            $response->setContent($content);

            return $response;
        } catch (\Exception $exception) {
            return new JsonResponse(['status' => 'error', 'message' => $exception->getMessage()]);
        }
    }
}
