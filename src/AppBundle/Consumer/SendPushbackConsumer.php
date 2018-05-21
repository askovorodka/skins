<?php
/**
 * Created by PhpStorm.
 * User: ahimas
 * Date: 21.11.16
 * Time: 14:43
 */

namespace AppBundle\Consumer;


use AppBundle\Exception\DepositPushBackException;
use AppBundle\Service\Deposit\DepositService;
use AppBundle\Service\IntegrationService;
use Monolog\Logger;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use OldSound\RabbitMqBundle\RabbitMq\Producer;
use PhpAmqpLib\Message\AMQPMessage;

class SendPushbackConsumer implements ConsumerInterface
{
    const MAX_RETRY_COUNT = 10;
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var DepositService
     */
    private $depositService;

    /**
     * @var Producer
     */
    private $retryProducer;

    /**
     * @var IntegrationService
     */
    private $integrationService;

    /**
     * @param Logger $logger
     * @param DepositService $depositService
     * @param IntegrationService $integrationService
     * @param Producer $retryProducer
     */
    public function __construct(Logger $logger, DepositService $depositService, IntegrationService $integrationService, Producer $retryProducer)
    {
        $this->logger = $logger;
        $this->depositService = $depositService;
        $this->integrationService = $integrationService;
        $this->retryProducer = $retryProducer;
    }

    public function execute(AMQPMessage $msg)
    {
        $this->logger->critical('wow', [$msg->getBody()]);
        $data = json_decode($msg->getBody(), true);
        $data['retry_count'] += 1;
        $msg->setBody(json_encode($data));
        try {
            $retryCount = $data['retry_count'] ?? 0;
            if ($retryCount >= self::MAX_RETRY_COUNT) {
                $this->logger->alert('pushback max retry count reached', $data);
                return true;
            }
            $deposit = $this->depositService->getDepositById($data['deposit_id']);
            $result = $this->integrationService->sendPushBack($deposit);
            if (!$result) {
                $this->retryProducer->publish(json_encode($data), 'pushback.retry');
            }
            return true;
        } catch(DepositPushBackException $e) {
            $this->logger->alert($e->getMessage(), $data);
            return true;
        }
    }

}