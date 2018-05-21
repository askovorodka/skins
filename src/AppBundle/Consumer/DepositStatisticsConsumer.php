<?php

namespace AppBundle\Consumer;

use AppBundle\Entity\Deposit;
use AppBundle\Entity\Integration;
use AppBundle\Entity\User;
use AppBundle\Entity\IntegrationReports;
use AppBundle\Service\Deposit\DepositService;
use AppBundle\Service\IntegrationService;
use AppBundle\Utils\DepositFilterParams;
use AppBundle\Utils\StatisticFilterParams;
use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Filesystem\Filesystem;

class DepositStatisticsConsumer implements ConsumerInterface
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var DepositService
     */
    private $depositService;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var IntegrationService
     */
    private $integrationService;

    private $reports_path;

    public function __construct(
        Logger $logger,
        DepositService $depositService,
        IntegrationService $integrationService,
        EntityManager $entityManager,
        $reports_path
    ) {
        $this->logger = $logger;
        $this->depositService = $depositService;
        $this->integrationService = $integrationService;
        $this->entityManager = $entityManager;
        $this->reports_path = $reports_path;
    }

    public function execute(AMQPMessage $message)
    {
        try {
            /**
             * @var DepositFilterParams
             */
            $filterParams = unserialize($message->getBody());

            if (!$filterParams instanceof DepositFilterParams) {
                throw new \Exception('filter parameters not found');
            }

            if (!$filterParams->getIntegrationId()) {
                throw new \Exception('parameter integrationId is empty');
            }

            $integration = $this->entityManager->getRepository(Integration::class)->find($filterParams->getIntegrationId());

            if (!$integration instanceof Integration) {
                throw new \Exception('integration not exists');
            }

            $depositRepository = $this->entityManager->getRepository(Deposit::class);
            $filterParams->setIntegration($integration);

            $countRows = $depositRepository->getCount($filterParams);
            $writer = new \XLSXWriter();

            if (!empty($countRows)) {
                $offset = 0;
                $limit = 5000;
                $filterParams->setLimit($limit);
                $fs = new Filesystem();
                $filePath = $this->reports_path;
                $fs->mkdir($filePath);
                $basename = sha1(rand(1, 10000).time());
                $filename_xlsx = $basename . '.xlsx';

                $writer->writeSheetHeader('Sheet1', [
                    'СУММА' => 'price',
                    'НОМЕР' => 'string',
                    'СТАТУС' => 'string',
                    'ДАТА' => 'date',
                    'ID' => 'string',
                    'ORDER ID' => 'string',
                    'STEAM ID' => 'string',
                    'TRADE OFFER ID' => 'string',
                ]);

                while (true) {
                    try {
                        $filterParams->setOffset($offset);
                        $deposits = $depositRepository->getDepositsByParameters($filterParams);
                        if (!empty($deposits)) {
                            foreach ($deposits as $depositItem)
                            {
                                $row = [
                                    $depositItem['value'],
                                    $depositItem['trade_hash'],
                                    $depositItem['status'],
                                    $depositItem['created'],
                                    $depositItem['id'],
                                    $depositItem['order_id'],
                                    $depositItem['steam_id'] ?? 0,
                                    $depositItem['trade_offer_id'],
                                ];
                                $writer->writeSheetRow('Sheet1', $row);
                            }
                        }

                        $offset += $limit;
                        if ($offset > $countRows) {
                            break;
                        }
                    } catch (\Exception $exception) {
                        break;
                    }
                }

                $writer->writeToFile($this->reports_path . $filename_xlsx);

                $this->saveFileInfo($filterParams, $filename_xlsx);
            }


            return true;
        } catch (\Exception $exception) {
            $this->logger->alert('deposit statistics consumer', [$exception->getMessage()]);

            return true;
        }
    }

    private function saveFileInfo(DepositFilterParams $filterParams, $filename)
    {
        try {
            $filePath = $this->reports_path;
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['integration' => $filterParams->getIntegrationId()]);
            $integration = $this->entityManager->getRepository(Integration::class)->find($filterParams->getIntegrationId());
            $integrationReports = new IntegrationReports();
            $integrationReports
                ->setIntegration($integration)
                ->setUser($user)
                ->setCreated(new \DateTime())
                ->setFile($filename)
                ->setFileSize(filesize($filePath.$filename))
                ->setFilterParams(json_encode($filterParams))
                ->setFileType(IntegrationReports::FILE_TYPE_CSV);

            $this->entityManager->persist($integrationReports);
            $this->entityManager->flush();
            $this->logger->info(date('Y-m-d H:i').' csv file generated ', [$integrationReports->getFile(), $integrationReports->getIntegration()->getId()]);
        } catch (\Exception $exception) {
            $this->logger->crit('deposit_consumer exception: ', [$exception->getMessage()]);
        }
    }
}
