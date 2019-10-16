<?php


namespace Kernel;


interface Server
{
        public function start() : Server;
        public function shutdown(\Closure $callback = null) : Server;
        public function getServer() : \Swoole\Server;
        public function setTask(string $event, \Closure $closure) : Server;
}