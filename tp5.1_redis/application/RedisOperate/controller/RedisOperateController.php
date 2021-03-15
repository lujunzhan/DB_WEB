<?php


namespace app\RedisOperate\controller;

use think\Request;
use traits\controller\ResponseJson;

header('Access-Control-Allow-Origin:*');
class RedisOperateController
{
    protected $request;
    public $data_type;
    public $op_type;

    use ResponseJson;
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->data_type = ['String','Hash','List','Set','Zset'];
        $this->op_type = ['get','set','del'];
    }


    public function index()
    {

        $data_type = $this->request->param('data_type');
        $input_order = $this->request->param('input_order');
        $operate_time = $this->request->param('operate_time');

        //数据预处理
        $op_info = $this->data_deal($input_order);
        //转换为小写 方便展示
        $op_info[0] = strtolower($op_info[0]);
        $operator = $op_info[0];

        //默认为失败
        $response = 'Failure';

        //组合成操作数
        $operand = '';
        for($i=1;$i<count($op_info);$i++)
        {
            $operand =$operand.$op_info[$i].' ';
        }

        //核心分类
        if(in_array($data_type,$this->data_type))
        {
            $url = $data_type.'/'.$data_type.'/'.$operator;

            switch ($data_type)
            {
                case 'String':
                    $response = action($url,['params'=>$op_info]);

                    switch ($operator)
                    {
                        case 'set':
                            if($response === 'Error')
                                return $this->jsonResponseRedis('400',$data_type,$operator,$operand,'Error',$operate_time);
                            else if($response == true)
                                return $this->jsonResponseRedis('200',$data_type,$operator,$operand,'Success',$operate_time);
                            else
                                return $this->jsonResponseRedis('300',$data_type,$operator,$operand,'Failure',$operate_time);
                        case 'del':
                        case 'get':
                            if($response == false)
                                return $this->jsonResponseRedis('300',$data_type,$operator,$operand,'Failure',$operate_time);
                            else if($response === 'Error')
                                return $this->jsonResponseRedis('400',$data_type,$operator,$operand,'Error',$operate_time);
                            else
                                return $this->jsonResponseRedis('200',$data_type,$operator,$operand,$response,$operate_time);

                        case 'keys':
                            if($response === 'Error')
                                return $this->jsonResponseRedis('400',$data_type,$operator,$operand,'Error',$operate_time);
                            else
                                return $this->jsonResponseRedis('200',$data_type,$operator,$operand,$response,$operate_time);

                        default:
                            return $this->jsonResponseRedis('400',$data_type,$operator,$operand,'Error',$operate_time);
                    }

                case 'Hash':
                    switch ($operator)
                    {
                        case 'hset':
                            $response = action($url,['params'=>$op_info]);

                            if($response==0||$response==1)//比较特殊的返回值
                                return $this->jsonResponseRedis('200',$data_type,$operator,$operand,'Sccuess',$operate_time);
                            else return $this->jsonResponseRedis('400',$data_type,$operator,$operand,'Error',$operate_time);

                        case 'hget':
                            $response = action($url,['params'=>$op_info]);

                            if($response == false)
                                return $this->jsonResponseRedis('300',$data_type,$operator,$operand,'Failure',$operate_time);
                            else
                                return $this->jsonResponseRedis('200',$data_type,$operator,$operand,$response,$operate_time);

                        case 'hdel':
                            $response = action($url,['params'=>$op_info]);
                            break;

                        case 'hkeys':
                        case 'hvals':
                        case 'hgetall':
                        case 'hlen':
                        case 'hstrlen':
                            $response = action($url,['params'=>$op_info]);
                            return $this->jsonResponseRedis('200',$data_type,$operator,$operand,$response,$operate_time);

                        default:
                            return $this->jsonResponseRedis('400',$data_type,$operator,$operand,'Error',$operate_time);
                    }
                    break;

                case 'Set':
                    $response = action($url,['params'=>$op_info]);
                    switch ($operator)
                    {
                        case 'sadd'://暂时只允许一个一个加
                            if($response==false)//比较特殊的返回值
                                return $this->jsonResponseRedis('200',$data_type,$operator,$operand,'Existed',$operate_time);
                            else if($response==true) return $this->jsonResponseRedis('200',$data_type,$operator,$operand,'Success',$operate_time);
                            else return $this->jsonResponseRedis('400',$data_type,$operator,$operand,'Error',$operate_time);


                        case 'srem'://暂时只允许一个一个删除

                            if($response)
                                return $this->jsonResponseRedis('200',$data_type,$operator,$operand,'Success',$operate_time);
                            elseif ($response==0)
                                return $this->jsonResponseRedis('200',$data_type,$operator,$operand,'NoExisted',$operate_time);
                            else return $this->jsonResponseRedis('400',$data_type,$operator,$operand,'Error',$operate_time);

                        case 'sismember':
                            if($response==true||$response==false)
                                return $this->jsonResponseRedis('200',$data_type,$operator,$operand,$response,$operate_time);
                            else return $this->jsonResponseRedis('400',$data_type,$operator,$operand,'Error',$operate_time);

                        case 'smembers':
                            return $this->jsonResponseRedis('400',$data_type,$operator,$operand,$response,$operate_time);

                        default:
                            return $this->jsonResponseRedis('400',$data_type,$operator,$operand,'Error',$operate_time);
                    }

                case 'List':
                    $url = $data_type.'_/'.$data_type.'/'.$operator;
                    $response = action($url,['params'=>$op_info]);
                    switch ($operator)
                    {
                        case 'rpush':
                        case 'lpush':
                        case 'lpop':
                        case 'rpop':
                        case 'lrem':

                            if($response == 'Error'||$response==false)
                                return $this->jsonResponseRedis('400',$data_type,$operator,$operand,'Error',$operate_time);
                            else
                                return $this->jsonResponseRedis('200',$data_type,$operator,$operand,'Success',$operate_time);
                        case 'llen':
                            if($response == 'Error'||$response==false)
                                return $this->jsonResponseRedis('400',$data_type,$operator,$operand,'Error',$operate_time);
                            else
                                return $this->jsonResponseRedis('200',$data_type,$operator,$operand,$response,$operate_time);
                        case 'lindex':
                            if($response == false)
                                return $this->jsonResponseRedis('300',$data_type,$operator,$operand,'NoExisted',$operate_time);
                            else if($response == 'Error')
                                return $this->jsonResponseRedis('400',$data_type,$operator,$operand,'Error',$operate_time);
                            else
                                return $this->jsonResponseRedis('200',$data_type,$operator,$operand,$response,$operate_time);
                        case 'lrange':
                            if($response == false||$response=='Error')
                                return $this->jsonResponseRedis('400',$data_type,$operator,$operand,'Error',$operate_time);
                            else
                                return $this->jsonResponseRedis('200',$data_type,$operator,$operand,$response,$operate_time);

                        default:
                            return $this->jsonResponseRedis('400',$data_type,$operator,$operand,'Error',$operate_time);

                    }


                case 'Zset':
                    $response = action($url,['params'=>$op_info]);
                    switch ($operator)
                    {
                        case 'zadd':
                            if($response==1)
                                return $this->jsonResponseRedis('200',$data_type,$operator,$operand,'Success',$operate_time);
                            else if($response==0)
                                return $this->jsonResponseRedis('200',$data_type,$operator,$operand,'Existed/Updated',$operate_time);
                            else
                                return $this->jsonResponseRedis('400',$data_type,$operator,$operand,'Error',$operate_time);

                        case 'zrem'://暂时设置为只能删除单个元素
                            if($response>0)
                                return $this->jsonResponseRedis('200',$data_type,$operator,$operand,'Success',$operate_time);
                            else if($response==0)
                                return $this->jsonResponseRedis('300',$data_type,$operator,$operand,'NoExisted',$operate_time);
                            else
                                return $this->jsonResponseRedis('400',$data_type,$operator,$operand,'Error',$operate_time);
                        case 'zscore':
                            if($response==false)
                                return $this->jsonResponseRedis('300',$data_type,$operator,$operand,'NoExisted',$operate_time);
                            else if($response=='Error')
                                return $this->jsonResponseRedis('400',$data_type,$operator,$operand,'Error',$operate_time);
                            else
                                return $this->jsonResponseRedis('200',$data_type,$operator,$operand,$response,$operate_time);

                        case 'zcard'://获得集合的基数
                            if($response!='Error')
                                return $this->jsonResponseRedis('200',$data_type,$operator,$operand,$response,$operate_time);
                            else
                                return $this->jsonResponseRedis('400',$data_type,$operator,$operand,'Error',$operate_time);

                        case 'zrange'://升序排序 支持withscore
                        case 'zrevrange'://降序排序 支持withscore
                            if($response!='Error')
                              return $this->jsonResponseRedis('200',$data_type,$operator,$operand,$response,$operate_time);

                        default:
                            return $this->jsonResponseRedis('400',$data_type,$operator,$operand,'Error',$operate_time);
                    }

                default:
                    return $this->jsonResponseRedis('400',$data_type,$operator,$operand,'Error',$operate_time);
            }

            if($response){
                $response = 'Success';
                $code = '200';
            } else{
                $response = 'Failure';
                $code ='300';
            }
            return $this->jsonResponseRedis($code,$data_type,$operator,$operand,$response,$operate_time);
        }

        return $this->jsonResponseRedis('400',$data_type,$operator,$operand,'Error',$operate_time);

    }

    /**
     * @description 数据处理函数
     * @param $input_order
     * @return array|\think\response\Json
     */
    public function data_deal($input_order)
    {
        $input_data = ltrim($input_order,' ');
        $input_data = rtrim($input_data,' ');
        $split_data = explode(' ',$input_data);

        $res = array();
        //去除空格
        foreach ($split_data as $val) {
            if ($val != '')
                $res[] = $val;
        }
        return $res;
    }

}