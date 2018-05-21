<?php

namespace AppBundle\Command;

use AppBundle\Exception\LoadWhiteListException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AppGetItemsWhitelistCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:load_items_whitelist')
            ->setDescription('load items white list from cases4real')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->getContainer()->get('app.items_price_service')->loadWhiteListAndStoreInCache();
            $this->getContainer()->get('app.items_price_service')->loadDotaWhiteListAndStoreInCache();
            $output->writeln('white list updated: ' . date('Y-m-d H:i:s', time()));
        } catch (LoadWhiteListException $e) {
            $this->getContainer()->get('logger')->critical('Failed to load items white list from cases4real', [$e]);
            $output->writeln($e->getMessage());
        }
    }

}
