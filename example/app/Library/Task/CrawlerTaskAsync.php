<?php


namespace Library\Task;

use Kernel\Core;
use Kernel\Core\Cache\Type\Hash;
use Library\Crawler\Crawler;

class CrawlerTaskAsync implements ITask
{
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
        protected $data = [];
        protected $fatherTimer = -1;
        protected $sonTimer = -1;
        protected static $hash = null;

        public function __construct(\swoole_server $server)
        {
                $this->server = $server;
        }

        public function setData(array $data)
        {
                /**
                 * target=>url
                 * action=>action
                 * task_en_name=>flag
                 * number_count=>count
                 * interval=>interval
                 * channel_rule=>rule
                 */
                if ($data['action'] == self::ACTION_START or $data['action'] == self::ACTION_CRAWLER) {
                        $keys = ['target', 'action', 'task_en_name', 'number_count', 'interval', 'channel_rule'];
                } else {
                        $keys = ['task_en_name', 'action'];
                }
                $diff = array_diff($keys, array_keys($data));
                if (!empty($diff)) {
                        throw new \Exception('params lost ' . json_encode($diff));
                }
                $this->data = [
                        'action' => $data['action'],
                        'flag' => $data['task_en_name'],
                        'count' => $data['number_count']??'',
                        'url' => $data['target']??'',
                        'interval' => $data['interval']??'',
                        'rule' => $data['channel_rule']??'',
                ];
        }

        public function run()
        {
                $this->_clear();
                $this->_check();
        }

        public function _check()
        {
                $data = $this->data;
//                if (isset($data['interval'])) {
//                        if (self::MIN_INTERVAL > $data['interval'] or self::MAX_INTERVAL < $data['interval']) {
//                                return ['code' => 1, 'response' => 'interval value must be gt ' . self::MIN_INTERVAL . ' and lt ' . self::MAX_INTERVAL];
//                        }
//                }
                $this->getHash(self::KEY . $data['flag']);
                if (isset($data['action']) and isset($data['flag'])) {
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
                                        return ['code' => 1];
                        }
                }
                return ['code' => 1];
        }

        private function getHash(string $key): Hash
        {
                if(self::$hash == null) {
                        $config = Core::getInstant()->get('config');
                        $redis = new Core\Cache\Redis($config);
                        $class = new Hash($redis);
                        $class->setKey($key);
                        self::$hash = $class;
                }
                return  self::$hash;
                //return $class;
        }

        private function _crawler(array $data)
        {
                return $this->_start($data);
        }

        private function _stop(array $data)
        {
                $hash = $this->getHash(self::KEY . $data['flag']);
                $hash->setField('stop', 1);
                $this->server->finish('');
        }

        private function _reload(array $data)
        {
                $hash = $this->getHash(self::KEY . $data['flag']);
                $hash->setField('stop', 2);
        }

        private function _doCrawler(array $data)
        {
                $hash = $this->getHash(self::KEY . $data['flag']);

                if(isset($data['interval'])){
                        $this->server->tick($data['interval'] * 1000, function ($faId) use ($data, $hash){
                                echo "父队列ID".$faId.PHP_EOL;
                                $this->_clearSt();
                                $this->fatherTimer = $faId;
                                $this->_doCrawlerEvent($data, $hash);
                        });
                        $this->_doCrawlerEvent($data, $hash);
                }else{
                        $this->_doCrawlerEvent($data, $hash);
                }
        }

        private function _doCrawlerEvent($data,Hash $hash)
        {
                echo "初始化队列".PHP_EOL;
                $task = Crawler::getCrawler($data);  //初始化抓取队列
                $this->server->tick(200, function ($tickId) use($data,$task,$hash){
                        echo "子队列ID".$tickId.PHP_EOL;
                        $this->sonTimer = $tickId;
                        $cache = $hash->getAll();
                        if($cache['stop'] == 1) {
                                $this->_clear();
                                $hash->delKey();
                                $this->server->finish('');
                        }
                        if($cache['stop'] == 2) {
                                $this->_clear();
                                $hash->setField('stop',0);
                                $this->_doCrawler($data);
                        }
                        try {
                                if ($data['count'] > 0) {
                                        if ($task->getGot() >= $data['count']) {
                                                $this->_clearSt();
                                        }
                                }
                                $url = $task->getUrl();
                                echo "url:" . $url . "\r\n";
                                if ($url == '') {
                                        $this->_clearSt();
                                }
                                $task->runOne($url);
                        } catch (\Exception $exception) {
                                file_put_contents('exception', date('Y-m-d H:i:s') . ":\r\n" . $exception->getTraceAsString() . "\r\n\r\n", FILE_APPEND);
                        }
                });
        }

        private function _start(array $data)
        {
                $hash = $this->getHash(self::KEY . $data['flag']);
                $cache = $hash->getAll();

                if (isset($cache['stop'])) {
                        if (self::ACTION_RELOAD == $data['action']) {
                                $hash->setField('stop', 0);
                                $this->_doCrawler($data);
                        } else {
                                if ($cache['stop'] == 1) {
                                        $hash->setField('stop', 0);
                                        $this->_doCrawler($data);
                                } else {
                                        $this->_doCrawler($data);
                                }
                        }
                } else {
                        $hash->setField('stop', 0);
                        $this->_doCrawler($data);
                }
        }


        private function _clearSt()
        {
                if($this->sonTimer != -1) {
                        swoole_timer_clear($this->sonTimer);
                        $this->sonTimer = -1;
                }
        }

        private function _clearFt()
        {
                if($this->fatherTimer != -1) {
                        swoole_timer_clear($this->fatherTimer);
                        $this->fatherTimer = -1;
                }
        }

        private function _clear() {
                $this->_clearFt();
                $this->_clearSt();
        }

}