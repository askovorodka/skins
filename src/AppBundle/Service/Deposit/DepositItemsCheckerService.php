<?php

namespace AppBundle\Service\Deposit;

use AppBundle\DTO\InventoryItem;
use AppBundle\Entity\Deposit;
use AppBundle\Event\DepositEvent;
use AppBundle\Exception\UnacceptableItemSubmittedException;
use AppBundle\Service\ItemsPriceService;
use AppBundle\Service\RedisService;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DepositItemsCheckerService
{
    const DEPOSIT_ITEMS_REDIS_KEY = 'deposit_items_';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ItemsPriceService
     */
    private $itemsPriceService;

    /**
     * @var RedisService
     */
    private $redisService;

    /**
     * @var EventDispatcherInterface $eventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var array
     */
    private $items = [];

    /**
     * @var array
     */
    private $fakeItems = [];

    public function __construct(
        ItemsPriceService $itemsPriceService,
        LoggerInterface $logger,
        RedisService $redis,
        EventDispatcherInterface $eventDispatcher
        )
    {
        $this->itemsPriceService = $itemsPriceService;
        $this->logger = $logger;
        $this->redisService = $redis;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param Deposit $deposit
     * @param $items
     * @param bool $throwOnFakeItems
     * @return Deposit
     * @throws UnacceptableItemSubmittedException
     */
    public function filterItemsAndRecountValue(Deposit $deposit, $items, bool $throwOnFakeItems = false)
    {
        $depositItems = $this->redisService->getJsonByKey(self::DEPOSIT_ITEMS_REDIS_KEY.$deposit->getId());
        $this->checkItemsMarketNames($items, $depositItems);
        $depositValue = $this->itemsPriceService->calculateInventoryValue($deposit, $this->items);

        $this->itemsPriceService->calculateDepositNoTaxValue($deposit, $this->items);
        $deposit
            ->setItems($this->items)
            ->setValue($depositValue)
        ;

        if (!empty($this->fakeItems)) {
            $this->logger->critical("HACKER ALERT! Fake items", [$this->fakeItems, $deposit]);
            if ($throwOnFakeItems) {
                $deposit->setStatus(Deposit::STATUS_ERROR_UNACCEPTABLE_ITEM);
                throw new UnacceptableItemSubmittedException("HACKER ALERT! Fake items: " . json_encode($this->fakeItems));
            }
        }

        //handle event dispatch on deposit update event
        $this->eventDispatcher->dispatch(DepositEvent::DEPOSIT_UPDATE, new DepositEvent($deposit));

        return $deposit;
    }

    /**
     * @param $items
     * @param $depositItems
     */
    private function checkItemsMarketNames($items, $depositItems)
    {
        foreach ($items as $item) {
            if (isset($depositItems[$item['id']]) && $depositItems[$item['id']]['market_hash_name'] == $item['market_hash_name'] && $depositItems[$item['id']]['acceptable'] == true) {
                $this->items[$item['id']] = new InventoryItem($depositItems[$item['id']]);
            } else {
                $this->fakeItems[$item['id']] = $item;
            }
        }
    }
}
