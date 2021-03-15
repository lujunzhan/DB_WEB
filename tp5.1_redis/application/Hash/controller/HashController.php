<?php


namespace app\Hash\controller;


class HashController
{
    public $redis;
    public function __construct()
    {
        $this->redis = new \Redis();
        $this->redis->connect(config('cache.redis.host'),'6379');
    }

    public function hset($params)
    {
        if(count($params)<3)
            return false;

        $key = $params[1];
        $hashKey=$params[2];
        $value = $params[3];

        return $this->redis->hSet($key,$hashKey,$value);
    }

    public function hget($params)
    {
        if(count($params)<3)
            return false;
        $key = $params[1];
        $hashkey = $params[2];

        return $this->redis->hGet($key,$hashkey);
    }

    public function hdel($params)
    {
        if(count($params)<3)
            return false;

        $key = $params[1];
        $hashkey = $params[2];

        return $this->redis->hdel($key,$hashkey);
    }

    public function hkeys($params)
    {
        if(count($params)!=2)
            return 'Error';
        $key = $params[1];
        return $this->redis->hKeys($key);
    }

    public function hvals($params)
    {
        if(count($params)!=2)
            return 'Error';
        $key = $params[1];
        return $this->redis->hVals($key);
    }

    public function hgetall($params)
    {
        if(count($params)!=2)
            return 'Error';
        $key = $params[1];
        return $this->redis->hGetAll($key);
    }

    public function hlen($params)
    {
        if(count($params)!=2)
            return 'Error';
        $key = $params[1];
        return $this->redis->hLen($key);
    }

    public function hstrlen($params)
    {
        if(count($params)!=3)
            return 'Error';
        $key = $params[1];
        $hashkey = $params[2];
        return $this->redis->hStrLen($key,$hashkey);
    }
}