<?php

namespace AppBundle\Service;

use AppBundle\Entity\Integration;
use AppBundle\Entity\IntegrationBalance;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class IntegrationBalanceService
{
    /**
     * @var EntityRepository
     */
    private $entityRepository;

    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->entityRepository = $this->entityManager->getRepository(IntegrationBalance::class);
    }

    public function deleteByIntegration(Integration $integration)
    {
        return $this->entityRepository->deleteByIntegration($integration);
    }
}
