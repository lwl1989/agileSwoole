<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 17-9-25
 * Time: 上午10:24
 */

namespace Controller;


use Component\Controller\BasicController;
use Library\Task\CrawlerTask;

class CrawlerGet extends BasicController
{
        protected $producerType = 'sync';
        public function get(string $task)
        {
                $crawlerTask = new CrawlerTask($this->server);
                return $crawlerTask->getTaskStatus($task);
        }

}