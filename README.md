# 关于Agile Swoole

一个高性能的PHP开发框架（swoole）

## 安装
    php 7.0+
    swoole 4.0+
    composer require fresh-li/agile-swoole:v4.0

## 特性
    
        1. 支持MVC
        2. 支持自定义常驻进程
        3. 支持多种任务模式
        4. 路由自定义事件
        5. 简单易用orm[可二次开发，实现接口，自动注入即可]
        6. 支持yaf
        7. 全面支持psr container psr http-message psr autoloader
        8. 全协程任务
        
## 压力测试

#### 测试机器
    

    
#### 测试命令

    cd bin
    php agile.php
    ab -c 100 -n 50000 http://127.0.0.1:9550/welcome
    
#### 测试结果

```

```   
        
## 快速开始

    composer require fresh-li/agile-swoole:dev-master
    cd bin
    php agile.php
    
    http://127.0.0.1:9550/welcome
	
## 路由

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

## 3种不同的触发模式
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

## 常驻内存任务,开启服务立马启用
    
```
    $serverProcess = new ServerProcess();
    $serverProcess->addProcess(function(){
        while(true){
            //do some things
        }
    });
```


## 支持yaf


```

```

## orm

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).