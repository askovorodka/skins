<?php

namespace AppBundle\Service;

use AppBundle\Entity\Integration;
use AppBundle\Entity\IntegrationDebit;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class IntegrationDebitService
{
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var EntityRepository
     */
    private $entityRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->entityRepository = $this->entityManager->getRepository(IntegrationDebit::class);
    }

    public function deleteByIntegration(Integration $integration)
    {
        return $this->entityRepository->deleteByIntegration($integration);
    }
}
