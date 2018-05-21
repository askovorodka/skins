<?php

namespace AppBundle\Command;

use AppBundle\Service\ItemsPriceService;
use AppBundle\Utils\ItemPriceDota;
use AppBundle\Utils\ItemPriceStrategy;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AppCheckDotaInventoryCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:check_dota_inventory')
            ->setDescription('Check dota json inventory')
            ->addOption('inventory', null, InputOption::VALUE_REQUIRED, 'Json inventory')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($inventory = $input->getOption('inventory')) {
            $inventory = (json_decode($inventory, true));
            if (is_string($inventory)) {
                $inventory = json_decode($inventory, true);
            }

            foreach ($inventory as $item)
            {
                $itemArray = $this->getContainer()->get('app.redis_service')->hgetJson(ItemsPriceService::DOTA_WHITE_LIST_REDIS_KEY, $item['market_hash_name']);
                $strategy = new ItemPriceStrategy(new ItemPriceDota($itemArray));
                $price = $strategy->calculatePrice();
                $comission = $strategy->calculateComission();
                $item['no_tax_value'] = $price;
                $item['value'] = $price * $comission;
                $item['comission'] = $comission;

                print_r($item);
            }



        }

    }

}
