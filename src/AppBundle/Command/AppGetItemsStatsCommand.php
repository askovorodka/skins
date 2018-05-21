<?php

namespace AppBundle\Command;

use AppBundle\DTO\InventoryItem;
use AppBundle\Entity\Deposit;
use AppBundle\Service\ItemsPriceService;
use AppBundle\Utils\DepositFilterParams;
use AppBundle\Utils\StringUtils;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AppGetItemsStatsCommand extends ContainerAwareCommand
{
    private $skins = [];
    private $rate = 60;

    protected function configure()
    {
        $this
            ->setName('app:get_items_stats')
            ->setDescription('get items stats')
            ->addOption('appid', 'aid', InputOption::VALUE_OPTIONAL, 'appid', 0)
            ->addOption('datefrom', 'df', InputOption::VALUE_OPTIONAL, 'date from: df')
            ->addOption('dateto', 'dt', InputOption::VALUE_OPTIONAL, 'date to: dt')
            ->addOption('groupby', 'gb', InputOption::VALUE_OPTIONAL, 'group by: gb', 'marketname')
            ->addOption('integration', 'i', InputOption::VALUE_OPTIONAL,'integration')
            ->addOption('status', 's', InputOption::VALUE_OPTIONAL, 'deposit status', Deposit::STATUS_COMPLETED)
            ->addOption('push_status', 'ps', InputOption::VALUE_OPTIONAL,'push status', '1')
            ->addOption('currency', 'cy', InputOption::VALUE_OPTIONAL, 'currency')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $params = new DepositFilterParams();
        $depositService = $this->getContainer()->get('app.deposit_service');
        $redisService = $this->getContainer()->get('app.redis_service');
        $rate = $redisService->get(ItemsPriceService::PRICE_RATE_REDIS_KEY);
        $this->rate = $rate;

        if ($df = $input->getOption('datefrom')) {
            $params->setDateFrom(new \DateTime($df));
        }

        if ($dt = $input->getOption('dateto')) {
            $params->setDateTo(new \DateTime($dt));
        }

        if ($integration = $input->getOption('integration')) {
            $params->setIntegrationId((int) $integration);
        }

        if ($status = $input->getOption('status')) {
            $params->setStatus((string) $status);
        }

        if ($push_status = $input->getOption('push_status')) {
            $params->setPushStatus((int) $push_status);
        }

        if ($currency = $input->getOption('currency')) {
            $params->setCurrency((string) $currency);
        }

        $inpAppId = (int) $input->getOption('appid');

        $offset = 0;
        $limit = 1000;
        $params->setLimit($limit);
        $em = $this->getContainer()->get('doctrine')->getManager();
        $path = $this->getContainer()->get('kernel')->getRootDir() . '/../web/uploads/';
        $filename = date('Ymd_his') . '.csv';
        $handler = fopen($path . $filename, "w+");
        $skins = [];
        while (true) {
            try {
                $params->setOffset($offset);
                $deposits = $depositService->getByParameters($params);
                if (empty($deposits)) {
                    throw new \Exception('empty deposits');
                }
                $offset += $limit;

                foreach ($deposits as $deposit) {
                    $entity = $depositService->getDepositById($deposit['id']);
                    if ($entity instanceof Deposit) {
                        $items = $entity->getItems();
                        if (empty($items)) {
                            continue;
                        }
                        foreach ($items as $item) {
                            try {

                                if (!is_array($item)) {
                                    continue;
                                }
                                $app_id = $item['app_id'] ?? null;
                                if (!empty($inpAppId) && $inpAppId !== $app_id) {
                                    continue;
                                }

                                if ($input->getOption('groupby') == 'marketname') {
                                    $this->byMarketName($entity, $item);
                                } elseif ($input->getOption('groupby') == 'integration') {
                                    $this->byIntegration($entity, $item);
                                } elseif ($input->getOption('groupby') == 'all') {
                                    $this->byAll($entity, $item);
                                } elseif ($input->getOption('groupby') == 'deposit') {
                                    $this->byDeposit($entity, $item);
                                }
                            } catch (\Exception $exception) {
                                $output->writeln($exception->getTraceAsString());
                            }
                        }
                    }
                }
                $em->clear();

            } catch (\Exception $exception) {
                $em->clear();
                $output->writeln($exception->getMessage());
                break;
            }
        }

        if (!empty($this->skins)) {
            if ($input->getOption('groupby') == 'marketname') {
                usort($this->skins,[$this, 'sortPrice']);
            } elseif ($input->getOption('groupby') == 'integration') {
                usort($this->skins,[$this, 'sortIntegration']);
            } elseif ($input->getOption('groupby') == 'all') {
                usort($this->skins,[$this,'sortByPartnerId']);
            }
        }

        if ($input->getOption('groupby') == 'marketname') {
            $head = [
                'market_name',
                'count',
                'sum ($)',
                'orig_price ($)',
                'app_id',
                'type',
                'deposits',
                'steam_id',
            ];

        }

        if ($input->getOption('groupby') == 'integration') {
            $head = [
                'id',
                'partner',
                'market_name',
                'count',
                'sum ($)',
                'orig_price ($)',
                'app_id',
                'type',
                'deposits',
                'steam_id',
            ];
        }

        if ($input->getOption('groupby') == 'all') {
            $head = [
                'item_id',
                'created',
                'market_hash_name',
                'partner_name',
                'partner_id',
                'price ($)',
                'orig_price ($)',
                'app_id',
            ];
        }

        if ($input->getOption('groupby') == 'deposit') {
            $head = [
                'deposit_id',
                'count',
                'price',
                'steam_id',
                'currency',
            ];
        }

        fputcsv($handler, $head);

        foreach ($this->skins as $skin) {
            if ($input->getOption('groupby') == 'integration') {
                fputcsv($handler,[
                    $skin['integration_id'],
                    $skin['name'],
                    $skin['market_name'],
                    $skin['count'],
                    $skin['price'],
                    $skin['orig_price'],
                    $skin['app_id'],
                    $skin['type'],
                    implode(", ", $skin['deposits']),
                    implode(", ", $skin['steam_id']),
                ]);
            } elseif ($input->getOption('groupby') == 'marketname') {
                fputcsv($handler,[
                    $skin['market_name'],
                    $skin['count'],
                    $skin['price'],
                    $skin['orig_price'],
                    $skin['app_id'],
                    $skin['type'],
                    implode(", ", $skin['deposits']),
                    implode(", ", $skin['steam_id']),
                ]);
            } elseif ($input->getOption('groupby') == 'all') {
                fputcsv($handler,[
                    $skin['item_id'],
                    $skin['created'],
                    $skin['market_hash_name'],
                    $skin['partner_name'],
                    $skin['partner_id'],
                    $skin['price_usd'],
                    $skin['orig_price'],
                    $skin['app_id'],
                ]);
            } elseif ($input->getOption('groupby') == 'deposit') {
                fputcsv($handler,[
                    $skin['deposit_id'],
                    $skin['count'],
                    $skin['price'],
                    $skin['steam_id'],
                    $skin['currency'],
                ]);
            }
        }

        fclose($handler);

        $output->writeln('generate file: ' . $path . $filename);
    }

    /**
     * method sort two variables
     * @param $a
     * @param $b
     * @return int
     */
    private function sortIntegration($a, $b){
        return bccomp($b['integration_id'], $a['integration_id'], 2);
    }

    private function sortPrice($a, $b){
        return bccomp($b['price'], $a['price'], 2);
    }

    private function sortByPartnerId($a, $b) {
        return bccomp($b['partner_id'], $a['partner_id']);
    }

    private function byAll(Deposit $deposit, array $item) {
        $market_name = $item['market_hash_name'] ?? $item['market_name'] ?? 'noname';
        $price = $item['price'] ?? 0.00;
        $orig_price = $item['orig_price'] ?? 0;
        $price = round(StringUtils::prepareNumber($price), 2);
        $app_id = $item['app_id'] ?? '';
        $type = ($app_id == InventoryItem::APPID_DOTA2) ? 'dota' : 'csgo';

        if($deposit->getCurrency() === Deposit::CURRENCY_RUB) {
            $price = $price / $this->rate;
            $price = round($price,2);
        }

        $this->skins[] = [
            'item_id'           => $item['id'] ?? 0,
            'created'           => $deposit->getCreated()->format('Y-m-d H:i:s'),
            'market_hash_name'  => $market_name,
            'partner_name'      => $deposit->getIntegration()->getName(),
            'partner_id'        => $deposit->getIntegration()->getId(),
            'price_usd'         => $price,
            'orig_price'        => $orig_price,
            'app_id'            => $app_id,
        ];
    }

    private function byDeposit(Deposit $deposit, array $item) {
        $price = $item['price'] ?? 0.00;
        $price = round(StringUtils::prepareNumber($price), 2);
        $app_id = $item['app_id'] ?? '';
        $currency = $deposit->getCurrency();
        $depositId = $deposit->getId();
        if (empty($this->skins[$depositId])) {
            $this->skins[$depositId] = [
                'deposit_id'    => $depositId,
                'count'         => 1,
                'price'         => $price,
                'steam_id'      => $deposit->getSteamId(),
                'currency'      => $currency,
            ];
        } else {
            $this->skins[$depositId]['price'] += $price;
            $this->skins[$depositId]['count'] += 1;
        }

    }

    private function byMarketName(Deposit $deposit, array $item) {
        $market_name = $item['market_hash_name'] ?? $item['market_name'] ?? 'noname';
        $price = $item['price'] ?? 0.00;
        $orig_price = $item['orig_price'] ?? 0;
        $price = round(StringUtils::prepareNumber($price), 2);
        $app_id = $item['app_id'] ?? '';
        $type = ($app_id == InventoryItem::APPID_DOTA2) ? 'dota' : 'csgo';

        if($deposit->getCurrency() === Deposit::CURRENCY_RUB) {
            $price = $price / $this->rate;
            $price = round($price,2);
        }

        if (empty($this->skins[$market_name])) {
            $this->skins[$market_name] = [
                'count' => 1,
                'market_name' => $market_name,
                'price' => $price,
                'orig_price' => StringUtils::round($orig_price),
                'type'  => $type,
                'app_id'    => $app_id,
                'deposits'  => [(string) ' '.$deposit->getId()],
                'steam_id'  => [(string) ' '.$deposit->getSteamId()],
            ];
        } else {
            if (!empty($orig_price)) {
                $this->skins[$market_name]['orig_price'] = StringUtils::round($orig_price);
            }
            $this->skins[$market_name]['count'] += 1;
            $this->skins[$market_name]['price'] += $price;
            if (!in_array($deposit->getId(), $this->skins[$market_name]['deposits'])) {
                $this->skins[$market_name]['deposits'][] = (string) $deposit->getId();
            }
            if (!in_array($deposit->getSteamId(), $this->skins[$market_name]['steam_id'])) {
                $this->skins[$market_name]['steam_id'][] = (string) $deposit->getSteamId();
            }

        }

    }

    public function byIntegration(Deposit $deposit, array $item)
    {
        $integration_id = $deposit->getIntegration()->getId();
        $integration_name = $deposit->getIntegration()->getName();

        $market_name = $item['market_hash_name'] ?? $item['market_name'] ?? 'noname';
        $price = $item['price'] ?? 0.00;
        $orig_price = $item['orig_price'] ?? 0.00;
        $price = StringUtils::round($price);
        $app_id = $item['app_id'] ?? '';
        $type = ($app_id == InventoryItem::APPID_DOTA2) ? 'dota' : 'csgo';

        if($deposit->getCurrency() === Deposit::CURRENCY_RUB) {
            $price = $price / $this->rate;
            $price = StringUtils::round($price);
        }

        if (empty($this->skins[$integration_id.$market_name])) {
            $this->skins[$integration_id.$market_name] = [
                'integration_id'    => $integration_id,
                'name'  => $integration_name,
                'count' => 1,
                'market_name' => $market_name,
                'price' => $price,
                'orig_price' => StringUtils::round($orig_price),
                'type'  => $type,
                'app_id'    => $app_id,
                'deposits'  => [(string) ' '.$deposit->getId()],
                'steam_id'  => [(string) ' '.$deposit->getSteamId()],
            ];
        } else {
            $this->skins[$integration_id.$market_name]['count'] += 1;
            $this->skins[$integration_id.$market_name]['price'] += $price;
            if (!in_array($deposit->getId(), $this->skins[$integration_id.$market_name]['deposits'])) {
                $this->skins[$integration_id.$market_name]['deposits'][] = (string) $deposit->getId();
            }
            if (!in_array($deposit->getSteamId(), $this->skins[$integration_id.$market_name]['steam_id'])) {
                $this->skins[$integration_id.$market_name]['steam_id'][] = (string) $deposit->getSteamId();
            }
        }

    }

}
