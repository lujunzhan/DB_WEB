<?php
namespace app\index\controller;

use app\common\library\Hotpoint;
use app\common\library\RedisHotList;
use app\index\model\HotListModel;
use app\index\model\UserModel;
use DOMDocument;
use DOMXPath;
use think\cache\driver\Memcached;
use think\Db;
use think\Exception;
use think\Request;
use traits\controller\ResponseJson;

header('Access-Control-Allow-Origin:*');
header('charset:utf-8');
class IndexController
{
    use ResponseJson;

    protected $request;
    public $redis;

    public function __construct(Request $request)
    {
        $this->redis =new \Redis();
        $this->redis->connect(config('cache.redis.host'),'6379');
        $this->request = $request;
    }

    public function index()
    {
        $res = UserModel::all();
        dump($res);
    }

    /**
     * @return false|string
     */
    public function  crawl()
    {
        //请求头
        $context_options = array(
            'http' =>
                array(
                    'method' => "GET",
                    'header' => "User-Agent:Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36\r\nReferer:http://www.zhuyanjun.cn/\r\n",
                ));

        $context = stream_context_create($context_options);

        $url = "https://s.weibo.com/top/summary?cate=realtimehot";
        $html = file_get_contents($url,FALSE,$context);

        $dom = new DOMDocument('1.0', 'UTF-8');
        // 从一个字符串加载HTML
        @$dom->loadHTML($html);
        // 使该HTML规范化
        $dom->normalize();
        // 用DOMXpath加载DOM，用于查询
        $xpath = new DOMXPath($dom);

        $titles= $xpath->query("//*[@id='pl_top_realtimehot']/table/tbody/tr/td[2]/a/text()");//热搜名
        $urls = $xpath->query("//*[@id='pl_top_realtimehot']/table/tbody/tr/td[2]/a/@href");//链接
        $hots = $xpath->query('//*[@id="pl_top_realtimehot"]/table/tbody/tr/td[2]/span/text()');//热度

        $res = array();

        //打印输出
        for ($i = 0; $i < $hots->length; $i++) {

            $title = $titles->item($i+1);
            $url = $urls->item($i+1);
            $hot = $hots->item($i);
//            $json = $title->nodeValue;
            $hotpoint = new Hotpoint();
            $hotpoint->name = $title->nodeValue;
            $hotpoint->url = "https://s.weibo.com".$url->nodeValue;
            $hotpoint->scores=$hot->nodeValue;

            $res[] = $hotpoint;
//            echo $title->nodeValue.$url->nodeValue.$hot->nodeValue."<br/>";
        }


        return $this->jsonSuccessData('DB',$res);

    }

    public function test()
    {

        $res = $this->request->param('data');

        if($res == 1)
        {
            return "this is 1";
        }

        $mem = new \Memcached();
        $mem->addServer('127.0.0.1','11211');

        $ret =$mem->set($res,'lujunzhan',100);
        dump($ret);

        $res = $mem->getAllKeys();

        return $this->jsonSuccessData('success',$res);
    }


    public function test_redis()
    {
        $redis = new \Redis();
        $redis->connect('127.0.0.1','6379');
        $redis->select(1);
        $redis->set('test','test_set',1000);

        dump('this is redis test');
    }


    public function hotAddScores()
    {
        $set_name = 'hotlist';
        $key = $this->request->param('hot_title');
        $score = $this->request->param('hot_score');

        $this->redis->select(1);
        return $this->redis->zIncrBy($set_name,432,$key);
    }


    /**
     * @return array|false|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @description 从redis中加载热搜数据
     */
    public function loadHotInit()
    {
        $this->redis->select(1);
        $redis_data =  $this->redis->zRevRange('hotlist',0,-1,true);

        if(count($redis_data)==0)
        {
            $this->loadHotFromMysql();
            $redis_data =  $this->redis->zRevRange('hotlist',0,-1,true);
        }

        $res = array();
        foreach ($redis_data as $key=>$val)
        {
            $hotpoint = new RedisHotList();
            $hotpoint->name = $key;
            $hotpoint->scores=$val;

            $res[] = $hotpoint;
        }

        return $this->jsonSuccessData('redis_hot_list',$res);

    }

    public function storedToMysql()
    {
        $data = $this->crawl();

        $info = json_decode($data)->data;
        //先清空数据库
        $res = Db::query("delete from hot_list");
        //清空redis
        $this->redis->select(1);
        $this->redis->del('hotlist');

        //存入数据库
        foreach ($info as $item)
        {
            Db::table('hot_list')->insert(['name'=>$item->name,'url'=>$item->url,'scores'=>$item->scores]);
        }

        $this->loadHotInit();

        return $this->jsonSuccessData('MysqlLoadSuccess',[]);
    }

    /**
     * @return array|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function loadHotFromMysql()
    {

        $data =  Db::table('hot_list')->field(['name','scores'])->select();

        $this->redis->select(1);
        //存入redis数据库
        $this->redis->multi();
        for($i=0;$i<count($data);$i++)
        {
            $this->redis->zAdd('hotlist',$data[$i]['scores']/100,$data[$i]['name']);
        }
        return $this->redis->exec();

    }

}