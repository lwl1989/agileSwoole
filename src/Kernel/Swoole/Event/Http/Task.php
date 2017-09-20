<?php


namespace Kernel\Swoole\Event\Http;


use Kernel\Core;
use Kernel\Swoole\Event\Event;
use Kernel\Swoole\Event\EventTrait;
use Library\Crawler\Crawler;
use Core\Cache\Type\Hash;

class Task implements Event
{
        use EventTrait;
        /* @var  \swoole_http_server $server*/
        protected $server;
        protected $db;
        protected $redis;
        protected $config;
        protected $data;
        const KEY = 'crawler:list:';
        const BASE_NUM = 1000;

        public function __construct(\swoole_http_server $server)
        {
                $this->server = $server;
        }

        public function doEvent(\swoole_server $server, $taskId, $fromId, $data)
        {
                $this->data = $data;
                $this->doClosure();
        }

        public function doClosure()
        {
                if(!empty($this->data) and $this->callback != null) {
                        $this->params = [$this->data];
                        return call_user_func_array($this->callback, $this->params);
                }
                return ['code'=>0];
        }


}