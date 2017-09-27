<?php


namespace Library\Task;

use Kernel\Core;
use Kernel\Core\Cache\Type\Hash;
use Kernel\Server;
use Kernel\Swoole\SwooleHttpServer;
use Library\Crawler\Crawler;
use Library\Crawler\CrawlerTable;

class CrawlerTask
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
        protected $table;

        public function __construct(Server $httpServer)
        {
                $this->server = $httpServer->getServer();
        }

        public function getTaskStatus(string $name): array
        {
                $table = $this->getTable(self::KEY .$name);
                $response =  $table->getAll();
                return $response;
        }

        public function start(array $data)
        {
                $task = Crawler::getCrawler($data);
                $task->runOne($data['url']);
                while (true) {
                        try {
                                if ($data['count'] > 0) {
                                        if ($task->getGot() >= $data['count']) {
                                                break;
                                        }
                                }
                                $url = $task->getUrl();
                                echo "url:" . $url . "\r\n";
                                if ($url == '') {
                                        break;
                                }
                                $task->runOne($url);
                        } catch (\Exception $exception) {
                                file_put_contents('exception', date('Y-m-d H:i:s') . ":\r\n" . $exception->getTraceAsString() . "\r\n\r\n", FILE_APPEND);
                        }
                        //不同workerId定时器共用一个问题  包括swoole_timer_tick swoole_time_afer都有此问题
                        //需要在while(true)里面加入休眠释放CPU控制权
                        //还有一个可能，我测试的机器是虚拟机，单核
                        //可能由于CPU控制权限问题造成错乱暂时无法定位
                        //usleep(20000);
                }
        }


        public function reload(array $data)
        {
                $hash = $this->getTable(self::KEY .$data['name']);
                $cache = $hash->getAll();
                if(isset($cache['processId']) and $cache['stop'] == '0') {
                        if (!empty($cache['processId'])) {
                                \swoole_process::kill($cache['processId']);
                                \swoole_process::wait(true);
                        }
                }
                $this->start($data);
        }

        public function getTable(string $key) : CrawlerTable
        {
        	return new CrawlerTable($key, $this->server);
        }



}