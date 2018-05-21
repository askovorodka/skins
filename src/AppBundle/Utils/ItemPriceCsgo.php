<?php
namespace AppBundle\Utils;

use AppBundle\DTO\InventoryItem;
use AppBundle\Entity\Deposit;
use Monolog\Logger;

class ItemPriceCsgo implements ItemPriceStrategyIface
{
    const MIN_PRICE_USD             = 0.3;
    const AVERAGE_MIN_PRICE_USD     = 1;
    const NORMAL_PRICE_USD          = 7;
    const HIGH_PRICE_USD            = 87;
    const STOCK_LIMIT               = 20;

    const DECREASE_PRICE_X          = 500;
    const DECREASE_PRICE_Y          = 10;
    const DECREASE_PRICE_Z          = 5;

    /**
     * @var InventoryItem $inventoryItem
     */
    private $inventoryItem;
    /**
     * @var array $itemPrice
     */
    private $itemPrice;
    /**
     * @var float $price
     */
    private $price;
    /**
     * @var array $whiteList
     */
    private $whiteList;
    /**
     * @var Deposit $deposit
     */
    private $deposit;

    /**
     * @var bool $isDecreasePrice
     */
    private $isDecreasePrice = false;

    /**
     * @var Logger $logger
     */
    private $logger;

    public function __construct(
        Deposit $deposit,
        InventoryItem $item,
        Logger $logger,
        array $itemPrice,
        array $whiteList
    )
    {
        $this->deposit          = $deposit;
        $this->inventoryItem    = $item;
        $this->itemPrice        = $itemPrice;
        $this->whiteList        = $whiteList;
        $this->logger           = $logger;
    }

    public function getPrice(): float
    {
        $prices = [];
        $price = $this->itemPrice['avg_price_7_days_raw'] ?? $this->itemPrice['suggested_amount_min_raw'];
        $price = str_replace(",", "", $price);
        array_push($prices, $price);
        if (!empty($this->itemPrice['current_price'])) {
            array_push($prices, str_replace(",", "", $this->itemPrice['current_price']));
        }

        if (isset($this->itemPrice['ongoing_price_manipulation']) && $this->itemPrice['ongoing_price_manipulation'] == 1) {
            $this->logger->info('ongoing price manipulation for market name', [$this->itemPrice]);
            array_push($prices, $this->itemPrice['safe_price_raw']);
        }

        $this->price = (float) min($prices);

        $this->price = $this->decreasePrice();
        return StringUtils::round($this->price);

    }

    private function decreasePrice() {
        $marketName = $this->inventoryItem->getMarketHashName();
        try {
            if (isset($this->whiteList[$marketName])) {
                $stockCount = $this->whiteList[$marketName]['stock_count'];
                $this->inventoryItem->setStockCount($stockCount);
                if ($stockCount >= self::DECREASE_PRICE_X) {
                    $xy = self::DECREASE_PRICE_Y * $stockCount / 100;
                    $xPrice = self::DECREASE_PRICE_Z * $this->price / 100;
                    $xx = round(abs($this->inventoryItem->getCount() - $stockCount)) / $xy;
                    $price_temp = $this->price - ($xx * $xPrice);
                    $this->price = round($price_temp,2) > 0.00 ? round($price_temp, 2) : $this->price;
                    $this->isDecreasePrice = true;
                }
            }
        } catch (\Exception $exception) {

        } finally {
            return $this->price;
        }
    }

    /**
     * @return bool
     */
    public function getIsDecreasePrice(): bool {
        return $this->isDecreasePrice;
    }

    public function getComission(): float
    {
        if ($this->price < self::MIN_PRICE_USD) {
            $dynamicCommission = 0.5;
        } elseif ($this->price >= self::MIN_PRICE_USD && $this->price < self::AVERAGE_MIN_PRICE_USD) {
            $dynamicCommission = 0.6;
        } elseif ($this->price >= self::AVERAGE_MIN_PRICE_USD && $this->price < self::NORMAL_PRICE_USD) {
            $dynamicCommission = $this->calculateCommission();
        } elseif ($this->price >= self::NORMAL_PRICE_USD && $this->price < self::HIGH_PRICE_USD) {
            $dynamicCommission = 0.765;
        } else {
            $dynamicCommission = 0.7;
        }

        return $dynamicCommission;

    }

    private function calculateCommission()
    {
        $dynamicCommission = (100 - $this->deposit->getIntegration()->getValueTaxPercent()) / 100;
        $marketName = $this->inventoryItem->getMarketName();
        if (isset($this->whiteList[$marketName])) {
            $stockCount = $this->whiteList[$marketName]['stock_count'];
            $demand = $this->whiteList[$marketName]['demand'];
            if ($stockCount > $demand && $demand != 0) {
                $dynamicCommission = $dynamicCommission * (1 - ($demand / $stockCount));
                $this->logger->info("$dynamicCommission%");
            } elseif ($demand == 0 && $stockCount > self::STOCK_LIMIT) {
                $dynamicCommission = $dynamicCommission * (1 - (self::STOCK_LIMIT / $stockCount));
                $this->logger->info("$dynamicCommission%");
            } elseif ($stockCount == 0 && $demand > 0) {
                $this->logger->info('+10%');
                $dynamicCommission = $dynamicCommission * 1.1;
            } elseif ($stockCount > 0 && $demand > 0 && $demand > $stockCount) {
                $this->logger->info('+5%');
                $dynamicCommission = $dynamicCommission * 1.05;
            }
        }

        return (float) $dynamicCommission;
    }

}