## 关于Agile Swoole

一个高性能的PHP开发框架（swoole）

###
    特性
        1.支持MVC
        2.支持自定义常驻进程
        3.支持多种任务模式
        4.路由自定义事件
        5.简单易用orm
        6.分布式（待开发）
        7.队列（待开发）
        
### 基準測試
    ab -c 100 -n 50000 http://127.0.0.1:9550/
    
    Server Software:        swoole-http-server
    Server Hostname:        127.0.0.1
    Server Port:            9550
    
    Document Path:          /
    Document Length:        0 bytes
    
    Concurrency Level:      100
    Time taken for tests:   22.286 seconds
    Complete requests:      50000
    Failed requests:        0
    Total transferred:      7350000 bytes
    HTML transferred:       0 bytes
    Requests per second:    2243.52 [#/sec] (mean)
    Time per request:       44.573 [ms] (mean)
    Time per request:       0.446 [ms] (mean, across all concurrent requests)
    Transfer rate:          322.07 [Kbytes/sec] received
    
    Connection Times (ms)
                  min  mean[+/-sd] median   max
    Connect:        0    0   0.6      0      15
    Processing:     0   44  32.3     44     254
    Waiting:        0   44  32.3     43     248
    Total:          0   45  32.3     44     254
   
        
### 快速开始
composer require fresh-li/agile-swoole:dev-master

index.php
```
include '../vendor/autoload.php';
include '../src/Kernel/Core.php';
define('CORE_PATH',realpath('../src'));
define('APP_PATH',realpath('../app'));
define('CONF_PATH',realpath('../conf'));
$app = new \Kernel\Core([CORE_PATH, APP_PATH], [CONF_PATH]);
```

运行命令:

	php index.php

示例

	input localhost:9550 in brower

支持MVC结构开发

	Controller
	Model
	View
	
### 路由

```
    CONF_PATH/route.php
    [
    'path'          =>      '/',
    'dispatch'      =>      [\Controller\Welcome::class, 'index']
    ],
    [
        'path'          =>      '/sync',
        'dispatch'      =>      [\Controller\Sync::class, 'run'],
        'type'          =>      \Component\Producer\Producer::PRODUCER_SYNC
    ],
    [
        'path'          =>      '/process',
        'dispatch'      =>      [\Controller\Process::class, 'run'],
        'before'        =>      [\Controller\Process::class, 'before'],
        'after'         =>      [\Controller\Process::class, 'after'],
        'type'          =>      \Component\Producer\Producer::PRODUCER_PROCESS
    ]
    
    GET: localhost:9550
    hello world!
    
    GET: localhost:9550/sync
    sync start
    ... 10 seconds after
    sync over
    
    POST: localhost:9550/process
    this process berfore
        create process ......
    this process after
```

### 3种不同的触发模式
```
    class Sync{
        public function index()
        {
            return 'ff';
        }
    }
    
    {"code":0,"response":"ff"}
    
    class Process{
            public function index()
            {
                return 'ff';
            }
    }
    {"code":0,"response":{"processId":"{$processId}"}}
    
    class Task{
            public function index()
            {
                return ff;
            }
    }
    {"code":0}
```

### 常驻内存任务,开启服务立马启用
    
```
    $serverProcess = new ServerProcess();
    $serverProcess->addProcess(function(){
        while(true){
            //do some things
        }
    });
```


