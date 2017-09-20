<?php


namespace Kernel\Swoole;


use Kernel\Core\Conf\Config;
use Kernel\Server;
use Kernel\Swoole\Event\Event;

class SwooleHttpServer implements Server
{
        const EVENT = [
                'request','task','finish'//,'packet','pipeMessage','task','finish','close'
        ];
        protected $server;
        protected $event = [

        ];
        protected $config;
        public function __construct(Config $config)
        {
        	$this->config = $config;
                $server = $config->get('server');
                if(empty($server)) {
                        throw new \Exception('config not found');
                }
                $this->server = new \swoole_http_server($server['host'], $server['port'], $server['mode'], $server['type']);
		//$this->createTable($config);
               // $extend = $config->get('event')['namespace'] ?? '';
                foreach (self::EVENT as $event) {
                //        $class = $extend.'\\'.ucfirst($event);

                //        if(!class_exists($class)) {
                                $class = '\\Kernel\\Swoole\\Event\\Http\\'.ucfirst($event);
                //        }
                        /* @var \Kernel\Swoole\Event\Event $callback */
                        $callback = new $class($this->server);
                        $this->event[$event] = $callback;
                        $this->server->on($event, [$callback, 'doEvent']);
                }
                $this->server->set($config->get('swoole'));
        }

        public function start(\Closure $callback = null): Server
        {
                if(!is_null($callback)) {
                        $callback();
                }
                $this->server->start();
                return $this;
        }

        public function shutdown(\Closure $callback = null): Server
        {
                // TODO: Implement shutdown() method.
        }

        public function close($fd, $fromId = 0) : Server
        {
                $this->server->close($fd, $fromId = 0);
                return $this;
        }

        public function getServer() : \swoole_server
        {
                return $this->server;
        }

        public function setTask(string $event, \Closure $closure) : Server
        {
                if(!isset($this->event[$event]) or !($this->event[$event] instanceof Event)) {
                        throw new \LogicException('设置任务失败');
                }
                $this->event[$event]->setEventCall($closure);
                return $this;
        }

        public function createTable(string $table)
        {
	        $crawlerConfig = $this->config->get($table);
	        $tableName = $table.'Table';
	        $table = new \swoole_table($crawlerConfig['max_process']);
	        $table->column('processId',\swoole_table::TYPE_INT);
	        $table->column('stop',\swoole_table::TYPE_INT);
	        $table->create();
	        $this->server->$tableName = $table;
        }

}