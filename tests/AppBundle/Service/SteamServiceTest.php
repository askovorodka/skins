<?php
namespace Tests\AppBundle\Service;
use AppBundle\Exception\InvalidTradeUrlException;
use AppBundle\Service\SteamService;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

/**
 * Created by PhpStorm.
 * User: ahimas
 * Date: 07.09.16
 * Time: 14:28
 */
class SteamServiceTest extends TestCase
{
    /**
     * @var SteamService
     */
    private $steamService;

    private $inventoryJSON;

    public function setUp() {
        $this->steamService = $this->getMockBuilder(SteamService::class)->disableOriginalConstructor();
        $this->inventoryJSON = json_decode(file_get_contents(__DIR__ . "/../../fixtures/inventory.json"), true);
    }

    /**
     * @param $tradeUrl
     * @param $expectedSteamId
     * @throws \Exception
     *
     * @dataProvider tradeUrlProvider
     */
    public function testGetSteamIdFromValidTradeUrl($tradeUrl, $expectedSteamId)
    {
        $actualSteamId = SteamService::getSteamIdFromTradeUrl($tradeUrl);
        $this->assertEquals($expectedSteamId, $actualSteamId, 'actual steam id does not match expected!');
    }

    public function tradeUrlProvider()
    {
        return [
            ['https://steamcommunity.com/tradeoffer/new/?partner=296658040', 76561198256923768],
            ['https://steamcommunity.com/tradeoffer/new/?partner=23524818', 76561197983790546],
            ['https://steamcommunity.com/tradeoffer/new/?partner=8866148', 76561197969131876],
        ];
    }

    /**
     * @throws InvalidTradeUrlException
     */
    public function testGetSteamIdFromInvalidUrl()
    {
        $badTradeUrl = 'https://steamcommunity.com/tradeoffe/new/?parner=296658040000';
        try {
            SteamService::getSteamIdFromTradeUrl($badTradeUrl);
        } catch (InvalidTradeUrlException $e) {
            return;
        }
        $this->fail('should throw InvalidTradeUrlException on bad trade url');
    }

    public function testPrepareInventory()
    {

    }


}