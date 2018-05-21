<?php
namespace Tests\AppBundle\Utils;

use AppBundle\Service\ItemsPriceService;
use AppBundle\Utils\ItemPriceDota;
use AppBundle\Utils\ItemPriceStrategy;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class ItemPriceTest extends WebTestCase
{

    public function testItemPriceDota()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $redisService = $container->get('app.redis_service');
        $all = $redisService->hgetall(ItemsPriceService::DOTA_WHITE_LIST_REDIS_KEY);
        $this->assertTrue(count($all)>0);

        foreach ($all as $market_name => $itemArray)
        {
            $json = $redisService->hget(ItemsPriceService::DOTA_WHITE_LIST_REDIS_KEY, $market_name);
        }
        die;

        $skinsJson = file_get_contents($container->get('kernel')->getRootDir() .'/../web/uploads/dota_skins.json');
        $skinsJson = json_decode($skinsJson, true);
        foreach ($skinsJson['data'] as $market_name => $item)
        {
            $itemStrategy = new ItemPriceStrategy(new ItemPriceDota($item));
            $price = $itemStrategy->calculatePrice();
            $comission = $itemStrategy->calculateComission();
            if ($market_name == "dota1") {
                $this->assertEquals(0.75, $comission, "not actual comission dota1, actual: 0.75");
            }
            if ($market_name == "dota2") {
                $this->assertEquals(0.35, $comission, "not actual comission dota2, actual: 0.35");
            }
            if ($market_name == "dota3") {
                $this->assertEquals(0.75, $comission, "not actual comission dota3, actual: 0.75");
            }
            if ($market_name == "dota4") {
                $this->assertEquals(-1, $comission, "not actual comission dota4, actual: -1");
            }
            if ($market_name == "dota5") {
                $this->assertEquals(0.55, $comission, "not actual comission dota5, actual: 0.55");
            }
        }

    }

}