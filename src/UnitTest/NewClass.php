<?php


namespace UnitTest;


use Kernel\Core\Cache\Redis;
use Kernel\Core\Cache\Redis\Set;

class NewClass
{
        public function __construct(Redis $redis)
        {
                $urls = new class($redis) extends Set{
                        public function setKey(string $key) {
                                $this->_key = $key;
                        }
                };
                $urls1 = new class($redis) extends Set{
                        public function setKey(string $key) {
                                $this->_key = $key;
                        }
                };
                $urls->setKey('a');
                $urls1->setKey('b');
                $urls->addValue('1111');
                $urls1->addValue('22222');
        }
}