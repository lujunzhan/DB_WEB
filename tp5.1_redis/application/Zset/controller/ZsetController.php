<?php


namespace app\Zset\controller;


class ZsetController
{
    public $redis;
    public function __construct()
    {
        $this->redis = new \Redis();
        $this->redis->connect(config('cache.redis.host'),'6379');
    }

    public function zadd($params)
    {
        if(count($params)!=4)
            return 'Error';

        $set_name = $params[1];
        $score = $params[2];
        $key = $params[3];
        return $this->redis->zAdd($set_name,$score,$key);
    }

    public  function zscore($params)
    {
        if(count($params)<3)
            return 'Error';

        $key = $params[1];
        $member = $params[2];

        return $this->redis->zScore($key,$member);
    }

    public function zrange($params)
    {
        if(count($params)<4)
            return 'Error';

        $key = $params[1];
        $start = $params[2];
        $end = $params[3];

        $withscore = null;
        if(count($params)==5)
            if($params[4]=='withscore')
                $withscore =boolval($params[4]) ;
            else
                return 'Error';

        return $this->redis->zRange($key,$start,$end,$withscore);
    }

    public function zrevrange($params)
    {
        if(count($params)<4)
            return 'Error';

        $key = $params[1];
        $start = $params[2];
        $end = $params[3];

        $withscore = null;
        if(count($params)==5)
            if($params[4]=='withscore')
                $withscore =boolval($params[4]) ;
            else
                return 'Error';


        return $this->redis->zRevRange($key,$start,$end,$withscore);
    }

    public function zrem($params)
    {
        if(count($params)<3)
            return 'Error';

        $key = $params[1];
        $member = $params[2];

        return $this->redis->zRem($key,$member);

    }

    public function zcard($params)
    {
        if(count($params)!=2)
            return 'Error';
        $key = $params[1];
        return $this->redis->zCard($key);
    }


    public function test()
    {

    }
}