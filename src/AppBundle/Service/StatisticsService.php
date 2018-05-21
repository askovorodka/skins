<?php

namespace AppBundle\Service;

use AppBundle\Entity\Deposit;
use AppBundle\Entity\Integration;
use AppBundle\Utils\DepositFilterParams;
use Doctrine\ORM\EntityManager;

class StatisticsService
{
    const GROUP_BY_DAY = 'day';
    const GROUP_BY_MONTH = 'month';
    const GROUP_BY_YEAR = 'year';
    const GROUP_BY_HOUR = 'hour';
    const TYPE_FILE = 'file';

    /**
     * @var EntityManager
     */
    private $entityManager;

    private $depositRepository;

    private $dateFrom;
    private $dateTo;
    private $dateFormat = "'%Y-%m-%d'";

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->depositRepository = $this->entityManager->getRepository(Deposit::class);
    }

    /**
     * @param null   $dateFrom
     * @param null   $dateTo
     * @param string $groupBy
     *
     * @return \Doctrine\ORM\Query
     */
    public function getStats($dateFrom = null, $dateTo = null, $groupBy = null, Integration $integration = null, $currency = null)
    {
        $this->getFilter($dateFrom, $dateTo, $groupBy);

        return $this->depositRepository->getStatsQuery($this->dateFrom, $this->dateTo, $this->dateFormat, $integration, $currency);
    }

    /**
     * @param $integration
     * @param $dateFrom
     * @param $dateTo
     * @param $limit
     * @param $offset
     *
     * @return array
     */
    public function getDeposits(DepositFilterParams $params)
    {
        return $this->depositRepository->getDepositsByIntegration($params);
    }

    /**
     * @param $dateFrom
     * @param $dateTo
     * @param $groupBy
     */
    private function getFilter($dateFrom = null, $dateTo = null, $groupBy = self::GROUP_BY_DAY)
    {
        if (!$dateFrom) {
            $this->dateFrom = new \DateTime('-1 month');
        } else {
            $this->dateFrom = $dateFrom;
        }

        if (!$dateTo) {
            $this->dateTo = new \DateTime();
        } else {
            $this->dateTo = $dateTo;
        }

        $this->dateFrom->setTime(0, 0, 0);

        switch ($groupBy) {
            case self::GROUP_BY_DAY:
                $this->dateFormat = "'%Y-%m-%d'";
                break;
            case self::GROUP_BY_MONTH:
                $this->dateFormat = "'%Y-%m'";
                break;
            case self::GROUP_BY_YEAR:
                $this->dateFormat = "'%Y'";
                break;
            case self::GROUP_BY_HOUR:
                $this->dateFormat = "'%Y-%m-%d %H'";
                break;
            default:
                $this->dateFormat = "'%Y-%m-%d'";
                break;
        }
    }

    /**
     * method return count deposits by parameters.
     *
     * @param Integration $integration
     * @param $dateFrom
     * @param $dateTo
     * @param null $status
     *
     * @return mixed
     */
    public function getCount(DepositFilterParams $params)
    {
        return $this->depositRepository->getCount($params);
    }

    public function getStatisticsAggregatedByCurrency(DepositFilterParams $params)
    {
        $params->setStatuses([Deposit::GROUP_STATUS_SUCCESS]);
        $result = $this->depositRepository->getStatisticsAggregatedByCurrency($params);
        /**
         * считаем кол-во по статусам и валюте
         */
         $params->setCurrency(Deposit::CURRENCY_RUB);
         $result['BY_STATUSES'][Deposit::CURRENCY_RUB]['all_count'] = $this->depositRepository->getCount($params->setStatuses([]));
         $result['BY_STATUSES'][Deposit::CURRENCY_RUB]['success_count'] = $this->depositRepository->getCount($params->setStatuses([Deposit::GROUP_STATUS_SUCCESS]));
         $result['BY_STATUSES'][Deposit::CURRENCY_RUB]['wait_count'] = $this->depositRepository->getCount($params->setStatuses([Deposit::GROUP_STATUS_WAITING]));
         $result['BY_STATUSES'][Deposit::CURRENCY_RUB]['fail_count'] = $this->depositRepository->getCount($params->setStatuses([Deposit::GROUP_STATUS_FAILS]));

         $params->setCurrency(Deposit::CURRENCY_USD);
         $result['BY_STATUSES'][Deposit::CURRENCY_USD]['all_count'] = $this->depositRepository->getCount($params->setStatuses([]));
         $result['BY_STATUSES'][Deposit::CURRENCY_USD]['success_count'] = $this->depositRepository->getCount($params->setStatuses([Deposit::GROUP_STATUS_SUCCESS]));
         $result['BY_STATUSES'][Deposit::CURRENCY_USD]['wait_count'] = $this->depositRepository->getCount($params->setStatuses([Deposit::GROUP_STATUS_WAITING]));
         $result['BY_STATUSES'][Deposit::CURRENCY_USD]['fail_count'] = $this->depositRepository->getCount($params->setStatuses([Deposit::GROUP_STATUS_FAILS]));

        /**
         * calculate by skin type (csgo,dota)
         */
        $params->setStatuses([Deposit::GROUP_STATUS_SUCCESS]);
        $result[Deposit::ITEMS_PRICE_CSGO_KEY] = $this->depositRepository->getStatisticsBySkinTypeAggregatedByCurrency($params->setSkinType(Deposit::ITEMS_PRICE_CSGO_KEY));
        $result[Deposit::ITEMS_PRICE_DOTA_KEY] = $this->depositRepository->getStatisticsBySkinTypeAggregatedByCurrency($params->setSkinType(Deposit::ITEMS_PRICE_DOTA_KEY));

         return $result;
    }

}
