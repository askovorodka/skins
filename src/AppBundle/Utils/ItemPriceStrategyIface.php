<?php
namespace AppBundle\Utils;

interface ItemPriceStrategyIface
{
    public function getPrice(): float;
    public function getComission(): float;
}