<?php

namespace AppBundle\Service\Deposit;

use AppBundle\DTO\InventoryItem;
use AppBundle\Entity\Deposit;
use AppBundle\Entity\Integration;
use AppBundle\Exception\DepositPushBackException;
use AppBundle\Service\IntegrationService;
use AppBundle\Service\ItemsPriceService;
use AppBundle\Service\NotificationService;
use AppBundle\Service\RedisService;
use AppBundle\Service\SteamService;
use AppBundle\Utils\DepositFilterParams;
use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use OldSound\RabbitMqBundle\RabbitMq\Producer;
use Psr\Log\LoggerInterface;

/**
 * Class DepositService.
 */
class DepositService
{
    const DEPOSIT_REDIS_KEY = 'deposit';
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var IntegrationService
     */
    private $integrationService;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var ItemsPriceService
     */
    private $itemsPriceService;

    /**
     * @var Producer
     */
    private $pushbackProducer;

    /**
     * @var NotificationService
     */
    private $notificationService;

    /**
     * @var DepositItemsCheckerService
     */
    private $depositCheckerService;

    /**
     * @var RedisService
     */
    private $redisService;

    /**
     * @param EntityManager              $entityManager
     * @param IntegrationService         $integrationService
     * @param ItemsPriceService          $itemsPriceService
     * @param LoggerInterface            $logger
     * @param Producer                   $pushbackProducer
     * @param NotificationService        $notificationService
     * @param DepositItemsCheckerService $depositCheckerService
     */
    public function __construct(EntityManager $entityManager,
                                IntegrationService $integrationService,
                                ItemsPriceService $itemsPriceService,
                                LoggerInterface $logger,
                                Producer $pushbackProducer,
                                NotificationService $notificationService,
                                DepositItemsCheckerService $depositCheckerService,
                                RedisService $redisService
    ) {
        $this->entityManager = $entityManager;
        $this->integrationService = $integrationService;
        $this->itemsPriceService = $itemsPriceService;
        $this->logger = $logger;
        $this->pushbackProducer = $pushbackProducer;
        $this->notificationService = $notificationService;
        $this->depositCheckerService = $depositCheckerService;
        $this->redisService = $redisService;
    }

    public function save(Deposit $deposit) {
        return $this->entityManager->getRepository(Deposit::class)->save($deposit);
    }

    /**
     * @param $depositId
     * @param $items
     *
     * @throws DepositPushBackException
     */
    public function confirmTrade($depositId, $items)
    {
        $deposit = $this->entityManager->getRepository(Deposit::class)->find($depositId);
        if (!$deposit) {
            throw new DepositPushBackException("Deposit not found by id: $depositId");
        }
        $deposit = $this->depositCheckerService->filterItemsAndRecountValue($deposit, $items, true);
        $deposit->setStatus(Deposit::STATUS_COMPLETED);
        $this->redisService->delete(DepositItemsCheckerService::DEPOSIT_ITEMS_REDIS_KEY.$deposit->getId());
        $this->entityManager->flush($deposit);
        $this->pushbackProducer->publish(json_encode(['deposit_id' => $deposit->getId(), 'retry_count' => 0]), 'pushback');
    }

    /**
     * @param $depositId
     * @param $errorMessage
     */
    public function declineTrade($depositId, $errorMessage)
    {
        $deposit = $this->entityManager->getRepository(Deposit::class)->find($depositId);
        $deposit
            ->setStatus(Deposit::STATUS_ERROR_BOT)
            ->setNote(substr((string) $errorMessage,0,255));
        $this->entityManager->flush($deposit);
    }

    /**
     * @param $integration
     * @param $orderId
     * @param $tradeUrl
     * @param $currency
     *
     * @return Deposit
     *
     * @throws \AppBundle\Exception\InvalidTradeUrlException
     */
    public function newDeposit($integration, $orderId, $tradeUrl, $currency)
    {
        $steamId = SteamService::getSteamIdFromTradeUrl($tradeUrl);
        $deposit = $this->entityManager->getRepository(Deposit::class)->findOneBy(['integration' => $integration, 'orderId' => $orderId, 'status' => Deposit::STATUS_NEW]);
        if ($deposit === null) {
            $deposit = new Deposit();
            $deposit->setIntegration($integration)
                ->setOrderId($orderId)
                ->setStatus(Deposit::STATUS_NEW)
                ->setSteamId($steamId)
                ->setTradeUrl($tradeUrl)
                ->setCreated(new \DateTime('now'))
                ->setCurrency($currency);
            $this->entityManager->persist($deposit);
            $this->entityManager->flush($deposit);
        }

        return $deposit;
    }

    /**
     * @param Deposit         $deposit
     * @param InventoryItem[] $items
     *
     * @return Deposit
     *
     * @throws \Exception
     */
    public function updateDeposit(Deposit $deposit, array $items)
    {
        $deposit = $this->depositCheckerService->filterItemsAndRecountValue($deposit, $items, true);
        $deposit
            ->setStatus(Deposit::STATUS_NEW)
            ->setTradeHash(strtoupper(substr(md5(uniqid() . $deposit->getOrderId()), 0, 21)))
        ;

        $this->entityManager->persist($deposit);
        $this->entityManager->flush($deposit);

        $this->logger->info('Deposit updated', [$deposit->getId(), $deposit->getItems(), $deposit->getValue(), $deposit->getNoTaxValue()]);

        return $deposit;
    }

    /**
     * @param $depositId
     *
     * @return array
     */
    public function getDepositStatus($depositId)
    {
        $deposit = $this->entityManager->getRepository(Deposit::class)->find($depositId);
        if ($deposit === null) {
            return ['status' => 'fail', ['message' => 'deposit not found by id'.$depositId]];
        }

        $action = 'show_pending';
        if ($deposit->getStatus() === Deposit::STATUS_COMPLETED) {
            $action = 'show_success';
        } elseif (!in_array($deposit->getStatus(), [Deposit::STATUS_COMPLETED, Deposit::STATUS_PENDING])) {
            $action = 'show_fail';
        }

        return [
            'status' => 'success',
            'deposit_status' => $deposit->getStatus(),
            'action' => $action,
            'amount' => $deposit->getValue(),
            'currency' => $deposit->getCurrency(),
        ];
    }

    /**
     * @param $id
     *
     * @return Deposit
     */
    public function getDepositById($id)
    {
        return $this->entityManager->getRepository(Deposit::class)->find($id);
    }

    public function getDepositByIntegrationAndId(Integration $integration, $id)
    {
        return $this->entityManager->getRepository(Deposit::class)->findOneBy(['integration' => $integration, 'id' => $id]);
    }

    public function findLostPushBacks()
    {
        $deposits = $this->entityManager->getRepository(Deposit::class)->getByLostPushBacks();
        if (!empty($deposits)) {
            $this->notificationService->notifyLostPushbacks($this->getDepositsIds($deposits));
        }
        foreach ($deposits as $depositArray) {
            $deposit = $this->entityManager->getRepository(Deposit::class)->find($depositArray['id']);
            $this->integrationService->sendPushBack($deposit);
        }

        return count($deposits);
    }

    /**
     * @param array $deposits
     *
     * @return string
     */
    private function getDepositsIds(array $deposits)
    {
        $ids = [];
        foreach ($deposits as $deposit) {
            $ids[] = $deposit['id'];
        }

        return implode(',', $ids);
    }

    /**
     * method create new deposit entity.
     *
     * @param Deposit $deposit
     *
     * @return Deposit
     */
    public function create(Deposit $deposit): Deposit
    {
        $this->entityManager->persist($deposit);

        return $deposit;
    }

    /**
     * get last rows.
     *
     * @param $limit
     *
     * @return array
     */
    public function getLast(int $limit = 100)
    {
        return $this->entityManager->getRepository(Deposit::class)->findBy([], ['id' => 'desc'], $limit);
    }

    /**
     * method call findBy of reposoitory class.
     *
     * @param array $criteria
     * @param array $sort
     *
     * @return array
     */
    public function findBy(array $criteria = [], array $sort = ['id' => 'desc'])
    {
        return $this->entityManager->getRepository(Deposit::class)->findBy($criteria, $sort);
    }

    /**
     * delete single entity.
     *
     * @param Deposit $deposit
     */
    public function remove(Deposit $deposit)
    {
        $this->entityManager->remove($deposit);
        $this->entityManager->flush();
    }

    public function deleteByIntegration(Integration $integration)
    {
        return $this->entityManager->getRepository(Deposit::class)->deleteByIntegration($integration);
    }

    /**
     * method find deposits by parameters
     * @param DepositFilterParams $params
     * @return array
     */
    public function getByParameters(DepositFilterParams $params) {
        return $this->entityManager->getRepository(Deposit::class)->getDepositsByParameters($params);
    }

    /**
     * get count deposits by parameters
     * @param DepositFilterParams $params
     * @return mixed
     */
    public function getCount(DepositFilterParams $params) {
        return $this->entityManager->getRepository(Deposit::class)->getCount($params);
    }

}
