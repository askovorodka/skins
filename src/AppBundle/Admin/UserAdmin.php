<?php
/**
 * Created by PhpStorm.
 * User: ahimas
 * Date: 16.01.17
 * Time: 19:52
 */

namespace AppBundle\Admin;


use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class UserAdmin extends AbstractAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'id',
    );

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('username')
            ->add('email')
            ->add('integration.name')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('username')
            ->add('email')
            ->add('integration')
            ->add('balance')
            ->add('enabled', null, [
                'editable' => true
            ])
        ;
    }

    protected function configureShowFields(ShowMapper $show)
    {
        $show
            ->add('id')
            ->add('integration.name')
            ->add('email')
            ->add('username')
        ;
    }

    protected function configureFormFields(FormMapper $form)
    {
        $container = $this->getConfigurationPool()->getContainer();
        $roles = $container->getParameter('security.role_hierarchy.roles');

        $rolesChoices = self::flattenRoles($roles);

        $form
            ->add('integration', null, [
                'required' => false
            ])
            ->add('email')
            ->add('username')
            ->add('enabled')
            ->add('roles', 'choice', array(
                    'choices'  => $rolesChoices,
                    'multiple' => true
                )
            )
            ->add('plainPassword', 'repeated', [
                'type' => 'password',
                'required' => false,
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array('label' => 'form.password'),
                'second_options' => array('label' => 'form.password_confirmation'),
                'invalid_message' => 'fos_user.password.mismatch',
            ])
        ;
    }

    protected static function flattenRoles($rolesHierarchy)
    {
        $flatRoles = array();
        foreach($rolesHierarchy as $roles) {

            if(empty($roles)) {
                continue;
            }

            foreach($roles as $role) {
                if(!isset($flatRoles[$role])) {
                    $flatRoles[$role] = $role;
                }
            }
        }

        return $flatRoles;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('delete');
    }

}