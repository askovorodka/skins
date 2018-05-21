<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;
use AppBundle\Entity\IntegrationReports;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Routing\RouterInterface;

class IntegrationReportsService
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var EntityRepository
     */
    private $entityRepository;

    public function __construct(EntityManager $entityManager, RouterInterface $router)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->entityRepository = $this->entityManager->getRepository(IntegrationReports::class);
    }

    public function getByUser(User $user, $toJson = true)
    {
        $result = $this->entityRepository->getByUser($user);
        return $result;
    }
}
