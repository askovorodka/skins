<?php

/**
 * Created by PhpStorm.
 * User: ahimas
 * Date: 14.09.16
 * Time: 19:20
 */

namespace AppBundle\DTO;

class InventoryItem implements \JsonSerializable
{
    const ICON_BASE_URL = 'https://steamcommunity-a.akamaihd.net/economy/image/';
    const APPID_CSGO = 730;
    const APPID_DOTA2 = 570;

    private $id;

    private $classId;

    private $instanceId;

    private $iconUrl;

    private $iconLargeUrl;

    /**
     * @var string
     */
    private $marketName;

    private $name;

    private $marketHashName;

    private $tradable;

    private $marketable;

    private $htmlDescription;

    private $value;

    private $valueRaw;

    private $noTaxValue;

    private $noTaxValueRaw;

    private $rarity;

    private $color;

    private $acceptable = true;

    private $appId;

    private $rateValue;

    private $origPrice;

    private $comission;

    private $count = 0;

    private $stockCount = 0;


    /**
     * fron bullshit (show item on left side)
     */
    private $type = 1;

    public function __construct($item)
    {
        $this
            ->setId($item['id'] ?? null)
            ->setClassId($item['classid'] ?? null)
            ->setInstanceId($item['instanceid'] ?? null)
            ->setIconLargeUrl($item['icon_url_large'] ?? null)
            ->setIconUrl($item['icon_url'] ?? null)
            ->setMarketName($item['market_name'] ?? null)
            ->setName($item['name'] ?? null)
            ->setMarketHashName($item['market_hash_name'] ?? null)
            ->setTradable($item['tradable'] ?? null)
            ->setMarketable($item['marketable'] ?? null)
            ->setAppId($item['appid'] ?? $item['app_id'] ?? null )
            ->setOrigPrice($item['orig_price'] ?? null)
            ->setRateValue($item['rate_value'] ?? null)
            ->parseTags($item['tags'] ?? []);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return InventoryItem
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getClassId()
    {
        return $this->classId;
    }

    /**
     * @param mixed $classId
     * @return InventoryItem
     */
    public function setClassId($classId)
    {
        $this->classId = $classId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInstanceId()
    {
        return $this->instanceId;
    }

    /**
     * @param mixed $instanceId
     * @return InventoryItem
     */
    public function setInstanceId($instanceId)
    {
        $this->instanceId = $instanceId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIconUrl()
    {
        return $this->iconUrl;
    }

    /**
     * @param mixed $iconUrl
     * @return InventoryItem
     */
    public function setIconUrl($iconUrl)
    {
        $this->iconUrl = $iconUrl;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIconLargeUrl()
    {
        return $this->iconLargeUrl;
    }

    /**
     * @param mixed $iconLargeUrl
     * @return InventoryItem
     */
    public function setIconLargeUrl($iconLargeUrl)
    {
        $this->iconLargeUrl = $iconLargeUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getMarketName()
    {
        return $this->marketName;
    }

    /**
     * @param string $marketName
     * @return InventoryItem
     */
    public function setMarketName($marketName)
    {
        $this->marketName = $marketName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return InventoryItem
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMarketHashName()
    {
        return $this->marketHashName;
    }

    /**
     * @param mixed $marketHashName
     * @return InventoryItem
     */
    public function setMarketHashName($marketHashName)
    {
        $this->marketHashName = $marketHashName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTradable()
    {
        return $this->tradable;
    }

    /**
     * @param mixed $tradable
     * @return InventoryItem
     */
    public function setTradable($tradable)
    {
        $this->tradable = $tradable;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMarketable()
    {
        return $this->marketable;
    }

    /**
     * @param mixed $marketable
     * @return InventoryItem
     */
    public function setMarketable($marketable)
    {
        $this->marketable = $marketable;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHtmlDescription()
    {
        return $this->htmlDescription;
    }

    /**
     * @param mixed $htmlDescription
     * @return InventoryItem
     */
    public function setHtmlDescription($htmlDescription)
    {
        $this->htmlDescription = $htmlDescription;
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
     * @return InventoryItem
     */
    public function setValue($value)
    {
        $this->value = $value;
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
     * @return InventoryItem
     */
    public function setNoTaxValue($noTaxValue)
    {
        $this->noTaxValue = $noTaxValue;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRarity()
    {
        return $this->rarity;
    }

    /**
     * @param mixed $rarity
     * @return InventoryItem
     */
    public function setRarity($rarity)
    {
        $this->rarity = $rarity;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param mixed $color
     * @return InventoryItem
     */
    public function setColor($color)
    {
        $this->color = $color;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAcceptable()
    {
        return $this->acceptable;
    }

    /**
     * @param mixed $acceptable
     * @return $this
     */
    public function setAcceptable($acceptable)
    {
        $this->acceptable = $acceptable;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValueRaw()
    {
        return $this->valueRaw;
    }

    /**
     * @param mixed $valueRaw
     * @return InventoryItem
     */
    public function setValueRaw($valueRaw)
    {
        $this->valueRaw = $valueRaw;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return InventoryItem
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNoTaxValueRaw()
    {
        return $this->noTaxValueRaw;
    }

    /**
     * @param mixed $noTaxValueRaw
     * @return InventoryItem
     */
    public function setNoTaxValueRaw($noTaxValueRaw)
    {
        $this->noTaxValueRaw = $noTaxValueRaw;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * @param mixed $appId
     * @return InventoryItem
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;
        return $this;
    }

    public function setRateValue($rateValue)
    {
        $this->rateValue = (float) $rateValue;
        return $this;
    }

    public function setOrigPrice($origPrice)
    {
        $this->origPrice = (float) $origPrice;
        return $this;
    }

    public function setComission($comission)
    {
        $this->comission = (float) $comission;
        return $this;
    }

    public function setCount(int $value) {
        $this->count = $value;
        return $this;
    }

    public function getCount(): int {
        return $this->count;
    }

    public function setStockCount(int $value) {
        $this->stockCount = $value;
    }

    public function getStockCount(): int {
        return $this->stockCount;
    }

    /**
     * @param array $tags
     */
    public function parseTags(array $tags)
    {
        foreach ($tags as $tag) {
            if ($tag['category'] == 'Rarity') {
                $this->setRarity($tag['name'] ?? '');
                $this->setColor($tag['color'] ?? '');
            }
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->marketName;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'market_name' => $this->marketName,
            'market_hash_name' => $this->marketHashName,
            'price' => $this->value,
            'price_raw' => $this->valueRaw,
            'icon_url' => $this->iconUrl,
            'color' => $this->color,
            'rarity' => $this->rarity,
            'marketable' => $this->marketable,
            'tradeable' => $this->tradable,
            'acceptable' => $this->acceptable,
            'type' => $this->type,
            'app_id' => $this->appId,
            'orig_price'    => $this->origPrice,
            'rate_value'    => $this->rateValue,
            'count'     => $this->count,
            'stock_count'   => $this->stockCount,
        ];
    }
}