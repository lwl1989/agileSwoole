<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 17-9-25
 * Time: 上午10:15
 */

namespace Performer;


use Component\Performer\BasicPerformer;
use Library\Task\CrawlerTask;

class CrawlerGet extends BasicPerformer
{
        protected $producerType = 'sync';
        public function get(string $task)
        {
                $crawlerTask = new CrawlerTask($this->server);
                return $crawlerTask->getTaskStatus($task);
        }
}