<?php

namespace AppBundle\Service;

use AppBundle\Entity\IntegrationDebit;
use AppBundle\Entity\Integration;
use AppBundle\Entity\IntegrationBalance;
use AppBundle\Exception\NotEnoughBalanceException;
use Doctrine\ORM\EntityManager;

class DebitService
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var NotificationService
     */
    private $notificationService;

    /**
     * @var IntegrationService
     */
    private $integrationService;

    /**
     * @param EntityManager       $entityManager
     * @param IntegrationService  $integrationService
     * @param NotificationService $notificationService
     */
    public function __construct(EntityManager $entityManager, IntegrationService $integrationService, NotificationService $notificationService)
    {
        $this->entityManager = $entityManager;
        $this->notificationService = $notificationService;
        $this->integrationService = $integrationService;
    }

    /**
     * @param Integration $integration
     * @param $amount
     * @param $currency
     *
     * @return IntegrationDebit
     *
     * @throws NotEnoughBalanceException
     */
    public function createIntegrationDebit(Integration $integration, $amount, $currency, $note, $payment_system = null, $payment_destination = null)
    {
        /** @var IntegrationBalance $integrationBalance */
        $integrationBalance = $this->integrationService->getOrCreateIntegrationBalance($integration, $currency);

        if ($integrationBalance->getBalance() < $amount) {
            throw new NotEnoughBalanceException('Недостаточно средств');
        }

        $debit = new IntegrationDebit($integration, $currency, $amount, $note, $payment_system, $payment_destination);
        $this->entityManager->persist($debit);
        $this->entityManager->flush($debit);

        return $debit;
    }
}
