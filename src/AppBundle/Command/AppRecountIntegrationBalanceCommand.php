<?php
/**
 * Created by PhpStorm.
 * User: ahimas
 * Date: 31.01.17
 * Time: 18:19
 */

namespace AppBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppRecountIntegrationBalanceCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:recount_integration_balance')
            ->setDescription('Load items price from cases4real and store in redis cache')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
            $this->getContainer()->get('app.integration_service')->recountIntegrationsBalance();
            $output->writeln('ok');
    }

}