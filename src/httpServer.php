<?php

require_once __DIR__ . '/../vendor/autoload.php';
use Workerman\Worker;
use workermanUsing\FileMonitor\FileMonitor;
use workermanUsing\echoSome;

// file monitor
$fileMonitor = new FileMonitor(__DIR__);
$fileMonitor->start();

# config
$config = [1];

// #### http worker ####
$http_worker = new Worker("http://0.0.0.0:2345");

$http_worker->name = 'httpServer';

// 4 processes
$http_worker->count = 4;


$http_worker->onWorkerStart = function ($worker){
    //此数组中的文件表示进程启动前就加载了，所以无法reload
//    var_dump(get_included_files());
};

// Emitted when data received
$http_worker->onMessage = function ($connection, $data){
    // $_GET, $_POST, $_COOKIE, $_SESSION, $_SERVER, $_FILES are available
//    var_dump($_GET, $_POST, $_COOKIE, $_SESSION, $_SERVER, $_FILES, $data);
    var_dump("onMessage");
    // echo
    //回调里面创建的对象reload可以生效
    $echoObj = new echoSome();
    $echoObj->echoStr();
    // send data to client
    $connection->send("hello world \n");
};

$http_worker->onWorkerReload = function ($worker){
    //不需要重启进程的情况下重新加载业务配置文件
    var_dump("onWorkerReload");
    $config = [date('Y-m-d H:i:s')];
    var_dump($config);
    //清理OpCode缓存
//    opcache_reset();
//    apc_clear_cache();
};

// run all workers
Worker::runAll();
