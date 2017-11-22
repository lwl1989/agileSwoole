## 关于Agile Swoole

一个高性能的PHP开发框架（swoole）

###
    特性
        1.支持MVC
        2.支持自定义常驻进程
        3.支持多种任务模式
        4.路由自定义事件
        5.简单易用orm[可二次开发，实现接口，自动注入即可]
        6.自动协程（Coroutine，假如你的swoole是2.0以上，自动开启协程进行调度）
        7.分布式（待开发）
        8.队列（待开发）
        
### 压力测试

##### 测试机器
     
    双核 Intel(R) Pentium(R) CPU G2020 @ 2.90GHz
    ddr3 1333mhz 4g
    硬盘5400转
    ubuntu 16.04 desktop
    
##### 测试命令

    ab -c 100 -n 50000 http://127.0.0.1:9550/
    
##### 测试结果

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
    cd bin
    php agile.php
    
    http://127.0.0.1:9550
	
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


### daemon模式測試（性能提升一倍）
需要将config/config.php中的daemonize设置为1或者true
```
    ab -c 100 -n 50000 http://127.0.0.1:9550/
    This is ApacheBench, Version 2.3 <$Revision: 1706008 $>
    Copyright 1996 Adam Twiss, Zeus Technology Ltd, http://www.zeustech.net/
    Licensed to The Apache Software Foundation, http://www.apache.org/
    
    Benchmarking 127.0.0.1 (be patient)
    Completed 5000 requests
    Completed 10000 requests
    Completed 15000 requests
    Completed 20000 requests
    Completed 25000 requests
    Completed 30000 requests
    Completed 35000 requests
    Completed 40000 requests
    Completed 45000 requests
    Completed 50000 requests
    Finished 50000 requests
    
    
    Server Software:        swoole-http-server
    Server Hostname:        127.0.0.1
    Server Port:            9550
    
    Document Path:          /
    Document Length:        38 bytes
    
    Concurrency Level:      100
    Time taken for tests:   11.934 seconds
    Complete requests:      50000
    Failed requests:        0
    Total transferred:      9300000 bytes
    HTML transferred:       1900000 bytes
    Requests per second:    4189.60 [#/sec] (mean)
    Time per request:       23.869 [ms] (mean)
    Time per request:       0.239 [ms] (mean, across all concurrent requests)
    Transfer rate:          761.00 [Kbytes/sec] received
    
    Connection Times (ms)
                  min  mean[+/-sd] median   max
    Connect:        0    0   0.4      0       8
    Processing:     0   24  55.7      6     377
    Waiting:        0   23  55.7      6     376
    Total:          0   24  55.7      6     377
    
    Percentage of the requests served within a certain time (ms)
      50%      6
      66%     10
      75%     14
      80%     17
      90%     47
      95%    162
      98%    246
      99%    288
     100%    377 (longest request)

```
