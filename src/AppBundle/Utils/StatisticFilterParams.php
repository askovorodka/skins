<?php

namespace AppBundle\Utils;

use AppBundle\Entity\Deposit;
use AppBundle\Entity\Integration;

class StatisticFilterParams implements \JsonSerializable
{
    const FILTER_ACTION_BY_CURRENCY_AND_DATE = 'getStatisticsAggregatedByCurrencyAndDate';

    /**
     * @var \DateTime
     */
    private $dateFrom;
    /**
     * @var \DateTime
     */
    private $dateTo;
    private $integration;
    private $integrationId;
    private $currency;
    private $limit;
    private $offset;
    private $groupBy;
    private $action;
    private $dateFormat;
    private $status = null;

    public function setIntegrationId(int $id)
    {
        $this->integrationId = $id;

        return $this;
    }

    public function getIntegrationId()
    {
        return $this->integrationId;
    }

    public function setDateFrom(\DateTime $dateFrom)
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    public function getDateFrom()
    {
        return $this->dateFrom;
    }

    public function setDateTo(\DateTime $dateTo)
    {
        $this->dateTo = $dateTo;
        $this->dateTo->setTime(23, 59, 59);

        return $this;
    }

    public function getDateTo()
    {
        return $this->dateTo;
    }

    public function setAction(string $action)
    {
        $this->action = $action;

        return $this;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function setIntegration(Integration $integration)
    {
        $this->integration = $integration;

        return $this;
    }

    public function getIntegration()
    {
        return $this->integration;
    }

    public function setDateFormat(string $dateFormat)
    {
        $this->dateFormat = $dateFormat;

        return $this;
    }

    public function getDateFormat()
    {
        return $this->dateFormat;
    }

    public function setCurrency(string $currency)
    {
        $this->currency = $currency;

        return $this;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setLimit(int $limit)
    {
        $this->limit = $limit;

        return $this;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function setGroupBy(string $groupBy)
    {
        $this->groupBy = $groupBy;

        return $this;
    }

    public function getGroupBy()
    {
        return $this->groupBy;
    }

    public function setOffset(int $offset)
    {
        $this->offset = $offset;

        return $this;
    }

    public function getOffset()
    {
        return $this->offset;
    }

    public function setStatus(string $status)
    {
        $this->status = $status;

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function jsonSerialize()
    {
        return [
            'integrationId' => $this->integrationId,
            'date_from' => $this->dateFrom->format('Y-m-d'),
            'date_to' => $this->dateTo->format('Y-m-d'),
            'date_format' => $this->dateFormat,
            'currency' => $this->currency,
            'offset' => $this->offset,
            'limit' => $this->limit,
            'status' => $this->status,
        ];
    }
}
