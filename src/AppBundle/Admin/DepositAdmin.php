<?php
/**
 * Created by PhpStorm.
 * User: ahimas
 * Date: 24.09.16
 * Time: 16:43
 */

namespace AppBundle\Admin;


use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;


class DepositAdmin extends AbstractAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'created',
    );

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('steamId')
            ->add('orderId')
            ->add('tradeHash')
            ->add('integration')
            ->add('status')
            ->add('pushStatus')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('created')
            ->add('tradeHash')
            ->add('tradeUrl')
            ->add('steamId')
            ->add('orderId')
            ->add('integration')
            ->add('value')
            ->add('noTaxValue')
            ->add('currency')
            ->add('status')
            ->add('note')
            ->add('tradeOfferId')
            ->add('pushStatus')
            ->add('pushbackCreated')
            ->add('_action', 'actions', [
                'actions' => [
                    'sendPushBackAction' => [
                        'template' => 'DepositAdmin/list__action_sendPushBack.html.twig'
                    ],
                ]
            ])
        ;
    }

    protected function configureShowFields(ShowMapper $show)
    {
        $show
            ->add('id')
            ->add('integration.name')
            ->add('steamId')
            ->add('tradeUrl')
            ->add('orderId')
            ->add('status')
            ->add('note')
            ->add('tradeHash')
            ->add('currency')
            ->add('tradeOfferId')
            ->add('pushbackCreated')
            ->add('pushStatus')
            ->add('items', null, [
                'template' => 'DepositAdmin/show_deposit_items.html.twig'
            ])
        ;
    }


    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('sendPushBack', $this->getRouterIdParameter().'/sendPushBack');
        $collection->remove('edit');
        $collection->remove('create');
    }

}