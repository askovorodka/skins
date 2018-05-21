<?php
/**
 * Created by PhpStorm.
 * User: ahimas
 * Date: 16.01.17
 * Time: 20:34
 */

namespace AppBundle\Admin;


use AppBundle\Service\IntegrationService;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class IntegrationDebitAdmin extends AbstractAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'created',
    );

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('integration')
            ->add('created')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('created')
            ->add('integration')
            ->add('amount')
            ->add('currency')
            ->add('status', 'choice', [
                'editable' => true,
                'choices' => IntegrationService::getIntegrationDebitStatuses()
            ])
            ->add('updated')
            ->add('payment_system')
            ->add('payment_destination')
        ;
    }

    protected function configureShowFields(ShowMapper $show)
    {
        $show
            ->add('id')
            ->add('integration')
            ->add('amount')
            ->add('currency')
            ->add('status')
            ->add('created')
            ->add('updated')
        ;
    }

    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('integration')
            ->add('amount')
            ->add('currency')
            ->add('status')
        ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('delete');
    }

}