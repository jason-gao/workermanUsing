<?php

/**
 * https://github.com/walkor/workerman-filemonitor
 * http://doc.workerman.net/components/file-monitor.html
 */
namespace workermanUsing\FileMonitor;

require __DIR__ . '/../../vendor/autoload.php';

use Workerman\Worker;
use Workerman\Lib\Timer;

class FileMonitor
{
    // watch Applications catalogue
    public $monitor_dir = '';

    //文件最后更新时间
    private $last_mtime;

    public function __construct($monitor_dir = '')
    {
        if ($monitor_dir) {
            $this->monitor_dir = realpath($monitor_dir);
        }
    }

    public function start()
    {
        // worker
        $worker                = new Worker();
        $worker->name          = 'FileMonitor';
        $worker->reloadable    = false;
        $this->last_mtime      = time();
        $worker->onWorkerStart = function () {
            // watch files only in daemon mode
            if (!Worker::$daemonize) {
                // chek mtime of files per second
                Timer::add(1, array($this, 'check_files_change'), array($this->monitor_dir));
            }
        };
    }

    // check files func
    public function check_files_change($monitor_dir)
    {
        // recursive traversal directory
        $dir_iterator = new \RecursiveDirectoryIterator($monitor_dir);
        $iterator     = new \RecursiveIteratorIterator($dir_iterator);
        foreach ($iterator as $file) {
            // only check php files
            if (pathinfo($file, PATHINFO_EXTENSION) != 'php') {
                continue;
            }
            // check mtime
            if ($this->last_mtime < $file->getMTime()) {
                echo $file . " update and reload\n";
                // send SIGUSR1 signal to master process for reload
                posix_kill(posix_getppid(), SIGUSR1);
                $this->last_mtime = $file->getMTime();
                break;
            }
        }
    }
}
