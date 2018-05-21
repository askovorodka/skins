<?php
/**
 * Created by PhpStorm.
 * User: ahimas
 * Date: 07.09.16
 * Time: 14:04.
 */

namespace AppBundle\Service;

use AppBundle\Controller\ApiController;
use AppBundle\DTO\InventoryItem;
use AppBundle\Entity\Deposit;
use AppBundle\Exception\InvalidTradeUrlException;
use AppBundle\Exception\SteamInventoryLoadException;
use AppBundle\Exception\TradeOfferException;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use Monolog\Logger;

/**
 * Class SteamService.
 */
class SteamService
{
    const TRADEURL_REGEX = '/^https?\:\/\/steamcommunity\.com\/tradeoffer\/new\/\?partner=([0-9]+)\&token=(.*?)$/';
    const STEAMID_DELTA = 76561197960265728;
    const INVENTORY_LOAD_URL = 'http://steamcommunity.com/profiles/'.self::INVENTORY_LOAD_URL_STEAM_ID_REPLACE.'/inventory/json/730/2';
    const INVENTORY_LOAD_URL_STEAM_ID_REPLACE = '<%steam_id%>';
    const STEAM_TRADE_OFFER_URL = 'https://steamcommunity.com/tradeoffer/';
    const BOT_RESPONSE_SUCCESS = 'success';
    const BOT_RESPONSE_FAIL = 'failed';

    const BOT_RESPONSE_ESCROW = 'escrow';
    const BOT_RESPONSE_INVALID_TRADE_URL = 'invalid_trade_url';
    const BOT_RESPONSE_PRIVATE_INVENTORY = 'private_inventory';
    const BOT_RESPONSE_BLOCKED_OR_NOT_AVAILABLE = 'blocked_or_not_available';

    const BOT_TRADE_OFFER_URL = 'tradeoffer';
    const BOT_INVENTORY_URL = 'inventory';

    const IS_STEAM_RIP_URL = 'http://is.steam.rip/api/v1/?request=IsSteamRip';
    const STEAM_STATUS_REDIS_KEY = 'steam_status';

    const STEAM_STATUS_DELAYED = 'Delayed';
    const STEAM_STATUS_NORMAL = 'Normal';
    const STEAM_STATUS_CRITICAL = 'Critical';

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var string
     */
    private $botUrl;

    /**
     * @var string
     */
    private $botKey;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var RedisService
     */
    private $redisService;

    public function __construct(EntityManager $entityManager, $botUrl, $botKey, Logger $logger, RedisService $redisService)
    {
        $this->entityManager = $entityManager;
        $this->botUrl = $botUrl;
        $this->botKey = $botKey;
        $this->logger = $logger;
        $this->redisService = $redisService;
    }

    /**
     * @param Deposit $deposit
     * @param string  $locale
     *
     * @return array
     *
     * @throws \Exception
     */
    public function loadInventoryByTradeUrl(Deposit $deposit, $locale)
    {
        try {
            $result = $this->askBotToLoadInventory($deposit, $locale);

            $status = $result['status'] ?? self::BOT_RESPONSE_FAIL;
            if ($status == self::BOT_RESPONSE_SUCCESS) {
                return $result['data'];
            }
            $error = $result['err'] ?? '';

            if (strtolower($error) == self::BOT_RESPONSE_ESCROW) {
                $deposit
                    ->setStatus(Deposit::STATUS_ERROR_INVENTORY_LOAD)
                    ->setNote(substr((string) ($result['err']),0,255));
                throw new SteamInventoryLoadException(self::BOT_RESPONSE_ESCROW);
            }
            if (strtolower($error) == self::BOT_RESPONSE_BLOCKED_OR_NOT_AVAILABLE) {
                $deposit
                    ->setStatus(Deposit::STATUS_ERROR_INVENTORY_LOAD)
                    ->setNote(substr((string) ($result['err']),0,255));
                throw new SteamInventoryLoadException(self::BOT_RESPONSE_BLOCKED_OR_NOT_AVAILABLE);
            }
            if (strtolower($error) == self::BOT_RESPONSE_INVALID_TRADE_URL) {
                $deposit
                    ->setStatus(Deposit::STATUS_ERROR_INVENTORY_LOAD)
                    ->setNote(substr((string) ($result['err']),0,255));
                throw new SteamInventoryLoadException(self::BOT_RESPONSE_INVALID_TRADE_URL);
            }
            if (strtolower($error) == self::BOT_RESPONSE_PRIVATE_INVENTORY) {
                $deposit
                    ->setStatus(Deposit::STATUS_ERROR_INVENTORY_LOAD)
                    ->setNote(substr((string) ($result['err']),0,255));
                throw new SteamInventoryLoadException(self::BOT_RESPONSE_PRIVATE_INVENTORY);
            }

            $this->logger->critical('failed to load inventory', [$deposit, $result]);
            $deposit
                ->setStatus(Deposit::STATUS_ERROR_INVENTORY_LOAD)
                ->setNote(substr((string) ($result['err']),0,255));

            $this->entityManager->flush($deposit);
            throw new SteamInventoryLoadException('could_not_load_inventory');
        } catch (ClientException $e) {
            $deposit
                ->setStatus(Deposit::STATUS_ERROR_INVENTORY_LOAD)
                ->setNote('http error code:'.substr((string) ($e->getCode()),0,230));
            $this->logger->critical('failed to load inventory', [$deposit, $e]);
            throw new SteamInventoryLoadException('could_not_load_inventory');
        } catch (ConnectException $e) {
            $deposit
                ->setStatus(Deposit::STATUS_ERROR_INVENTORY_LOAD)
                ->setNote('http error code:'.substr((string) ($e->getCode()),0,230));
            $this->logger->critical('failed to load inventory', [$deposit, $e]);
            throw new SteamInventoryLoadException('could_not_load_inventory');
        } catch (ServerException $e) {
            $deposit
                ->setStatus(Deposit::STATUS_ERROR_INVENTORY_LOAD)
                ->setNote('http error code:'.substr((string) $e->getCode(),0,230));
            $this->logger->critical('failed to load inventory', [$deposit, $e]);
            throw new SteamInventoryLoadException('could_not_load_inventory');
        } finally {
            $this->entityManager->flush($deposit);
        }
    }

    /**
     * @param $tradeUrl
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public static function getSteamIdFromTradeUrl($tradeUrl)
    {
        $matches = [];
        if (preg_match(self::TRADEURL_REGEX, $tradeUrl, $matches)) {
            $partnerId = $matches[1];

            return $partnerId + self::STEAMID_DELTA;
        }

        throw new InvalidTradeUrlException('invalid_trade_url');
    }

    /**
     * @param InventoryItem[] $items
     * @param Deposit         $deposit
     *
     * @return bool
     */
    public function sendTradeOffer(array $items, Deposit $deposit)
    {
        if ($deposit->getStatus() === Deposit::STATUS_PENDING) {
            return true;
        }
        $itemsIds = [];
        foreach ($items as $item) {
            $itemsIds[] = ['id' => $item->getId(), 'market_hash_name' => $item->getMarketHashName()];
        }
        $this->logger->info('items in trade', [$itemsIds, $items]);

        $post = [
            'trade_url' => $deposit->getTradeUrl(),
            'items' => json_encode($itemsIds),
            'trade_hash' => $deposit->getTradeHash(),
            'deposit_id' => $deposit->getId(),
            'integration_name' => $deposit->getIntegration()->getName(),
            'key' => $this->botKey,
        ];

        try {
            $tradeOfferId = $this->askBotToSendTradeOffer($post);
            $deposit
                ->setTradeOfferId($tradeOfferId)
                ->setStatus(Deposit::STATUS_PENDING);
            $this->entityManager->flush($deposit);

            return true;
        } catch (TradeOfferException $e) {
            $deposit
                ->setStatus(Deposit::STATUS_ERROR_BOT)
                ->setNote(substr((string) ($e->getMessage()),0, 255));
            $this->entityManager->flush($deposit);
        }

        return false;
    }

    /**
     * @param Deposit $deposit
     * @param string  $locale
     *
     * @return mixed
     */
    public function askBotToLoadInventory(Deposit $deposit, $locale)
    {
        $httpClient = new Client();
        $time = time();
        $this->logger->crit('sign bot request', [$time, $this->botKey, ApiController::sign($time, $this->botKey)]);
        $response = json_decode($httpClient->post($this->botUrl.self::BOT_INVENTORY_URL,
            [
                'headers' => [
                    'Time' => $time,
                ],
                'form_params' => [
                    'sign' => ApiController::sign($time, $this->botKey),
                    'trade_url' => $deposit->getTradeUrl(),
                    'deposit_id' => $deposit->getId(),
                    'integration' => $deposit->getIntegration()->getName(),
                    'lang' => $locale,
                ],
            ])
            ->getBody()->getContents(), true);

        return $response;
    }

    /**
     * @param $post
     *
     * @return int
     *
     * @throws TradeOfferException
     */
    public function askBotToSendTradeOffer($post)
    {
        try {
            $this->logger->critical('ask bot ot send trade offer');
            $httpClient = new Client();
            $time = time();
            $post['sign'] = ApiController::sign($time, $this->botKey);
            $result = json_decode($httpClient->post($this->botUrl.self::BOT_TRADE_OFFER_URL, [
                'headers' => [
                    'Time' => $time,
                ],
                'form_params' => $post,
            ])->getBody()->getContents(), true);
            $this->logger->critical('bot response', $result);
            if ($result['status'] == self::BOT_RESPONSE_SUCCESS) {
                return $result['trade_offer_id'] ?? null;
            } else {
                $this->logger->critical('Bot error!', []);
                throw new TradeOfferException($result['err']);
            }
        } catch (ServerException $e) {
            $this->logger->critical('Bot response error!', [$e]);
            throw new TradeOfferException($e->getMessage());
        }
    }

    /**
     * @return mixed
     */
    public function checkSteamStatus()
    {
        $steamStatus = $this->redisService->get(self::STEAM_STATUS_REDIS_KEY);

        if (!in_array($steamStatus, [self::STEAM_STATUS_DELAYED, self::STEAM_STATUS_CRITICAL, self::STEAM_STATUS_NORMAL])) {
            $steamStatus = self::STEAM_STATUS_NORMAL;
        }

        return $steamStatus;
    }
}
