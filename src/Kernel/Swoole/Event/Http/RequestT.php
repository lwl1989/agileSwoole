<?php


namespace Kernel\Swoole\Event\Http;

use Kernel\AgileCore as Core;
use Kernel\Core\Cache\Type\Hash;
use Kernel\Core\Cache\Redis;
use Kernel\Swoole\Event\Event;
use Kernel\Swoole\Event\EventTrait;
use Library\Crawler\Crawler;

class RequestT implements Event
{
        use EventTrait;
        /* @var  \swoole_http_server $server*/

        protected $action = '';
        protected $actionParams = [];

        protected $server;
        protected $redis;


        const ACTION_CRAWLER = 'crawler';
        const ACTION_START = 'start';
        const ACTION_KILL = 'kill';
        const ACTION_RELOAD = 'reload';
        const ACTION_STOP = 'stop';
        const KEY = 'crawler:list:';
        const DAY_SECOND = 86400;
        const MIN_INTERVAL = 3600;
        const MAX_INTERVAL = self::DAY_SECOND;

        public function __construct(\swoole_http_server $server)
        {
                $this->server = $server;
        }

        public function _check($data)
        {
                if(isset($data['action']) and isset($data['flag'])) {
                        switch ($data['action']) {
                                case self::ACTION_START:
                                case self::ACTION_CRAWLER:
                                        return $this->_crawler($data);
                                case self::ACTION_KILL:
                                case self::ACTION_STOP:
                                        return $this->_stop($data);
                                case self::ACTION_RELOAD:
                                        return $this->_reload($data);
                                default:
                                        return ['code'=>0];
                        }
                }
                return ['code'=>0];
        }

        public function getHash(string $key) :Hash
        {
                $config = Core::getInstant()->get('config');
                $redis = new Redis($config, false);
                $class = new Hash($redis);
                $class->setKey($key);
                return $class;
        }

        public function _crawler(array $data)
        {
                $hash = $this->getHash(self::KEY.$data['flag']);
                if($hash->hasKey()) {
                        $cache = $hash->getAll();
                        if($cache['stop'] == 1) {
                                return $this->_start($data);
                        }
                        return $hash->getAll();
                }
                return $this->_start($data);
        }

        public function _stop(array $data)
        {
                $processId = -1;
                $hash = $this->getHash(self::KEY.$data['flag']);
                $cache = $hash->getAll();
                $hash->setField('stop', 1);
                if (isset($cache['processId']) and $cache['stop'] == '0') {
                        if (!empty($cache['processId'])) {
                                $this->_delProcess($cache['processId']);
                        }
                }
                unset($hash);
                return ['processId'=>$processId];
        }

        public function _reload(array $data)
        {
                $this->_stop($data);
                return $this->_start($data);
        }

//        private function _start(array $data) : array
//        {
//                $hash = $this->getHash(self::KEY.$data['flag']);
//                $processId = $this->_doCrawler($data);
//                if(isset($data['interval'])) {
//                        $tickId = $this->server->tick($data['interval']*1000,function ($tickId) use ($data){
//                                $hash = $this->getHash(self::KEY.$data['flag']);
//                                $cache = $hash->getAll();
//                                if(isset($cache['processId'])) {
//                                        echo "kill processId".$cache['processId'].PHP_EOL;
//                                        \swoole_process::kill($cache['processId']);
//                                        \swoole_process::wait(true);
//                                };
//                                if(isset($cache['tickId']) and $cache['tickId'] != $tickId) {
//                                        if($cache['workerId'] != $this->server->worker_id) {
//                                                $this->server->sendMessage(json_encode(["action" => "killTick", "tickId" => $tickId]));
//                                        }else {
//                                                \swoole_timer_clear($tickId);
//                                        }
//                                        $hash->setField('tickId', $tickId);
//                                        $hash->setField('workerId', $this->server->worker_id);
//                                }
//                                $hash->setField('processId', $this->_doCrawler($data));
//                        });
//                        $hash->setField('tickId',$tickId);
//                }
//                $hash->setField('workerId', $this->server->worker_id);
//                $hash->setField('processId', $processId);
//                unset($hash);
//                return ['processId'=>$processId,'tickId'=>$tickId??'','workerId'=>$this->server->worker_id];
//        }

        public function _doCrawler(array $data)
        {
                $process = new \swoole_process(function () use ($data){
                      $task = Crawler::getCrawler($data);
                       $task->run();

                });
                $processId =  $process->start();
                echo 'new Process '.$data['flag'].' is '.$processId.PHP_EOL;
                $process->name($data['flag']);
                return $processId;
        }

        public function _start(array $data)
        {
                $hash = $this->getHash(self::KEY.$data['flag']);
                $cache = $hash->getAll();

                if(isset($cache['stop'])) {
                        if($cache['stop'] == 0) {
                                $processId = $this->_doCrawler($data);
                                $hash->setField('processId', $processId);
                        } else {
                                $processId = '-1';
                        }
                } else {
                        $processId = $this->_doCrawler($data);
                        $hash->setField('processId', $processId);
                        $hash->setField('stop', 0);
                }

                $this->server->after($data['interval'] * 1000, function () use ($data) {
                        $hash = $this->getHash(self::KEY . $data['flag']);
                        $cache = $hash->getAll();
                        if (isset($cache['processId']) and $cache['stop'] == '0') {
                                $this->_start($data);
                                $this->_delProcess($cache['processId']);
                        }
                        //不同workerId定时器共用一个问题  包括swoole_timer_tick swoole_time_afer都有此问题
                });

                return ['processId' => $processId];
        }

        public function _delProcess($processId)
        {
                $this->server->tick(20,function ($id) use($processId){
                        echo "kill processId". $processId . PHP_EOL;
                        \swoole_process::kill($processId);
                        \swoole_process::wait(true);
                        \swoole_timer_clear($id);
                });
        }
}