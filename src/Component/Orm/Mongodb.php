<?php


namespace Component\Orm;

use Kernel\Core\IComponent\IConnection;
use MongoDB\Driver\Command;
use MongoDB\Driver\Manager;


class Mongodb implements IConnection
{
        protected $manager;
        public function __construct(array $config)
        {
                try {
                        $manager = new Manager($config['uri'], $config['uriOptions']??[]);
                        $command = new Command(['ping' => 1]);
                        $manager->executeCommand('db', $command);

                } catch (\exception $e) {
                        throw new \InvalidArgumentException('Connection failed: '.$e->getMessage(), $e->getCode());
                }

                $this->manager = $manager;
        }


}