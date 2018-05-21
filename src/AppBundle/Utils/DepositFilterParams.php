<?php
namespace AppBundle\Utils;

use AppBundle\Entity\Deposit;
use AppBundle\Entity\Integration;

class DepositFilterParams implements \JsonSerializable
{
    /**
     * @var \DateTime $dateFrom
     */
    private $dateFrom;

    /**
     * @var \DateTime $dateTo
     */
    private $dateTo;

    /**
     * @var  Integration $integration
     */
    private $integration;

    /**
     * @var int $integrationId
     */
    private $integrationId;

    /**
     * @var int $limit
     */
    private $limit;

    /**
     * @var int $offset
     */
    private $offset = 0;

    /**
     * @var array $statuses
     */
    private $statuses = [];

    /**
     * @var string $status
     */
    private $status = null;

    /**
     * @var int $price
     */
    private $price = null;

    /**
     * @var string $priceCriteria
     */
    private $priceCriteria = null;

    /**
     * @var int $countItems
     */
    private $countItems = null;

    /**
     * @var string $currency
     */
    private $currency;

    /**
     * @var string $skinType
     */
    private $skinType = null;

    /**
     * @var string $countItemsCriteria
     */
    private $countItemsCriteria = null;

    /**
     * @var int $pushStatus
     */
    private $pushStatus = null;

    public function setPushStatus(int $status) {
        $this->pushStatus = $status;
        return $this;
    }

    public function getPushStatus() {
        return $this->pushStatus;
    }

    public function setSkinType(string $type) {
        $this->skinType = $type;
        return $this;
    }

    public function getSkinType() {
        return $this->skinType;
    }

    public function setDateFrom(\DateTime $dateFrom) {
        $this->dateFrom = $dateFrom;
        return $this;
    }

    public function getDateFrom(){
        return $this->dateFrom;
    }

    public function setDateTo(\DateTime $dateTo) {
        $this->dateTo = $dateTo;
        return $this;
    }

    public function getDateTo(){
        return $this->dateTo;
    }

    public function setLimit($limit){
        $this->limit = $limit;
        return $this;
    }

    public function getLimit(){
        return $this->limit;
    }

    public function setOffset($offset){
        $this->offset = $offset;
        return $this;
    }

    public function getOffset(){
        return $this->offset;
    }

    public function setStatuses(array $statuses = []){
        $this->statuses = [];
        foreach ($statuses as $status) {
            if ($status == Deposit::GROUP_STATUS_SUCCESS) {
                array_push($this->statuses, Deposit::STATUS_COMPLETED);
            } elseif ($status == Deposit::GROUP_STATUS_WAITING) {
                array_push($this->statuses, Deposit::STATUS_NEW);
                array_push($this->statuses, Deposit::STATUS_PENDING);
                array_push($this->statuses, Deposit::STATUS_SENDING_TRADE);
            } elseif ($status == Deposit::GROUP_STATUS_FAILS) {
                array_push($this->statuses, Deposit::STATUS_ERROR_BOT);
                array_push($this->statuses, Deposit::STATUS_ERROR_INVENTORY_LOAD);
                array_push($this->statuses, Deposit::STATUS_ERROR_PUSHBACK);
                array_push($this->statuses, Deposit::STATUS_ERROR_UNACCEPTABLE_ITEM);
            }
        }
        return $this;
    }

    public function getStatuses(){
        return $this->statuses;
    }

    public function setStatus(string $status){
        $this->status = $status;
        return $this;
    }

    public function getStatus(){
        return $this->status;
    }

    public function setPrice($price){
        if (is_numeric($price)){
            $this->price = $price;
        } else {
            if (preg_match("/^([\!\<\>\=]{1,2})(\d+)$/", $price, $matches)) {
                $this->setPriceCriteria($matches[1]);
                $this->price = $matches[2];
            }
        }
        return $this;
    }

    public function getPrice(){
        return $this->price;
    }

    public function setPriceCriteria($criteria)
    {
        $this->priceCriteria = $this->getCriteria($criteria);
    }

    public function getPriceCriteria(){
        return $this->priceCriteria;
    }

    public function setCountItems($countItems){
        if (is_numeric($countItems)) {
            $this->countItems = $countItems;
        } elseif (preg_match("/^([\!\<\>\=]{1,2})(\d+)$/", $countItems, $matches)) {
            $this->countItemsCriteria = $this->getCriteria($matches[1]);
            $this->countItems = $matches[2];
        }
        return $this;
    }

    public function getCountItems(){
        return $this->countItems;
    }

    public function getCountItemsCriteria() {
        return $this->countItemsCriteria;
    }

    public function setIntegration(Integration $integration){
        $this->integration = $integration;
        return $this;
    }

    public function getIntegration(){
        return $this->integration;
    }

    public function setCurrency(string $currency){
        $this->currency = $currency;
    }

    public function getCurrency(){
        return $this->currency;
    }

    public function setIntegrationId(int $id){
        $this->integrationId = $id;
    }

    public function getIntegrationId(){
        return $this->integrationId;
    }

    private function getCriteria($criteria)
    {
        if ($criteria == '='){
            return 'eq';
        } elseif ($criteria == '<'){
            return 'lt';
        } elseif ($criteria == '<='){
            return 'lte';
        } elseif ($criteria == '>'){
            return 'gt';
        } elseif ($criteria == '>='){
            return 'gte';
        } elseif ($criteria == '!='){
            return 'neq';
        }
    }

    public function jsonSerialize()
    {
        return [
            'integrationId' => $this->integrationId,
            'date_from' => $this->dateFrom instanceof \DateTime ? $this->dateFrom->format('Y-m-d') : null,
            'date_to' => $this->dateTo instanceof \DateTime ? $this->dateTo->format('Y-m-d') : null,
            'currency' => $this->currency,
            'offset' => $this->offset,
            'limit' => $this->limit,
            'status' => $this->status,
            'push_status' => $this->pushStatus,
        ];
    }


}