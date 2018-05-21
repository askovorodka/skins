<?php
/**
 * Created by PhpStorm.
 * User: ahimas
 * Date: 20.12.16
 * Time: 15:54
 */

namespace AppBundle\Admin;


use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Route\RouteCollection;

class StatisticsAdmin extends AbstractAdmin
{
    protected $baseRoutePattern = 'statistics';
    protected $baseRouteName = 'statistics';

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['list']);
    }
}