<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 17-9-25
 * Time: 上午10:24
 */

namespace Controller;


use Component\Controller\Controller;
use Library\Crawler\CrawlerTable;

class CrawlerAction extends Controller
{
        protected $producerType = 'sync';
        public function get(string $task)
        {
                $crawlerTask = new CrawlerTable($task, $this->server->getServer());
                $status = $crawlerTask->getAll();
                return $status;
        }

        public function delete(string $task)
        {
                $crawlerTask = new CrawlerTable($task, $this->server->getServer());
                $status = $crawlerTask->getAll();
                if(!empty($status) and $status['stop'] == 0) {
                        \swoole_process::kill($status['processId']);
                        \swoole_process::wait(true);
                }
                return [];
        }

}