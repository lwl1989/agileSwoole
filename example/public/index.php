<?php

include '../../vendor/autoload.php';
include '../../src/Kernel/Core.php';
$app = new \Kernel\Core([ realpath('../../src'), realpath('../app')], [realpath('../conf')]);
$container = $app->getContainer();
$container->alias('redis', Redis::class);
//将ServerInterface设置为 HttpServer并且设置别名为http
$container->singleton(\Kernel\Server::class, \Kernel\Swoole\SwooleHttpServer::class, 'http');
//set default route
$container->singleton(\Kernel\Core\Route\IRoute::class, \Kernel\Core\Route\CuteRoute::class, 'route');
/* @var \Kernel\Swoole\SwooleHttpServer $http */
$http = $app->get('http');

//http server
$http->setTask('request', function (\swoole_http_request $request, \swoole_http_response $response) use($container){
	$_POST = json_decode($request->rawContent(), true);
	try {
		$data = ['code'=>0,'response'=> $container->get('route')->dispatch(
			$request->server['request_uri'],strtolower($request->server['request_method'])
		)];
	}catch (Exception $exception) {
		$data = ['code'=>$exception->getCode()>0?$exception->getCode():1, 'response'=>$exception->getMessage()];
	}
	if(isset($data['response']['view'])) {
		$res = $data['response'];
		if(isset($res['response'])) {
			$res['response_alisa'] = $res['response'];
			unset($res['response']);
		}
		extract($res);
		ob_start();
		include ($data['response']['view']);
		$content = ob_get_contents();
		ob_clean();
	}else {
		$content = json_encode($data);
	}
	$response->end($content);
});

$http->createTable('crawler');
$app->serverStart($http);
