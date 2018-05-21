<?php

namespace AppBundle\Command;

use AppBundle\Exception\LoadPriceListException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AppLoadItemsPriceCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:load_items_price')
            ->setDescription('Load items price from cases4real and store in redis cache')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->getContainer()->get('app.items_price_service')->loadPricesAndStoreInCache();
            $output->writeln('ok');
        } catch (LoadPriceListException $e) {
            $this->getContainer()->get('logger')->critical('Failed to load items price list from cases4real', [$e]);
            $output->writeln($e->getMessage());
        }
    }

}
