<?php
/**
 * Created by PhpStorm.
 * User: ahimas
 * Date: 24.09.16
 * Time: 14:14
 */

namespace AppBundle\Admin;


use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class IntegrationAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('pushbackUrl')
            ->add('homeUrl')
            ->add('logoUrl')
            ->add('isWhitelabel')
            ->add('successUrl')
            ->add('privateKey')
            ->add('publicKey')
            ->add('valueTaxPercent', 'number', [
                'attr' => ['min' => 1, 'max' => 100],
                'required' => true
            ])
            ->add('integrationTaxPercent', 'number', [
                'attr' => ['min' => 1, 'max' => 100],
                'required' => true
            ])
            ->add('httpAuthUsername', null, [
                'required' => false
            ])
            ->add('httpAuthPassword', null, [
                'required' => false
            ])
        ;

    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('created')
            ->add('pushbackUrl')
            ->add('publicKey')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('pushbackUrl')
            ->add('successUrl')
            ->add('publicKey')
            ->add('created')
        ;
    }
}