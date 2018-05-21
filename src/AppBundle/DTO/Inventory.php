<?php
/**
 * Created by PhpStorm.
 * User: ahimas
 * Date: 19.09.16
 * Time: 11:06
 */

namespace AppBundle\DTO;


use AppBundle\Entity\Deposit;

class Inventory
{
    /**
     * @var InventoryItem[]
     */
    public $items;

    /**
     * @var string
     */
    public $itemsValue;

    /**
     * @var Deposit
     */
    public $deposit;

    /**
     * @return InventoryItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param InventoryItem[] $items
     * @return Inventory
     */
    public function setItems($items)
    {
        $this->items = $items;
        return $this;
    }

    /**
     * @return string
     */
    public function getItemsValue()
    {
        return $this->itemsValue;
    }

    /**
     * @param string $itemsValue
     * @return Inventory
     */
    public function setItemsValue($itemsValue)
    {
        $this->itemsValue = $itemsValue;
        return $this;
    }

    /**
     * @return Deposit
     */
    public function getDeposit()
    {
        return $this->deposit;
    }

    /**
     * @param Deposit $deposit
     * @return Inventory
     */
    public function setDeposit($deposit)
    {
        $this->deposit = $deposit;
        return $this;
    }
}