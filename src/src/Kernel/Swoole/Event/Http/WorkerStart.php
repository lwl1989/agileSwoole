<?php


namespace Kernel\Swoole\Event\Http;

use Kernel\AgileCore;
use Kernel\Core\IComponent\IConnectionPool;
use Kernel\Swoole\Event\Event;
use Kernel\Swoole\Event\EventTrait;

class WorkerStart implements Event
{
        use EventTrait;
        /* @var  \swoole_http_server $server*/
        protected $server;

        public function __construct(\swoole_http_server $server)
        {
                $this->server = $server;
        }

        public function doEvent(\swoole_server $server, $workerId)
        {
                /** @var IConnectionPool $poolClass */
                $poolClass = AgileCore::getInstant()->get('pool');
                $poolClass->init();
                $this->doClosure();
        }
}