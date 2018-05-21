<?php

namespace AppBundle\Command;

use AppBundle\DTO\InventoryItem;
use AppBundle\Entity\Deposit;
use AppBundle\Service\IntegrationService;
use AppBundle\Service\ItemsPriceService;
use AppBundle\Utils\DepositFilterParams;
use AppBundle\Utils\ItemPriceDota;
use AppBundle\Utils\ItemPriceStrategy;
use AppBundle\Utils\StringUtils;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AppCalculatePriceByItemsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:calculate_price_by_items')
            ->setDescription('calculate price by items dota')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $params = new DepositFilterParams();
        $params
            ->setStatus(Deposit::STATUS_COMPLETED)
            ->setPushStatus(IntegrationService::PUSH_STATUS_SUCCESS)
            ->setDateFrom(new \DateTime(('2017-11-24 00:00:00')))
            ->setDateTo(new \DateTime('2017-12-02 00:00:00'))
        ;

        $depositService = $this->getContainer()->get('app.deposit_service');
        $redisService = $this->getContainer()->get('app.redis_service');
        $rate = $redisService->get(ItemsPriceService::PRICE_RATE_REDIS_KEY);

        $offset = 0;
        $limit = 1000;
        $params->setLimit($limit);
        $filename = 'skins_all_24_11-02_12.csv';
        $handler = fopen($this->getContainer()->get('kernel')->getRootDir() .'/../web/uploads/' . $filename,'w+');
        fputcsv($handler,[
            'market_hash_name',
            'count',
            'price',
            'type',
            'deposit_id',
        ]);

        $output = [];
        while(true)
        {
            try {

                $params->setOffset($offset);
                $depositsList = $depositService->getByParameters($params);
                $offset += $limit;

                if (empty($depositsList)) {
                    break;
                }

                foreach ($depositsList as $deposit) {
                    $entity = $depositService->getDepositById($deposit['id']);
                    $items = $entity->getItems();
                    if (empty($items)) {
                        continue;
                    }
                    $currency = $entity->getCurrency();
                    $created = $entity->getCreated()->format('Y-m-d H:i:s');
                    $integration = $entity->getIntegration()->getName();
                    foreach ($items as $item) {
                        $market_hash_name = $item['market_hash_name'] ?? $item['market_name'] ?? '';
                        $price = $item['price'] ?? 0.00;
                        $price = StringUtils::round($price);
                        $app_id = $item['app_id'];
                        if ($currency == Deposit::CURRENCY_RUB){
                            $price = $price / $rate;
                            $price = round($price, 2);
                        }

                        if (empty($output[$market_hash_name])) {
                            $output[$market_hash_name]['market_hash_name'] = $market_hash_name;
                            $output[$market_hash_name]['count'] = 1;
                            $output[$market_hash_name]['price'] = $price;
                            $output[$market_hash_name]['app_id'] = $app_id;
                            $output[$market_hash_name]['type'] = $app_id == InventoryItem::APPID_DOTA2 ? 'dota' : 'csgo';
                            $output[$market_hash_name]['steam_id'][] = $entity->getSteamId();
                            $output[$market_hash_name]['deposit_id'][] = ' ' . $entity->getId();

                        } else {
                            $output[$market_hash_name]['count'] += 1;
                            $output[$market_hash_name]['price'] += $price;
                            $output[$market_hash_name]['steam_id'][] = $entity->getSteamId();
                            $output[$market_hash_name]['deposit_id'][] = ' ' . $entity->getId();
                        }
                        //continue;

                        /*if (empty($market_hash_name)) {
                            continue;
                        }
                        $id = $item['id'] ?? 0;
                        $price = $item['price'] ?? 0.00;
                        $price = str_replace(" ",'', $price);
                        $price = str_replace(",",'', $price);
                        $price = round($price, 2);
                        $price_in_usd = round($price, 2);
                        $app_id = $item['app_id'] ?? 0;
                        if ($currency == Deposit::CURRENCY_RUB){
                            $price_in_usd = $price_in_usd / $rate;
                            $price_in_usd = round($price_in_usd, 2);
                        }
                        fputcsv($handler, [
                            $id,
                            $created,
                            $market_hash_name,
                            $integration,
                            $price,
                            $currency,
                            $price_in_usd,
                            $app_id,
                        ]);*/
                    }
                }
                $em->clear();

            } catch (\Exception $exception){
                print_r($exception->getMessage());
                break;
            }
        }

        usort($output, [$this,"sort"]);

        foreach ($output as $value) {
            fputcsv($handler,[
                $value['market_hash_name'],
                $value['count'],
                $value['price'],
                $value['type'],
                implode(", ", $value['deposit_id']),
            ]);
        }

        fclose($handler);
        echo ('end ' . $filename) . PHP_EOL;

    }

    private function sort($a, $b){
        return bccomp($b['price'], $a['price'], 2);
    }

}
