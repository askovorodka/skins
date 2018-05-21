<?php
/**
 * Created by PhpStorm.
 * User: ahimas
 * Date: 14.10.16
 * Time: 13:51.
 */

namespace AppBundle\Service;

use AppBundle\DTO\InventoryItem;
use AppBundle\Entity\Deposit;
use AppBundle\Exception\ItemUnknownMarketNameException;
use AppBundle\Exception\LoadPriceListException;
use AppBundle\Exception\LoadWhiteListException;
use AppBundle\Exception\UnsupportedCurrencyException;
use AppBundle\Utils\ItemPriceCsgo;
use AppBundle\Utils\ItemPriceDota;
use AppBundle\Utils\ItemPriceStrategy;
use AppBundle\Utils\StringUtils;
use GuzzleHttp\Client;
use Monolog\Logger;

/**
 * Class ItemsPriceService.
 */
class ItemsPriceService
{
    const ITEMS_PRICE_LIST_URL = 'https://cases4real.com/data/prices.json?key=g323g48Gws213DF2d';
    const ITEMS_WHITE_LIST_URL = '/api/get_whitelist';
    const ITEMS_PRICE_LIST_REDIS_KEY = 'itemsPriceList';
    const ITEMS_WHITE_LIST_REDIS_KEY = 'itemsWhiteList';
    const DOTA_WHITE_LIST_REDIS_KEY =   'itemsWhiteListDota';
    const CSGO_WHITE_LIST_REDIS_KEY = 'itemsWhiteListCSGO';
    const ITEM_PRICE_REDIS_PREFIX = 'price:';
    const PRICE_RATE_REDIS_KEY = 'price:rate';

    /**
     * @var string
     */
    private $casesUrl;

    /**
     * @var string
     */
    private $casesApiKey;

    private $redisService;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var string $whiteListUrl
     */
    private $whiteListUrl;

    /**
     * @param $casesUrl
     * @param $whiteListUrl
     * @param $casesApiKey
     * @param RedisService $redisService
     * @param Logger       $logger
     */
    public function __construct(
        $casesUrl,
        $whiteListUrl,
        $casesApiKey,
        RedisService $redisService,
        Logger $logger)
    {
        $this->casesUrl = $casesUrl;
        $this->whiteListUrl = $whiteListUrl;
        $this->casesApiKey = $casesApiKey;
        $this->redisService = $redisService;
        $this->logger = $logger;
    }

    /**
     * @return array
     */
    public function getItemsPrices()
    {
        return $this->redisService->getJsonByKey(self::ITEMS_PRICE_LIST_REDIS_KEY);
    }

    /**
     * @return array
     */
    public function getItemsWhiteList()
    {
        $return = [];
        $keys_array = $this->redisService->hkeys(self::CSGO_WHITE_LIST_REDIS_KEY);
        if (!empty($keys_array))
        {
            foreach ($keys_array as $key)
            {
                $return[$key] = $this->redisService->hgetJson(self::CSGO_WHITE_LIST_REDIS_KEY, $key);
            }
        }
        return $return;
    }

    public function loadPricesAndStoreInCache()
    {
        $itemsPrices = $this->itemsPriceKeyedByMarketName();

        if (empty($itemsPrices)) {
            throw new LoadPriceListException('Failed to load price list');
        }

        foreach ($itemsPrices as $key => $value) {
            $redisKey = self::ITEM_PRICE_REDIS_PREFIX.$key;
            $this->redisService->setJsonToKey($redisKey, $value);
        }
    }

    /**
     * @param Deposit         $deposit
     * @param InventoryItem[] $items
     *
     * @return int
     *
     * @throws ItemUnknownMarketNameException
     */
    public function calculateInventoryValue(Deposit $deposit, array $items)
    {
        $rateValue = (float) $this->redisService->get(self::PRICE_RATE_REDIS_KEY);
        $result = 0;
        $whiteList = $this->getItemsWhiteList();
        $itemsPrice = [];
        foreach ($items as $item) {
            $marketName = $item->getMarketHashName();
            $redisKey = self::ITEM_PRICE_REDIS_PREFIX . $marketName;

            //if value not in Db, then continue iteration
            if (!$this->redisService->isExists($redisKey) && !$this->redisService->hexists(self::DOTA_WHITE_LIST_REDIS_KEY, $marketName)) {
                $item->setAcceptable(false);
                $this->logger->info('Unknown marketName', [$marketName]);
                continue;
            }

            if ($this->redisService->isExists($redisKey)) {
                $itemPrice = $this->redisService->getJsonByKey($redisKey);
                $itemPriceStrategy = new ItemPriceStrategy(new ItemPriceCsgo($deposit, $item, $this->logger, $itemPrice, $whiteList));
                $price = $itemPriceStrategy->calculatePrice();
                $dynamicCommission = $itemPriceStrategy->calculateComission();
            }
            elseif ($this->redisService->hexists(self::DOTA_WHITE_LIST_REDIS_KEY, $marketName))
            {
                $itemArray = $this->redisService->hgetJson(self::DOTA_WHITE_LIST_REDIS_KEY, $marketName);
                $itemPriceStrategy = new ItemPriceStrategy(new ItemPriceDota($itemArray));
                $price = $itemPriceStrategy->calculatePrice();
                $dynamicCommission = $itemPriceStrategy->calculateComission();
            } else {
                continue;
            }

            if (empty($dynamicCommission) || $dynamicCommission < 0 || $dynamicCommission > 1) {
                $item->setAcceptable(false);
                $this->logger->critical('PIZDEC COMMISSION FOR MARKETNAME', [$marketName, $dynamicCommission]);
                continue;
            }

            $item
                ->setRateValue($rateValue)
                ->setOrigPrice($price ?? 0)
                ->setComission($dynamicCommission)
                ->setStockCount($whiteList[$marketName]['stock_count'] ?? 0);

            $this->calculateItemPrice($deposit->getCurrency(), $price, $dynamicCommission, $item);

            if ($deposit->getCurrency() === Deposit::CURRENCY_RUB) {
                $price = $price * $rateValue;
            }

            if ($item->getAcceptable()) {
                $result += $item->getValueRaw();
            }

            try {
                if ($item->getAppId() === InventoryItem::APPID_CSGO) {
                    if (isset($itemsPrice[Deposit::ITEMS_PRICE_CSGO_KEY])) {
                        $itemsPrice[Deposit::ITEMS_PRICE_CSGO_KEY]['value'] += StringUtils::round($item->getValue());
                        $itemsPrice[Deposit::ITEMS_PRICE_CSGO_KEY]['no_tax_value'] += StringUtils::round($item->getNoTaxValue());
                    } else {
                        $itemsPrice[Deposit::ITEMS_PRICE_CSGO_KEY]['value'] = StringUtils::round($item->getValue());
                        $itemsPrice[Deposit::ITEMS_PRICE_CSGO_KEY]['no_tax_value'] = StringUtils::round($item->getNoTaxValue());
                    }
                } elseif ($item->getAppId() === InventoryItem::APPID_DOTA2) {
                    if (isset($itemsPrice[Deposit::ITEMS_PRICE_DOTA_KEY])) {
                        $itemsPrice[Deposit::ITEMS_PRICE_DOTA_KEY]['value'] += StringUtils::round($item->getValue());
                        $itemsPrice[Deposit::ITEMS_PRICE_DOTA_KEY]['no_tax_value'] += StringUtils::round($item->getNoTaxValue());
                    } else {
                        $itemsPrice[Deposit::ITEMS_PRICE_DOTA_KEY]['value'] = StringUtils::round($item->getValue());
                        $itemsPrice[Deposit::ITEMS_PRICE_DOTA_KEY]['no_tax_value'] = StringUtils::round($item->getNoTaxValue());
                    }
                }
            } catch (\Exception $exception) {
                $this->logger->crit('ItemsPriceCalculate exception',[
                    'class' => __CLASS__,
                    'message'   => $exception->getMessage(),
                ]);
            }

        }

        try {
            $deposit
                ->setValueDota($itemsPrice[Deposit::ITEMS_PRICE_DOTA_KEY]['value'] ?? 0.00)
                ->setValueCsgo($itemsPrice[Deposit::ITEMS_PRICE_CSGO_KEY]['value'] ?? 0.00)
                ->setNoTaxValueDota($itemsPrice[Deposit::ITEMS_PRICE_DOTA_KEY]['no_tax_value'] ?? 0.00)
                ->setNoTaxValueCsgo($itemsPrice[Deposit::ITEMS_PRICE_CSGO_KEY]['no_tax_value'] ?? 0.00);
        } catch (\Exception $exception) {
            $this->logger->crit('ItemPriceService exception', [
                'message'   => $exception->getMessage(),
                'trace'     => $exception->getTraceAsString(),
            ]);
        }

        return $result;
    }

    /**
     * @param $currency
     * @param $price
     * @param $dynamicCommission
     * @param $item
     */
    private function calculateItemPrice($currency, $price, $dynamicCommission, InventoryItem $item)
    {
        $rateValue = (float) $this->redisService->get(self::PRICE_RATE_REDIS_KEY);
        $valueAfterTax = StringUtils::round($price * $dynamicCommission);

        if ($currency === Deposit::CURRENCY_RUB) {
            $price *= $rateValue;
            $price = StringUtils::round($price);
            $valueAfterTax = $valueAfterTax * $rateValue;
            $valueAfterTax = StringUtils::round($valueAfterTax);
        }

        $item->setValueRaw($valueAfterTax);

        if ($item->getValueRaw() == 0.00) {
            $item->setAcceptable(false);
            $this->logger->crit('item price is too low', [$item]);

            return;
        }

        $item->setNoTaxValueRaw(StringUtils::round($price));
        $value = $this->formatForCurrency($item->getNoTaxValueRaw(), $currency);
        $valueAfterTax = $this->formatForCurrency($item->getValueRaw(), $currency);
        $item->setNoTaxValue($value);
        $item->setValue($valueAfterTax);
    }

    /**
     * @param Deposit         $deposit
     * @param InventoryItem[] $items
     */
    public function calculateDepositNoTaxValue(Deposit $deposit, array $items)
    {
        $result = 0;
        foreach ($items as $item) {
            $result += $item->getNoTaxValueRaw();
        }

        $deposit->setNoTaxValue($result);
    }

    private function formatForCurrency($price, $currency)
    {
        switch ($currency) {
            case Deposit::CURRENCY_RUB:
                return number_format(StringUtils::round($price), 2, '.', ' ');
            case Deposit::CURRENCY_USD:
                return number_format(StringUtils::round($price), 2, '.', ',');
            default:
                throw new UnsupportedCurrencyException("unsupported currency $currency");
        }
    }

    /**
     * @param InventoryItem[] $items
     *
     * @return InventoryItem[]
     */
    public function sortByValue(array $items)
    {
        usort($items, [$this, 'compareItemsValue']);

        return $items;
    }

    /**
     * @param InventoryItem $a
     * @param InventoryItem $b
     *
     * @return int
     */
    private static function compareItemsValue(InventoryItem $a, InventoryItem $b)
    {
        return bccomp($b->getValueRaw(), $a->getValueRaw(), 2);
    }

    public function loadWhiteListAndStoreInCache()
    {
        $whiteList = $this->loadItemsWhiteList();
        if (!$whiteList) {
            throw new LoadWhiteListException('empty response');
        }
        $all_keys = $this->redisService->hkeys(self::CSGO_WHITE_LIST_REDIS_KEY);
        //remove keys
        foreach ($all_keys as $market_name){
            if (!isset($whiteList[$market_name])){
                $this->redisService->hdel(self::CSGO_WHITE_LIST_REDIS_KEY, $market_name);
            }
        }
        foreach ($whiteList as $marketName => $item) {
            $this->redisService->hsetJson(self::CSGO_WHITE_LIST_REDIS_KEY, $marketName, $item);
        }
    }

    /**
     * save whitelist to redis
     * @throws \Exception
     */
    public function loadDotaWhiteListAndStoreInCache()
    {
        $whiteList = $this->loadItemsDota2WhiteList();
        if (!$whiteList) {
            throw new LoadWhiteListException('empty white list dota2');
        }

        $dotaWhiteListTmpKey = ItemsPriceService::DOTA_WHITE_LIST_REDIS_KEY . "_tmp";
        //clear tmp member
        $this->redisService->del($dotaWhiteListTmpKey);
        //add actual values in tmp member
        foreach ($whiteList as $marketName => $data) {
            $this->redisService->hsetJson($dotaWhiteListTmpKey, $marketName, $data);
        }

        //get all old values
        $allValues = $this->redisService->hgetall(ItemsPriceService::DOTA_WHITE_LIST_REDIS_KEY);
        $deletes = [];
        //find deletes keys in old member
        foreach ($allValues as $field => $value)
        {
            if (!$this->redisService->hexists($dotaWhiteListTmpKey, $field)){
                array_push($deletes, $field);
            }
        }
        //delete not actual keys in old member
        if (!empty($deletes)) {
            $this->redisService->hdel(ItemsPriceService::DOTA_WHITE_LIST_REDIS_KEY, $deletes);
        }
        //get all actual keys from tmp member
        $allActualValues = $this->redisService->hgetall($dotaWhiteListTmpKey);
        //add or update old member
        foreach ($allActualValues  as $field=>$value) {
            $this->redisService->hset(ItemsPriceService::DOTA_WHITE_LIST_REDIS_KEY, $field, $value);
        }
        //clear tmp member
        $this->redisService->del($dotaWhiteListTmpKey);
    }

    /**
     * @return array
     */
    private function itemsPriceKeyedByMarketName()
    {
        $response = $this->loadItemsPrices();
        $result = [];

        if (isset($response['results']) && !empty($response['results'])) {
            $result['rate'] = $response['rate'];
            foreach ($response['results'] as $item) {
                $result[$item['market_name']] = $item;
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    private function loadItemsPrices()
    {
        $httpClient = new Client();

        return json_decode($httpClient->request('GET', self::ITEMS_PRICE_LIST_URL)->getBody()->getContents(), true);
    }

    /**
     * @return array
     */
    private function loadItemsWhiteList()
    {
        $post = [
            'key' => $this->casesApiKey,
        ];

        $httpClient = new Client();

        return json_decode($httpClient->request('POST', $this->casesUrl.self::ITEMS_WHITE_LIST_URL, [
            'headers' => ['User-Agent' => 'HEYDRUPAL'],
            'form_params' => $post,
        ])->getBody()->getContents(), true);
    }

    private function loadItemsDota2WhiteList()
    {
        $client = new Client();
        $result = json_decode($client->request('GET', $this->whiteListUrl,[])->getBody()->getContents(), true);
        return $result['data'] ?? null;
    }

}
