<?php
/**
 * Created by PhpStorm.
 * User: ahimas
 * Date: 06.12.16
 * Time: 13:04
 */

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class AppCheckNotAcceptedPushbacksCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:check_pushbacks')
            ->setDescription('Checks for lost pushbacks and notify to slack')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $depositsCount = $this->getContainer()->get('app.deposit_service')->findLostPushBacks();
        $output->writeln("ok. Deposits with lost pushback count $depositsCount");
    }
}