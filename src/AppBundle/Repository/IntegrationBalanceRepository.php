<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Integration;
use Doctrine\ORM\EntityRepository;

class IntegrationBalanceRepository extends EntityRepository
{
    /**
     * method delete integration_balance rows by integration.
     *
     * @param Integration $integration
     *
     * @return mixed
     */
    public function deleteByIntegration(Integration $integration)
    {
        $query = $this->createQueryBuilder('ib');
        $query
            ->delete('AppBundle:IntegrationBalance', 'ib')
            ->where('ib.integration = :integration')
            ->setParameter('integration', $integration->getId());

        return $query->getQuery()->execute();
    }
}
