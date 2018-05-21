<?php
/**
 * Created by PhpStorm.
 * User: ashmits
 * Date: 5/4/17
 * Time: 5:38 PM
 */

namespace Tests\AppBundle\Controller;

use AppBundle\Service\ItemsPriceService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DepositControllerTest extends WebTestCase
{

    public function testPrices()
    {

        $client = static::createClient();
        $container = $client->getContainer();
        //reflection class redisService
        $redistServiceRef = new \ReflectionClass(ItemsPriceService::class);
        //assert if exists constants
        $this->assertArrayHasKey('ITEMS_PRICE_LIST_URL', $redistServiceRef->getConstants());
        $this->assertArrayHasKey('ITEM_PRICE_REDIS_PREFIX', $redistServiceRef->getConstants());
        $this->assertArrayHasKey('PRICE_RATE_REDIS_KEY', $redistServiceRef->getConstants());

        //create redisService
        $redistService = $container->get('app.redis_service');

        //assert if exists key in Db
        $this->assertEquals(1, $redistService->isExists( $redistServiceRef->getConstant('PRICE_RATE_REDIS_KEY') ));

        //assert count keys by mask in Db
        $keysByMask = $redistService->keys($redistServiceRef->getConstant('ITEM_PRICE_REDIS_PREFIX') . '*');
        $this->assertGreaterThan(2, count($keysByMask));

    }
}