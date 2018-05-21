<?php

namespace AppBundle\Command;

use AppBundle\Service\SteamService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class AppCheckSteamStatusCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        return
            $this->setName('app:check_steam_status')
            ->setDescription('Checking last steam status');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $redisService = $this->getContainer()->get('app.redis_service');
        $host = $this->getContainer()->getParameter('casperjs_host');
        $port = $this->getContainer()->getParameter('casperjs_port');
        try {
            $command = "wget -qO- $host:$port";
            $process = new Process($command);
            $process->run();
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            //set steam status in redis db
            $steamResponse = json_decode($process->getOutput(), true);
            if (!empty($steamResponse['message']) && $steamResponse['message'] !== '…') {
                $redisService->set(SteamService::STEAM_STATUS_REDIS_KEY, $steamResponse['message']);
                $output->writeln(date('Y-m-d H:i:s').' steam status: '.$steamResponse['message']);
            }
        } catch (ProcessFailedException $exception) {
            $output->writeln(__CLASS__.' process_fail_exception: '.$exception->getMessage());
        } catch (\Exception $error) {
            $output->writeln(__CLASS__.' exception: '.$error->getMessage());
        }
    }
}
