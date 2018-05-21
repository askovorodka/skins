<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Integration;
use Doctrine\ORM\EntityRepository;

class IntegrationDebitRepository extends EntityRepository
{
    /**
     * @param Integration $integration
     *
     * @return array
     */
    public function getDebitsByIntegration(Integration $integration)
    {
        $qb = $this->createQueryBuilder('d');
        $qb
            ->select('SUM(d.amount) as debit')
            ->addSelect('d.currency')
            ->indexBy('d', 'd.currency')
            ->where('d.integration = :integration')
            ->groupBy('d.currency')
            ->setParameter('integration', $integration)
        ;

        return $qb->getQuery()->getResult();
    }

    public function getDebitsListByIntegration(Integration $integration)
    {
        $qb = $this->createQueryBuilder('d');
        $qb
            ->select('d.id as id')
            ->addSelect('d.amount as amount')
            ->addSelect('d.currency as currency')
            ->addSelect('d.status as status')
            ->addSelect('d.created')
            ->where('d.integration = :integration')
            ->setParameter('integration', $integration)
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * method delete integration_debit rows by integration.
     *
     * @param Integration $integration
     *
     * @return mixed
     */
    public function deleteByIntegration(Integration $integration)
    {
        $query = $this->createQueryBuilder('id');
        $query
            ->delete('AppBundle:IntegrationDebit', 'ib')
            ->where('ib.integration = :integration')
            ->setParameter('integration', $integration->getId());

        return $query->getQuery()->execute();
    }
}
