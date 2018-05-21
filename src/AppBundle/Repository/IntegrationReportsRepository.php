<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;

/**
 * IntegrationReportsRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class IntegrationReportsRepository extends \Doctrine\ORM\EntityRepository
{
    public function getByUser(User $user)
    {
        return $this->findBy(['user' => $user], ['id' => 'DESC']);
    }
}