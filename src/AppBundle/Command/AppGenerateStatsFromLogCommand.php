<?php

namespace AppBundle\Command;

use AppBundle\DTO\InventoryItem;
use AppBundle\Entity\Deposit;
use AppBundle\Service\ItemsPriceService;
use AppBundle\Utils\ItemPriceCsgo;
use AppBundle\Utils\ItemPriceDota;
use AppBundle\Utils\ItemPriceStrategy;
use AppBundle\Utils\StringUtils;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AppGenerateStatsFromLogCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:generate_stats_from_log')
            ->setDescription('generate stats from log file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $root = $this->getContainer()->get('kernel')->getRootDir() . '/../';
        $redisService = $this->getContainer()->get('app.redis_service');
        $itemPriceService = $this->getContainer()->get('app.items_price_service');
        $whiteList = $itemPriceService->getItemsWhiteList();
        $logger = $this->getContainer()->get('logger');

        $log = fopen($root . "prod_alerts.log","r");
        $result = [];
        $result = [];
        while (($row = fgets($log)) !== false) {
            if (preg_match("/(?<json>\{[^\}]+\})/is", $row, $match)) {
                $data = json_decode($match['json'], true);
                $market_name = $data['market_hash_name'];
                $deposit = $data['deposit_id'];
                $trade_hash = $data['trade_hash'];
                $count = $data['count'];

                if (isset($result[$market_name])) {
                    $result[$market_name]['count'] += (int) $count;
                } else {
                    $result[$market_name] = [
                        'market_name'   => $market_name,
                        'count'         => $count,
                    ];
                }
            }
        }

        fclose($log);


        $csv = fopen($root . "web/uploads/over500.csv","w+");
        fputcsv($csv,[
            'market_name',
            'type',
            'sum_count',
            'price',
            'orig_price',
        ]);

        foreach ($result as $value) {
            if ($value['count'] < 500) {
                continue;
            }

            $key = ItemsPriceService::ITEM_PRICE_REDIS_PREFIX . $value['market_name'];
            $type = null;
            if ($redisService->isExists($key)) {
                $data = $redisService->getJsonByKey($key);
                $strategy = new ItemPriceStrategy(new ItemPriceCsgo(new Deposit(), new InventoryItem([]), $logger,$data, $whiteList));
                $type = 'csgo';
            } elseif ($redisService->hexists(ItemsPriceService::DOTA_WHITE_LIST_REDIS_KEY, $value['market_name'])) {
                $data = $redisService->hgetJson(ItemsPriceService::DOTA_WHITE_LIST_REDIS_KEY, $value['market_name']);
                $strategy = new ItemPriceStrategy(new ItemPriceDota($data));
                $type = 'dota';
            }

            $origPrice = $strategy->calculatePrice();
            $price = $strategy->calculateComission() * $origPrice;

            fputcsv($csv, [
                $value['market_name'],
                $type,
                $value['count'],
                StringUtils::round($price),
                StringUtils::round($origPrice),
            ]);

        }

        fclose($csv);
        $output->writeln('Command result.');
    }

}
