<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});

Route::get('hello/:name', 'index/hello');

Route::rule('/test', 'index/index/test')
    ->allowCrossDomain();

Route::rule('/spider', 'index/index/crawl')
    ->allowCrossDomain();
//热榜操作路由
//热榜
Route::rule('/hotadd', 'index/index/hotAddScores')
    ->allowCrossDomain();
//初始化加载redis数据
Route::rule('/hotinit', 'index/index/loadHotInit')
    ->allowCrossDomain();
//主函数分类器
Route::rule('/classify', 'RedisOperate/RedisOperate/index')
    ->allowCrossDomain();

Route::rule('/crawl', 'index/index/storedToMysql')
    ->allowCrossDomain();

return [
];
