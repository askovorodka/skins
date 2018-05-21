<?php
/**
 * Created by PhpStorm.
 * User: ahimas
 * Date: 06.10.16
 * Time: 22:24.
 */

namespace AppBundle\Service;

use AppBundle\DTO\Inventory;
use AppBundle\DTO\InventoryItem;
use AppBundle\Entity\Deposit;
use AppBundle\Service\Deposit\DepositItemsCheckerService;
use Monolog\Logger;

/**
 * Class InventoryService.
 */
class InventoryService
{
    /**
     * @var SteamService
     */
    private $steamService;

    /**
     * @var ItemsPriceService
     */
    private $itemsPriceService;

    private $redisService;

    private $logger;

    public function __construct(SteamService $steamService, ItemsPriceService $itemsPriceService, RedisService $redisService, Logger $logger)
    {
        $this->steamService = $steamService;
        $this->itemsPriceService = $itemsPriceService;
        $this->redisService = $redisService;
        $this->logger = $logger;
    }

    /**
     * @param Deposit $deposit
     * @param string  $locale
     *
     * @return Inventory
     */
    public function getInventoryByTradeUrl(Deposit $deposit, $locale)
    {
        $steamInventory = $this->steamService->loadInventoryByTradeUrl($deposit, $locale);
        $inventory = new Inventory();
        $inventoryItems = $this->prepareInventory($steamInventory);
        $inventory->setItems($inventoryItems);
        $this->redisService->setex(DepositItemsCheckerService::DEPOSIT_ITEMS_REDIS_KEY.$deposit->getId(), 36000, $inventoryItems);
        $inventoryValue = $this->itemsPriceService->calculateInventoryValue($deposit, $inventory->getItems());
        $inventory->setItemsValue($inventoryValue);
        $inventory->setItems($this->itemsPriceService->sortByValue($inventory->getItems()));
        $inventory->setDeposit($deposit);

        return $inventory;
    }

    /**
     * @param $steamInventory
     *
     * @return array
     */
    private function prepareInventory($steamInventory)
    {
        $result = [];
        $groupByMarketName = [];

        foreach ($steamInventory as $item) {
            $market_hash_name = $item['market_hash_name'] ?? $item['market_name'] ?? '';
            if (!isset($groupByMarketName[$market_hash_name])) {
                $groupByMarketName[$market_hash_name] = 1;
            } else {
                $groupByMarketName[$market_hash_name] += 1;
            }

        }

        foreach ($steamInventory as $item) {
            $inventoryItem = new InventoryItem($item);
            if ($inventoryItem->getTradable() === false || $inventoryItem->getMarketable() === false) {
                continue;
            }

            if ((preg_match('/case/i', $inventoryItem->getMarketHashName())
                && !preg_match('/case key/i', $inventoryItem->getMarketHashName())
                && !preg_match('/case hardened/i', $inventoryItem->getMarketHashName()))
                || preg_match('/Base Grade Container/i', $inventoryItem->getMarketHashName())
                || preg_match('/Base Grade Key/i', $inventoryItem->getMarketHashName())
                || preg_match('/High Grade Music Kit/i', $inventoryItem->getMarketHashName())
                || preg_match('/Loading Screen/i', $inventoryItem->getMarketHashName())
                || preg_match('/Swap Tool/i', $inventoryItem->getMarketHashName())
                || preg_match('/Graffiti/i', $inventoryItem->getMarketHashName())
                || preg_match('/Capsule/i', $inventoryItem->getMarketHashName())
                || preg_match('/Name Tag/i', $inventoryItem->getMarketHashName())
            ) {
                $inventoryItem->setAcceptable(false);
            }

            if (isset($groupByMarketName[$inventoryItem->getMarketHashName()])){
                $inventoryItem->setCount($groupByMarketName[$inventoryItem->getMarketHashName()]);
            }
            $inventoryItem->setIconUrl(InventoryItem::ICON_BASE_URL.$inventoryItem->getIconUrl().'/100x100');
            $result[$inventoryItem->getId()] = $inventoryItem;
        }

        return $result;
    }
}
