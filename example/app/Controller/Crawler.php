<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 17-9-8
 * Time: 下午6:00
 */

namespace Controller;


use Component\Controller\Controller;
use Library\Crawler\CrawlerTable;
use Library\Task\CrawlerTask;

class Crawler extends Controller
{
        const KEY = 'crawler:list:';
        const DAY_SECOND = 86400;
        const MIN_INTERVAL = 3600;
        const MAX_INTERVAL = self::DAY_SECOND;
        /*** @var CrawlerTask */
        protected $crawlerTask;
        protected $data;


        public function createBefore()
        {
                $this->_diffArray();
                return;
        }

        public function createAfter()
        {
                $data = $this->_getData();
                $table = new CrawlerTable($data['name'], $this->server->getServer());
                $producer = $this->getProducer();
                $table->setField('processId', $producer->getProcessId());
                $table->setField('stop','0');
        }

        public function updateAfter()
        {
                $this->createAfter();
        }


        public function updateBefore()
        {
                $this->_diffArray();
                return;
        }

	public function create()
        {
                $data = $this->_getData();
                $this->crawlerTask = new CrawlerTask($this->server);
                $name = $data['name'];
                $status = $this->crawlerTask ->getTaskStatus($name);
                if(!empty($status) and $status['stop'] == 0) {
                        return $status;
                } else {
                        $this->crawlerTask ->start($data);
                }
                return [];
        }

        public function update()
        {
                $data = $this->_getData();
                $this->crawlerTask = $crawlerTask = new CrawlerTask($this->server);
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



        /**
         * 检查参数是否正确
         * @return bool
         * @throws \Exception
         */
        private function _diffArray() : bool
        {
                $keys = ['target', 'task_en_name', 'number_count', 'interval', 'channel_rule'];
                $diff = array_diff($keys, array_keys($_POST));
                if (!empty($diff)) {
                        throw new \Exception('params lost ' . json_encode($diff));
                }

                return true;
        }

        /**
         * 获取数据
         * @return array
         */
        private function _getData() : array
        {
                $params = [
                        'name'  =>  $_POST['task_en_name'],
                        'flag' => $_POST['task_en_name'],
                        'count' => $_POST['number_count']??'',
                        'url' =>  str_replace('\/','/',$_POST['target']??''),
                        'interval' => $_POST['interval']??'',
                        'rule' => str_replace('\/','/',$_POST['channel_rule']??''),
                ];
                $this->data = $params;
                return $params;
        }


}