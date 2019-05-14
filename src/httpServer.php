<?php

require_once __DIR__ . '/../vendor/autoload.php';
use Workerman\Worker;
use workermanUsing\FileMonitor\FileMonitor;

$fileMonitor = new FileMonitor(__DIR__);
$fileMonitor->start();

// #### http worker ####
$http_worker = new Worker("http://0.0.0.0:2345");

$http_worker->name = 'httpServer';

// 4 processes
$http_worker->count = 4;

// Emitted when data received
$http_worker->onMessage = function ($connection, $data) {
    // $_GET, $_POST, $_COOKIE, $_SESSION, $_SERVER, $_FILES are available
//    var_dump($_GET, $_POST, $_COOKIE, $_SESSION, $_SERVER, $_FILES, $data);
    var_dump("receive data:".json_encode($data));
    // send data to client
    $connection->send("hello world1 \n");
};

// run all workers
Worker::runAll();
