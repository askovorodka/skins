<?php

namespace AppBundle\Command;

use AppBundle\DTO\InventoryItem;
use AppBundle\Entity\Deposit;
use AppBundle\Service\IntegrationService;
use AppBundle\Service\ItemsPriceService;
use AppBundle\Utils\DepositFilterParams;
use AppBundle\Utils\ItemPriceCsgo;
use AppBundle\Utils\ItemPriceDota;
use AppBundle\Utils\ItemPriceStrategy;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AppSetDepositsItemsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:set_deposits_items')
            ->setDescription('set deposits items dota and csgo ')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $depositService = $this->getContainer()->get('app.deposit_service');
        $redisService = $this->getContainer()->get('app.redis_service');
        $em = $this->getContainer()->get('doctrine')->getManager();

        $params = new DepositFilterParams();
        $offset = 0;
        $limit = 100;
        $params
            ->setStatus(Deposit::STATUS_COMPLETED)
            //->setPushStatus(IntegrationService::PUSH_STATUS_SUCCESS)
            ->setDateFrom(new \DateTime('2017-07-21 00:00:00'));
        while (true)
        {
            try {

                $params
                    ->setOffset($offset)
                    ->setLimit($limit);
                $offset += $limit;

                $deposits = $depositService->getByParameters($params);
                if (empty($deposits)) {
                    throw new \Exception('deposits empty');
                }

                foreach ($deposits as $deposit)
                {
                    /**
                     * @var Deposit $entity
                     */
                    $entity = $depositService->getDepositById($deposit['id']);
                    if (!empty($entity))
                    {
                        $items = $entity->getItems();
                        $itemsPrice = [];
                        /**
                         * @var array $item
                         */
                        foreach ($items as $item) {

                            $price = str_replace(" ","", $item['price']);
                            $price = (float) str_replace(",","", $price);

                            $appId = $item['app_id'] ?? null;
                            if (empty($appId)) {
                                $marketHashName = $item['market_hash_name'] ?? $item['market_name'];
                                $redisKey = ItemsPriceService::ITEM_PRICE_REDIS_PREFIX . $marketHashName;
                                if ($redisService->isExists($redisKey)) {
                                    $item['app_id'] = InventoryItem::APPID_CSGO;
                                } elseif ($redisService->hexists(ItemsPriceService::DOTA_WHITE_LIST_REDIS_KEY, $marketHashName)) {
                                    $item['app_id'] = InventoryItem::APPID_DOTA2;
                                }
                            }
                            if ($item['app_id'] === InventoryItem::APPID_CSGO) {
                                if (isset($itemsPrice[Deposit::ITEMS_PRICE_CSGO_KEY])) {
                                    $itemsPrice[Deposit::ITEMS_PRICE_CSGO_KEY]['value'] += $price;
                                } else {
                                    $itemsPrice[Deposit::ITEMS_PRICE_CSGO_KEY]['value'] = $price;
                                    $itemsPrice[Deposit::ITEMS_PRICE_CSGO_KEY]['no_tax_value'] = 0;
                                }
                            } elseif ($item['app_id'] === InventoryItem::APPID_DOTA2) {
                                $no_tax_price = 0;
                                if (isset($itemsPrice[Deposit::ITEMS_PRICE_DOTA_KEY])) {
                                    $itemsPrice[Deposit::ITEMS_PRICE_DOTA_KEY]['value'] += $price;
                                    $itemsPrice[Deposit::ITEMS_PRICE_DOTA_KEY]['no_tax_value'] += $no_tax_price;
                                } else {
                                    $itemsPrice[Deposit::ITEMS_PRICE_DOTA_KEY]['value'] = $price;
                                    $itemsPrice[Deposit::ITEMS_PRICE_DOTA_KEY]['no_tax_value'] = $no_tax_price;
                                }
                            }
                        }

                        $entity
                            ->setValueCsgo($itemsPrice[Deposit::ITEMS_PRICE_CSGO_KEY]['value'] ?? 0.000)
                            ->setNoTaxValueCsgo($itemsPrice[Deposit::ITEMS_PRICE_CSGO_KEY]['no_tax_value'] ?? 0.000)
                            ->setValueDota($itemsPrice[Deposit::ITEMS_PRICE_DOTA_KEY]['value'] ?? 0.000)
                            ->setNoTaxValueDota($itemsPrice[Deposit::ITEMS_PRICE_DOTA_KEY]['no_tax_value'] ?? 0.000);
                        $em->merge($entity);
                        $output->writeln('update deposit: ' . $entity->getId() . " - " . $entity->getValueCsgo() . ' - ' . $entity->getValueDota());

                    }
                }
                $em->flush();
                $em->clear();
            } catch(\Exception $exception) {
                $em->flush();
                $em->clear();
                $output->writeln($exception->getMessage());
                break;
            }
        }

    }

}

