## 关于Agile Swoole

一个高性能的PHP开发框架（swoole）

###
    特性
        1.支持MVC
        2.支持自定义常驻进程
        3.支持不同的控制器类型
        4.路由自定义
        5.分布式（待开发）
        
### 快速开始
composer require fresh-li/agile-swoole

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
    return [
        'route' =>[
            'get'     =>      [
                [
                    'path'          =>      '/',
                    'dispatch'      =>      'hello'
                ],
                [
                    'path'          =>      '/welcome',
                    'dispatch'      =>      ['Controller\Welcome','index']      
                ]
            ],
            'post'  =>      [
                [
                    'path'      =>  '/'
                    'dispatch'      =>      'this is post'
                ],
                [
                     'path'          =>      '/welcome',
                     'dispatch'      =>      ['Controller\Welcome','index']      
                ]
            ]
        ]
    ];
    
    GET: localhost:9550
    hello
    
    GET: localhost:9550/welcome
    hello world!
    
    POST: localhost:9550
    this is post
    
    GET: localhost:9550/welcome
    hello world!
```

### 3种不同的触发模式
```
    class Sync extends BasicController{
        public function index()
        {
            return 'ff';
        }
    }
    
    {"code":0,"response":"ff"}
    
    class Process extends BasicController{
            public function index()
            {
                return 'ff';
            }
    }
    {"code":0,"response":{"processId":"{$processId}"}}
    
    class Task extends BasicController{
            public function index()
            {
                return ff;
            }
    }
    {"code":0}
```

### 常驻内存任务
    
```
    $serverProcess = new ServerProcess();
    $serverProcess->addProcess(function(){
        while(true){
            //do some things
        }
    });
```
