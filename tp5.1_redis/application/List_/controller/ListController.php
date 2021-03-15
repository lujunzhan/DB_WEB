<?php


namespace app\List_\controller;


class ListController
{
    public $redis;
    public function __construct()
    {
        $this->redis = new \Redis();
        $this->redis->connect(config('cache.redis.host'),'6379');
    }

    public function lpush($params)
    {
        if(count($params)<3)
            return 'Error';

        $key = $params[1];
        $value = $params[2];

        return $this->redis->lPush($key,$value);
    }

    public function rpush($params)
    {
        if(count($params)<3)
            return 'Error';

        $key = $params[1];
        $value = $params[2];

        return $this->redis->rPush($key,$value);
    }

    public function lpop($params)
    {
        if(count($params)<2)
            return 'Error';

        $key =  $params[1];

        return $this->redis->lPop($key);

    }

    public function rpop($params)
    {
        if(count($params)<2)
            return 'Error';

        $key =  $params[1];

        return $this->redis->lPop($key);
    }

    public function lrem($params)
    {
        if(count($params)<4)
            return 'Error';

        $key = $params[1];
        $value = $params[2];
        $count = $params[3];

        return $this->redis->lRem($key,$value,$count);
    }

    public function llen($params)
    {
        if(count($params)<2)
            return 'Error';

        $key = $params[1];

        return $this->redis->llen($key);
    }

    public function lindex($params)
    {
        if(count($params)<3)
            return  'Error';

        $key = $params[1];
        $index = $params[2];

        return $this->redis->lindex($key,$index);
    }

    public function lrange($params)
    {
        if(count($params)!=2)
            return 'Error';

        $key = $params[1];

        return  $this->redis->lrange($key,0,-1);
    }


}