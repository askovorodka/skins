<?php

namespace AppBundle\Controller\REST;

use AppBundle\Entity\Deposit;
use AppBundle\Entity\User;
use AppBundle\Service\StatisticsService;
use AppBundle\Utils\DateTimeFilterTrait;
use AppBundle\Utils\DepositFilterParams;
use AppBundle\Utils\PaginationTrait;
use AppBundle\Utils\StatisticFilterParams;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DashboardController.
 *
 * @Route("/dashboard/api/v1/statistics")
 */
class StatisticsController extends Controller
{
    use DateTimeFilterTrait;
    use PaginationTrait;

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Method("GET")
     * @Route("/by-currency", name="get_statistics_grouped_by_currency")
     */
    public function getStatisticsAggregatedAction(Request $request)
    {
        /** @var User $user */
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $integration = $user->getIntegration();
        $this->getFilterFromRequest($request);
        $filterParams = new DepositFilterParams();
        $filterParams
            ->setDateFrom($this->dateFrom)
            ->setDateTo($this->dateTo)
            ->setIntegration($integration);

        $service = $this->get('app.stats_service');
        $result = $service->getStatisticsAggregatedByCurrency($filterParams);

        return new JsonResponse(['status' => 'success', 'result' => $result]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Method("GET")
     * @Route("/by-currency-and-date", name="get_statistics_grouped_by_currency_and_date")
     */
    public function getStatisticsAction(Request $request)
    {
        /** @var User $user */
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $integration = $user->getIntegration();
        $this->getFilterFromRequest($request);
        $this->getLimitAndOffsetFromRequest($request);

        $groupBy = $request->query->get('group_by', StatisticsService::GROUP_BY_DAY);
        $currency = $request->query->get('currency', Deposit::CURRENCY_RUB);
        switch ($groupBy) {
            case StatisticsService::GROUP_BY_DAY:
                $dateFormat = "'%Y-%m-%d'";
                break;
            case StatisticsService::GROUP_BY_MONTH:
                $dateFormat = "'%Y-%m'";
                break;
            case StatisticsService::GROUP_BY_YEAR:
                $dateFormat = "'%Y'";
                break;
            case StatisticsService::GROUP_BY_HOUR:
                $dateFormat = "'%Y-%m-%d %H'";
                break;
            default:
                $dateFormat = "'%Y-%m-%d'";
                break;
        }

        $result = $this->get('doctrine.orm.entity_manager')->getRepository(Deposit::class)->getStatisticsAggregatedByCurrencyAndDate($integration, $currency, $this->dateFrom, $this->dateTo, $dateFormat, $this->limit, $this->offset);

        return new JsonResponse(['status' => 'success', 'result' => $result]);
    }
}
