<?php


namespace app\String\controller;


class StringController
{
    public $redis;
    public function __construct()
    {
        $this->redis = new \Redis();
        $this->redis->connect(config('cache.redis.host'),'6379');
    }

    public function set($params)
    {
        if(count($params)<3)
            return 'Error';


        $key =$params[1];
        $value =$params[2];

        $expire = null;
        if(count($params)==4)
            $expire = $params[3];

        return $this->redis->set($key,$value,$expire);
    }

    public function get($params)
    {
        if(count($params)<2)
            return 'Error';

        $key =$params[1];
        return $this->redis->get($key);
    }

    public function del($params)
    {
        if(count($params)<2)
            return 'Error';

        $key =$params[1];

        return $this->redis->del($key);
    }
    public function keys($params)
    {
        if(count($params)<2)
            return 'Error';

        return $this->redis->keys($params[1]);
    }

}