<?php
namespace AppBundle\EventListener;

use AppBundle\DTO\InventoryItem;
use AppBundle\Entity\Deposit;
use AppBundle\Event\DepositEvent;
use AppBundle\Service\Deposit\DepositService;
use AppBundle\Service\ItemsPriceService;
use AppBundle\Service\RedisService;
use AppBundle\Service\SlackService;
use Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DepositListener implements EventSubscriberInterface
{
    private $logger;
    private $depositService;
    private $redisService;
    private $slackService;

    public function __construct(
        Logger $logger,
        RedisService $redisService,
        DepositService $depositService,
        SlackService $slackService
        )
    {
        $this->logger = $logger;
        $this->redisService = $redisService;
        $this->depositService = $depositService;
        $this->slackService = $slackService;
    }

    public static function getSubscribedEvents()
    {
        return [
            DepositEvent::DEPOSIT_UPDATE    => 'onDepositUpdate',
        ];
    }

    public function onDepositUpdate(DepositEvent $event)
    {
        try {
            $deposit = $event->getDeposit();
            $value = $deposit->getValue();
            $trade_hash = $deposit->getTradeHash();
            //calculate deposit value
            if ($deposit->getCurrency() == Deposit::CURRENCY_RUB) {
                $rate = $this->redisService->get(ItemsPriceService::PRICE_RATE_REDIS_KEY);
                if (!empty($rate)) {
                    $value = $value / $rate;
                }
            }
            if ($value >= Deposit::MAXIMUM_VALUE_LIMIT && !empty($trade_hash))
            {
                $trace = [
                    'deposit_id' => $deposit->getId(),
                    'trade_hash' => $deposit->getTradeHash(),
                    'value' => $deposit->getValue(),
                    'currency' => $deposit->getCurrency(),
                ];
                $this->logger->notice('Deposit exceeded limit of value', $trace);
                $this->slackService->sendNotice('Превышен лимит депозита: ' . json_encode($trace));
            }

            //calculate items count duplicate by item_hash_name
            $items = $deposit->getItems();
            $itemsByHashName = [];
            foreach ($items as $inventoryItem) {
                $marketHashName = null;
                if ($inventoryItem instanceof InventoryItem) {
                    $marketHashName = $inventoryItem->getMarketHashName();
                } elseif (is_array($inventoryItem)) {
                    $marketHashName = $inventoryItem['market_hash_name'];
                }
                if (isset($itemsByHashName[$marketHashName])) {
                    $itemsByHashName[$marketHashName] += 1;
                } else {
                    $itemsByHashName[$marketHashName] = 1;
                }
            }

            $itemsByHashName = array_filter($itemsByHashName, function ($value) {
                return $value > 10;
            });

            if (!empty($itemsByHashName)) {
                foreach ($itemsByHashName as $hashName => $count) {
                    $trade_hash = $deposit->getTradeHash();
                    if (empty($trade_hash)) {
                        continue;
                    }
                    $trace = [
                        'deposit_id' => $deposit->getId(),
                        'trade_hash' => $deposit->getTradeHash(),
                        'market_hash_name' => $hashName,
                        'count' => $count,
                    ];
                    $this->logger->notice('Deposit items over limit', $trace);
                    $this->slackService->sendNotice('Превышен лимит кол-ва items: ' . json_encode($trace));
                }
            }

        } catch(\Exception $exception) {
            $this->logger->crit('DepositListener exception',[
                'message'   => $exception->getMessage(),
            ]);
        }

    }

}