## About Swoole Framework

Swoole Framework is a high performance framework by PHP.
    
### quck start 

index.php
```
include '../vendor/autoload.php';
include '../src/Kernel/Core.php';
define('CORE_PATH',realpath('../src'));
define('APP_PATH',realpath('../app'));
define('CONF_PATH',realpath('../conf'));
$app = new \Kernel\Core([CORE_PATH, APP_PATH], [CONF_PATH]);
```

run commond:

	php index.php

hello word

	input localhost:9550 in brower

Other MVC operations are like any other framework

	Controller
	Model
	View


```
you can set http server:
//http server example
$http->setTask('request', function (\swoole_http_request $request, \swoole_http_response $response) use($container){
	$_POST = json_decode($request->rawContent(), true);
	try {
		$data = ['code'=>0,'response'=> $container->get('route')->dispatch(
			$request->server['request_uri'],strtolower($request->server['request_method'])
		)];
	}catch (Exception $exception) {
		$data = ['code'=>$exception->getCode()>0?$exception->getCode():1, 'response'=>$exception->getMessage()];
	}
	$response->end(json_encode($data));
});

you can set tcp server:
//tcp server example
$tcp->setTask('receive', function (\swoole_server $server, $fd, $data) use($container){
	$_POST = json_decode($data, true);
	try {
		$data = ['code'=>0,'response'=> $container->get('route')->dispatch(
			$data['path'],$data['method']
		)];
	}catch (Exception $exception) {
		$data = ['code'=>$exception->getCode()>0?$exception->getCode():1, 'response'=>$exception->getMessage()];
	}
	$server->send($fd, json_encode($data));
});
```


### example route

```
return [
	'route' =>[
		'get'     =>      [
			[
				'path'          =>      '/',
				'dispatch'      =>      'hello'
			],

		],
		'post'  =>      [
			[
				'path'          =>      '/crawler',
				'dispatch'      =>      [\Library\Task\CrawlerTask::class, 'run']
			]
		]
	]
];
```

### example request

    http post /crawler 
    
    response will be new \Library\Task\CrawlerTask()->run();
