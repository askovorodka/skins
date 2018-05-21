<?php
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppSkinpriceLoadPriceCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:skinprice_load_price')
            ->setDescription('Load price from skinprice');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = $this->getContainer()->get('logger');
        $startTime = time();
        $skinpriceService = $this->getContainer()->get('app.skinprice_service');

        $logger->info($this->getName() . ' start');
        $skinpriceService->saveCSGOInCache();
        $skinpriceService->saveDotaInCache();
        $skinpriceService->savePubgInCache();

        $logger->info($this->getName() . ' end. working time:' . (time() - $startTime) . ' sec');
    }
}
