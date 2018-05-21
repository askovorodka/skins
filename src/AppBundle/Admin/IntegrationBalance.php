<?php
/**
 * Created by PhpStorm.
 * User: ahimas
 * Date: 29.03.17
 * Time: 18:06
 */

namespace AppBundle\Admin;


use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class IntegrationBalance extends AbstractAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'id',
    );

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('integration')
            ->add('currency')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('integration')
            ->add('balance')
            ->add('currency')
        ;
    }


    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('integration')
            ->add('balance')
            ->add('currency')

        ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('delete');
        $collection->remove('show');
    }
}