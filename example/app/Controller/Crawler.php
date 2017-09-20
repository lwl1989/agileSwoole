<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 17-9-8
 * Time: 下午6:00
 */

namespace Controller;


use Component\Controller\BasicController;
use Library\Task\CrawlerTask;

class Crawler extends BasicController
{
        const KEY = 'crawler:list:';
        const DAY_SECOND = 86400;
        const MIN_INTERVAL = 3600;
        const MAX_INTERVAL = self::DAY_SECOND;


        public function get(string $task)
        {
                $crawlerTask = new CrawlerTask($this->server);
                return $crawlerTask->getTaskStatus($task);
        }

	public function create()
        {
                $data = $this->_diffArray();
                $crawlerTask = new CrawlerTask($this->server);
                $name = $data['name'];
                $status = $crawlerTask->getTaskStatus($name);
                if(!empty($status) and $status['stop'] == 0) {
                        return $status;
                }else{
                        $table = $crawlerTask->getTable($name);
                        $producer = $this->getProducer();
                        $producer->addAfter(function () use($name, $table, $producer){
                                $table->setField('processId', $producer->getProcessId());
                                $table->setField('stop','0');
                        });
                        $crawlerTask->start($data);
                }
                return [];
        }

        public function update()
        {
                $data = $this->_diffArray();
                $crawlerTask = new CrawlerTask($this->server);
                $name = $data['name'];
                $status = $crawlerTask->getTaskStatus($name);
                if(!empty($status) and $status['stop'] == 0) {
                        $crawlerTask->stop($data);
                }
                $table = $crawlerTask->getTable($name);
                $producer = $this->getProducer();
                $producer->addAfter(function () use($name, $table, $producer){
                        $table->setField('processId', $producer->getProcessId());
                        $table->setField('stop','0');
                });
                $crawlerTask->start($data);
        }

        public function delete(string $task)
        {
                $crawlerTask = new CrawlerTask($this->server);
                $status = $crawlerTask->getTaskStatus($task);
                if(!empty($status) and $status['stop'] == 0) {
                        $crawlerTask->stop($task);
                }
                return [];
        }

        /**
         * 检查参数是否正确
         * @return array
         * @throws \Exception
         */
        private function _diffArray() : array
        {
                $data = $_POST;
                if(!isset($data['action'])) {
                        throw new \Exception('params lost action');
                }

                $keys = ['target', 'action', 'task_en_name', 'number_count', 'interval', 'channel_rule'];
                $diff = array_diff($keys, array_keys($data));
                if (!empty($diff)) {
                        throw new \Exception('params lost ' . json_encode($diff));
                }

                $params = [
                        'action' => $data['action'],
                        'flag' => $data['task_en_name'],
                        'count' => $data['number_count']??'',
                        'url' =>  str_replace('\/','/',$data['target']??''),
                        'interval' => $data['interval']??'',
                        'rule' => str_replace('\/','/',$data['channel_rule']??''),
                ];
                return $params;
        }


}