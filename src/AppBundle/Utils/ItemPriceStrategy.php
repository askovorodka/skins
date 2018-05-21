<?php
namespace AppBundle\Utils;

class ItemPriceStrategy
{
    /**
     * @var ItemPriceStrategyIface $itemPrice
     */
    private $itemPrice;

    public function __construct(ItemPriceStrategyIface $itemPrice)
    {
        $this->itemPrice = $itemPrice;
    }

    public function calculatePrice() {
        return $this->itemPrice->getPrice();
    }

    public function calculateComission() {
        return $this->itemPrice->getComission();
    }

}