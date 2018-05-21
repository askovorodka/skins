<?php
/**
 * Created by PhpStorm.
 * User: ahimas
 * Date: 20.12.16
 * Time: 15:41.
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Deposit;
use AppBundle\Entity\Integration;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use AppBundle\Forms\AdminStatisticsType;

class StatisticsAdminController extends CRUDController
{
    public function listAction()
    {
        $request = $this->getRequest();
        $filterInput = $request->query->all()['admin_statistics'] ?? null;
        $dateFrom = new \DateTime((!empty($filterInput['date_from']) ? $filterInput['date_from'] : '-1 month'));
        $dateTo = new \DateTime((!empty($filterInput['date_to']) ? $filterInput['date_to'] : 'now'));
        $currency = $filterInput['currency'] ?? null;
        $groupBy = $filterInput['group_by'] ?? 'day';
        $integrations = $this->get('app.integration_service')->getAllIntegrations();
        $integrationsChoices = ['all' => null];
        foreach ($integrations as $integration) {
            $integrationsChoices[$integration->getName()] = $integration;
        }
        
        $integration = null;
        if (!empty($filterInput['integration'])) {
            $integration = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Integration')
                ->find($filterInput['integration']);
        }

        $form = $this->createForm(AdminStatisticsType::class, null, ['data' => [
            'dateFrom'  => $dateFrom,
            'dateTo'    => $dateTo,
            'groupBy'   => $groupBy,
            'integration'   => $integration,
            'currency'  => $currency,
        ]]);
        $form->handleRequest($this->getRequest());

        $statisticsQuery = $this->get('app.stats_service')->getStats($dateFrom, $dateTo, $groupBy, $integration, $currency);
        $statisticsData = $statisticsQuery->getResult();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $statisticsData, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            30/*limit per page*/
        );

        return $this->render(':admin/statistics:statistics.html.twig', [
            'pagination' => $pagination,
            'form' => $form->createView(),
        ]);
    }
}
