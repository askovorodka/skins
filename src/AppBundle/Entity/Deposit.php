<?php

namespace AppBundle\Entity;

use AppBundle\DTO\InventoryItem;
use Doctrine\ORM\Mapping as ORM;

/**
 * Deposit.
 *
 * @ORM\Table(
 *    name="deposit",
 *    indexes={
 *      @ORM\Index(name="created", columns={"created"}),
 *      @ORM\Index(name="integration_id_order_id_status", columns={"integration_id", "order_id", "status"}),
 *      @ORM\Index(name="status_push_status", columns={"status", "push_status"}),
 *    }
 * )
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DepositRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Deposit implements \JsonSerializable
{
    const CURRENCY_RUB = 'RUB';
    const CURRENCY_USD = 'USD';

    const STATUS_NEW = 'new';
    const STATUS_PENDING = 'pending';
    const STATUS_SENDING_TRADE = 'sending_trade';
    const STATUS_ERROR_INVENTORY_LOAD = 'error_inventory';
    const STATUS_ERROR_BOT = 'error_bot';
    const STATUS_ERROR_PUSHBACK = 'error_pushback';
    const STATUS_ERROR_UNACCEPTABLE_ITEM = 'error_unacceptable_item';
    const STATUS_COMPLETED = 'completed';
    const STATUS_TERMINAL =  'terminal';

    const GROUP_STATUS_SUCCESS  = 1;
    const GROUP_STATUS_WAITING  = 2;
    const GROUP_STATUS_FAILS    = 3;

    const MAXIMUM_VALUE_LIMIT   = 680;//USD

    const ITEMS_PRICE_DOTA_KEY  = 'dota';
    const ITEMS_PRICE_CSGO_KEY  = 'csgo';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime()
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime()
     *
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;

    /**
     * @var int
     *
     * @ORM\Column(name="steam_id", type="string")
     */
    private $steamId;

    /**
     * @var int
     *
     * @ORM\Column(name="order_id", type="integer")
     */
    private $orderId;

    /**
     * @var string
     *
     * @ORM\Column(name="trade_hash", type="string")
     */
    private $tradeHash = '';

    /**
     * @ORM\Column(name="no_tax_value", type="decimal", precision=10, scale=3)
     */
    private $noTaxValue = 0;

    /**
     * @ORM\Column(name="value", type="decimal", precision=10, scale=3)
     */
    private $value = 0;

    /**
     * @ORM\Column(name="value_dota", type="decimal", precision=10, scale=3, options={"comment":"deposit value of dota part"})
     */
    private $valueDota = 0;

    /**
     * @ORM\Column(name="value_csgo", type="decimal", precision=10, scale=3, options={"comment":"deposit value of csgo part"})
     */
    private $valueCsgo = 0;

    /**
     * @ORM\Column(name="no_tax_value_dota", type="decimal", precision=10, scale=3, options={"comment":"deposit np_tax_value of dota part"})
     */
    private $noTaxValueDota = 0;

    /**
     * @ORM\Column(name="no_tax_value_csgo", type="decimal", precision=10, scale=3, options={"comment":"deposit no_tax_value of csgo part"})
     */
    private $noTaxValueCsgo = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string")
     */
    private $status = self::STATUS_NEW;

    /**
     * @var int
     *
     * @ORM\Column(name="push_status", type="integer", nullable=true)
     */
    private $pushStatus;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="pushback_created", type="datetime", nullable=true)
     */
    private $pushbackCreated;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string")
     */
    private $currency = self::CURRENCY_RUB;

    /**
     * @var Integration
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Integration")
     */
    private $integration;

    /**
     * @var int
     *
     * @ORM\Column(name="trade_offer_id", type="bigint", nullable=true)
     */
    private $tradeOfferId;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="string", nullable=true)
     */
    private $note;

    /**
     * @ORM\Column(name="items", type="json_array", nullable=true)
     */
    private $items;

    /**
     * @ORM\Column(name="trade_url", type="string", nullable=true)
     *
     * @var string
     */
    private $tradeUrl;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return Deposit
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return Deposit
     *
     * @ ORM\PrePersist()
     */
    public function setCreated(\DateTime $date = null)
    {
        if (!$date instanceof \DateTime) {
            $date = new \DateTime();
        }
        $this->created = $date;

        return $this;
    }

    /**
     * @return int
     */
    public function getSteamId()
    {
        return $this->steamId;
    }

    /**
     * @param int $steamId
     *
     * @return Deposit
     */
    public function setSteamId($steamId)
    {
        $this->steamId = $steamId;

        return $this;
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param int $orderId
     *
     * @return Deposit
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * @return string
     */
    public function getTradeHash()
    {
        return $this->tradeHash;
    }

    /**
     * @param string $tradeHash
     *
     * @return Deposit
     */
    public function setTradeHash($tradeHash)
    {
        $this->tradeHash = $tradeHash;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getNoTaxValue()
    {
        return $this->noTaxValue;
    }

    /**
     * @param mixed $noTaxValue
     *
     * @return Deposit
     */
    public function setNoTaxValue($noTaxValue)
    {
        $this->noTaxValue = $noTaxValue;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     *
     * @return Deposit
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return Deposit
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return int
     */
    public function getPushStatus()
    {
        return $this->pushStatus;
    }

    /**
     * @param int $pushStatus
     *
     * @return Deposit
     */
    public function setPushStatus($pushStatus)
    {
        $this->pushStatus = $pushStatus;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPushbackCreated()
    {
        return $this->pushbackCreated;
    }

    /**
     * @param \DateTime $pushbackCreated
     *
     * @return Deposit
     */
    public function setPushbackCreated($pushbackCreated)
    {
        $this->pushbackCreated = $pushbackCreated;

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     *
     * @return Deposit
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return Integration
     */
    public function getIntegration()
    {
        return $this->integration;
    }

    /**
     * @param Integration $integration
     *
     * @return Deposit
     */
    public function setIntegration($integration)
    {
        $this->integration = $integration;

        return $this;
    }

    /**
     * @return int
     */
    public function getTradeOfferId()
    {
        return $this->tradeOfferId;
    }

    /**
     * @param int $tradeOfferId
     *
     * @return Deposit
     */
    public function setTradeOfferId($tradeOfferId)
    {
        $this->tradeOfferId = $tradeOfferId;

        return $this;
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param string $note
     *
     * @return Deposit
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * @return InventoryItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param mixed $items
     *
     * @return $this
     */
    public function setItems($items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * @return string
     */
    public function getTradeUrl()
    {
        return $this->tradeUrl;
    }

    /**
     * @param string $tradeUrl
     *
     * @return Deposit
     */
    public function setTradeUrl($tradeUrl)
    {
        $this->tradeUrl = $tradeUrl;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @return Deposit
     *
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function setUpdated()
    {
        $this->updated = new \DateTime();

        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'currency' => $this->currency,
            'status' => $this->status,
            'trade_hash' => $this->tradeHash,
            'value' => $this->value,
            'order_id' => $this->orderId,
            'push_status' => $this->pushStatus,
            'push_created' => $this->pushbackCreated,
            'created' => $this->created,
            'trade_url' => $this->tradeUrl,
            'steam_id' => $this->steamId,
            'note' => $this->note,
            'trade_offer_id' => $this->tradeOfferId,
            'items' => $this->items,
        ];
    }

    /**
     * Set valueDota
     *
     * @param string $valueDota
     *
     * @return Deposit
     */
    public function setValueDota($valueDota)
    {
        $this->valueDota = $valueDota;

        return $this;
    }

    /**
     * Get valueDota
     *
     * @return string
     */
    public function getValueDota()
    {
        return $this->valueDota;
    }

    /**
     * Set valueCsgo
     *
     * @param string $valueCsgo
     *
     * @return Deposit
     */
    public function setValueCsgo($valueCsgo)
    {
        $this->valueCsgo = $valueCsgo;

        return $this;
    }

    /**
     * Get valueCsgo
     *
     * @return string
     */
    public function getValueCsgo()
    {
        return $this->valueCsgo;
    }

    /**
     * Set noTaxValueDota
     *
     * @param string $noTaxValueDota
     *
     * @return Deposit
     */
    public function setNoTaxValueDota($noTaxValueDota)
    {
        $this->noTaxValueDota = $noTaxValueDota;

        return $this;
    }

    /**
     * Get noTaxValueDota
     *
     * @return string
     */
    public function getNoTaxValueDota()
    {
        return $this->noTaxValueDota;
    }

    /**
     * Set noTaxValueCsgo
     *
     * @param string $noTaxValueCsgo
     *
     * @return Deposit
     */
    public function setNoTaxValueCsgo($noTaxValueCsgo)
    {
        $this->noTaxValueCsgo = $noTaxValueCsgo;

        return $this;
    }

    /**
     * Get noTaxValueCsgo
     *
     * @return string
     */
    public function getNoTaxValueCsgo()
    {
        return $this->noTaxValueCsgo;
    }
}
