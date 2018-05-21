<?php

namespace AppBundle\Command;

use AppBundle\Entity\Deposit;
use AppBundle\Utils\DepositFilterParams;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AppDepositStatusTerminalCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:deposit_status_terminal')
            ->setDescription('move all deposits in status new -> terminal with date_created greater or equal to 24 hours')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $depositService = $this->getContainer()->get('app.deposit_service');
        $loggerService = $this->getContainer()->get('logger');
        $dateAgo24Hours = (time() - 3600 * 24);
        $params = new DepositFilterParams();
        $params->setStatus(Deposit::STATUS_NEW)->setDateTo(new \DateTime(date('Y-m-d H:i:s', $dateAgo24Hours)));
        $limit = 1000;
        $start = time();
        $em = $this->getContainer()->get('doctrine')->getManager();
        while(true)
        {
            try {
                //if time working over 1 hour
                if ((time() - $start) > 3600) {
                    throw new \Exception(__CLASS__ . ' uptime exception');
                }

                $params->setLimit($limit);
                $deposits = $depositService->getByParameters($params);
                if (!empty($deposits))
                {
                    $ids = [];
                    foreach ($deposits as $deposit) {
                        array_push($ids, $deposit['id']);
                        $loggerService->info('move deposit to status ' . Deposit::STATUS_TERMINAL,[
                            'deposit_id'    => $deposit['id'],
                            'trade_hash'    => $deposit['trade_hash'],
                            'order_id'      => $deposit['order_id'],
                        ]);
                    }
                    $em->getRepository(Deposit::class)->updateStatusByIds($ids, Deposit::STATUS_TERMINAL);
                } else {
                    break;
                }
            } catch (\Exception $exception){
                $output->writeln('exception terminal' . $exception->getMessage());
                $loggerService->crit('exception terminal', [
                    __CLASS__,
                    $exception->getMessage(),
                ]);
                break;
            }
        }

    }

}
