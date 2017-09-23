<?php
$server = new Swoole\Server('127.0.0.1', 9501);

//$process = new Swoole\Process(function($process) use ($server) {
//        while (true) {
//                $msg = $process->read();
//                var_dump($msg);
//                foreach($server->connections as $conn) {
//                        $server->send($conn, $msg);
//                }
//        }
//});

#$server->addProcess($process);
//use ($process)
//$server->on('request', function (swoole_http_request $request, swoole_http_response $response)  {
//
//      //  var_dump($request);
//      //  $process->write('ok');
//        $response->end('ok!');
//});
$server->on('receive', function ($serv, $fd, $from_id, $data){
        var_dump($data);
        //群发收到的消息
        //$process->write($data);
});

$server->start();