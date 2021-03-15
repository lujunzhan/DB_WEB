<?php


namespace app\Set\controller;


class SetController
{
    public $redis;
    public function __construct()
    {
        $this->redis = new \Redis();
        $this->redis->connect(config('cache.redis.host'),'6379');
    }

    public function sadd($params)
    {
        if(count($params)<3)
            return null;
        $key= $params[1];
        $val = $params[2];

        return $this->redis->sAdd($key,$val);
    }

    public function srem($params)
    {
        if(count($params)<3)
            return 'Error';
        $key= $params[1];
        $val = $params[2];

        return $this->redis->sRem($key,$val);
    }


    public function smembers($params)
    {
        if(count($params)<2)
            return 'Error';

        $key = $params[1];

        return  $this->redis->sMembers($key);
    }

    public function sismember($params)
    {
        if(count($params)<3)
            return 'Error';
        $key= $params[1];
        $val = $params[2];

        return $this->redis->sIsMember($key,$val);
    }
}