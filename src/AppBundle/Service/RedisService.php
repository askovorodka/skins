<?php
/**
 * Created by PhpStorm.
 * User: ahimas
 * Date: 07.12.16
 * Time: 19:51.
 */

namespace AppBundle\Service;

use Predis\Client;

class RedisService
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param $key
     * @param $value
     */
    public function setJsonToKey($key, $value)
    {
        $this->client->set($key, json_encode($value));
    }

    public function setex($key, $seconds, $value)
    {
        $this->client->setex($key, $seconds, json_encode($value));
    }

    public function getJsonByKey($key)
    {
        $result = $this->client->get($key);

        return json_decode($result, true);
    }

    public function delete($key)
    {
        $this->client->del([$key]);
    }

    public function setExpireat($key, $timestamp)
    {
        $this->client->expireat($key, $timestamp);
    }

    /**
     * @param string $key
     *
     * @return int
     */
    public function isExists(string $key): int
    {
        return $this->client->exists($key);
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function get(string $key)
    {
        return $this->client->get($key);
    }

    public function keys(string $mask)
    {
        return $this->client->keys($mask);
    }

    /**
     * set value in redis db.
     *
     * @param $key
     * @param $value
     *
     * @return mixed
     */
    public function set($key, $value)
    {
        return $this->client->set($key, $value);
    }

    public function hsetJson($key, $field, array $value)
    {
        return $this->hset($key, $field, json_encode($value));
    }

    public function hset($key, $field, $value)
    {
        return $this->client->hset($key, $field, $value);
    }

    public function hexists($key, $field)
    {
        return $this->client->hexists($key, $field);
    }

    public function hmset($key, $array)
    {
        return $this->client->hmset($key, $array);
    }

    public function del($key)
    {
        return $this->client->del($key);
    }

    public function hdel($key, $field)
    {
        return $this->client->hdel($key, $field);
    }

    public function hget($key, $field)
    {
        return $this->client->hget($key, $field);
    }

    public function hgetJson($key, $field)
    {
        $jsonValue = $this->hget($key, $field);
        return json_decode($jsonValue, true);
    }

    public function hvals($key)
    {
        return $this->client->hvals($key);
    }

    public function hkeys($key)
    {
        return $this->client->hkeys($key);
    }

    public function hgetall($key)
    {
        return $this->client->hgetall($key);
    }

    public function hvalsJsonDecode($key)
    {
        $items = $this->hvals($key);
        $return = [];
        if (!empty($items))
        {
            foreach ($items as $item){
                $return[] = json_decode($item, true);
            }
        }
        return $return;
    }
}
