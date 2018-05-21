<?php
namespace AppBundle\Utils;

class ItemPriceDota implements ItemPriceStrategyIface
{
    const MIN_PRICE_USD = 1;
    /**
     * @var array $itemArray
     */
    private $itemArray;

    /**
     * @var float $price
     */
    private $price;

    public function __construct(array $itemArray)
    {
        $this->itemArray = $itemArray;
        $this->price = $itemArray['price'] ?? 0.00;
    }

    public function getPrice(): float
    {
        return StringUtils::round($this->price);
    }

    public function getComission(): float
    {
        $quality = $this->itemArray['quality'] ?? null;
        $market_current_volume = $this->itemArray['market_current_volume'] ?? 0;
        $avg_daily_volume = $this->itemArray['avg_daily_volume'] ?? 0;
        $sold_last_7d = $this->itemArray['sold_last_7d'] ?? 0;

        if ($market_current_volume < 100 && $this->price > self::MIN_PRICE_USD) {
            return -1;
        }

        if (preg_match("/Arcana/i", $quality)) {
            return 0.65;
        }

        if ($market_current_volume < 100 && $this->price < self::MIN_PRICE_USD) {
            return 0.25;
        }

        $relate = null;
        if (!empty($avg_daily_volume)) {
            $relate = $sold_last_7d / ($avg_daily_volume * 7);
        }

        if ($relate && $relate <= 0.5) {
            return 0.25;
        }

        return 0.45;

    }
}