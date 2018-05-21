<?php
/**
 * Created by PhpStorm.
 * User: ahimas
 * Date: 06.10.16
 * Time: 18:45
 */

namespace Tests\AppBundle\Service;


use AppBundle\DTO\InventoryItem;
use AppBundle\Entity\Deposit;
use AppBundle\Service\Deposit\DepositItemsCheckerService;

use AppBundle\Service\ItemsPriceService;
use AppBundle\Service\RedisService;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class DepositItemsCheckerServiceTest extends TestCase
{
//    /**
//     * @var DepositItemsCheckerService
//     */
//    private $service;
//
//    public function setUp()
//    {
//        $itemsPriceServiceMock = $this->getMockBuilder(ItemsPriceService::class)
//            ->disableOriginalConstructor()
//            ->getMock()
//            ->method('calculateInventoryValue')->withAnyParameters()->willReturn(100)
//            ->method('calculateDepositNoTaxValue')->withAnyParameters()->willReturn(135)
//        ;
//        $loggerMock = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();
//        $redisMock = $this->getMockBuilder(RedisService::class)
//            ->disableOriginalConstructor()->getMock()
//            ->method('getJsonByKey')->withAnyParameters()->willReturn(['id' => 1, 'market_hash_name' => 'dragon_lore'])
//        ;
//        $this->service = $this->getMockBuilder(DepositItemsCheckerService::class)
//            ->setConstructorArgs([$itemsPriceServiceMock, $loggerMock, $redisMock])
//            ->getMock()
//        ;
//    }
//
//    /**
//     * @param array $items
//     * @param $expectedFinalItems
//     * @dataProvider itemsProvider
//     */
//    public function testFilterItemsAndRecountValueNotThrow($items, $expectedFinalItems)
//    {
//        $deposit = (new Deposit())
//            ->setId(1)
//            ->setNoTaxValue(135)
//            ->setValue(100)
//            ->setItems(json_encode(['1' => ['market_hash_name' => 'dragon_lore', 'value' => 100]]))
//        ;
//        $filteredDeposit = $this->service->filterItemsAndRecountValue($deposit, $items);
//        var_dump($filteredDeposit);die;
//        $this->assertJsonStringEqualsJsonString($expectedFinalItems, $filteredDeposit->getItems(), "Items should be filtered properly");
//        var_dump($deposit->getItems());die;
//    }
//
//    public function itemsProvider()
//    {
//        $finalItems[1] = new InventoryItem(['id' => 1, 'market_hash_name' => 'dragon_lore']);
//        return [
//            [['1' => 'dragon_lore'], json_encode($finalItems)],
//            [['1' => 'drugoe_govno'], json_encode([])],
//            [['1' => ''], json_encode([])],
//            [[], json_encode([])],
//        ];
//    }
}
