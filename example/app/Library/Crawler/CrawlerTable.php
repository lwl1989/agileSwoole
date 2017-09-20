<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 17-9-20
 * Time: 下午6:30
 */

namespace Library\Crawler;


use Kernel\Server;

class CrawlerTable
{
        protected $key;
        protected $table;
        public function __construct(string $key, Server $server)
        {
                $this->table  = $server->getServer()->crawlerTable;
                $this->key = $key;
        }

        public function getAll() :array
        {
                $value = $this->table->get($this->key);
                if(!is_array($value)) {
                        $value = [];
                }
                return $value;
        }

        public function setField(string $field, int $value)
        {
                $values = $this->getAll();
                $this->table->set($this->key, array_merge([$field=>$value],$values));
                return $this;
        }

        public function delKey()
        {
                $this->table->del($this->key);
        }
        public function hasKey()
        {
                $value = $this->getAll();
                return !empty($value) ? true : false;
        }
}