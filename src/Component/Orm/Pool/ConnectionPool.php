<?php

namespace Component\Orm\Pool;


use Kernel\AgileCore;
use Kernel\Core\Conf\Config;
use Kernel\Core\IComponent\IConnection;
use Kernel\Core\IComponent\IConnectionPool;

class ConnectionPool implements IConnectionPool
{
        protected $mysqlPool = [];
        protected $mongoPool = [];

        protected $config = [];

        public function __construct(Config $config)
        {
                $this->config = [
                        'mysql'         =>      $config->get('mysql'),
                        'mongo'         =>      $config->get('mongo')
                ];
        }

        public function init(): IConnectionPool
        {
                if(isset($this->config['mysql']['pool'])) {
                        $class = $this->_init('mysql');
                        array_push($this->mysqlPool,['status'=>self::CONNECTION_STATUS_FREE,'obj'=>$class]);
                }
                if(isset($this->config['mysql']['pool'])) {
                        $class = $this->_init('mongodb');
                        array_push($this->mysqlPool,['status'=>self::CONNECTION_STATUS_FREE,'obj'=>$class]);
                }
                return $this;
        }

        private function _init(string $connectionName) : IConnection
        {
                $class = AgileCore::getInstant()->getWorkerStartClassName($connectionName);
                if(!empty($class)) {
                        throw new \Exception($connectionName.' class not exists');
                }
                $config = $this->config[$connectionName];
                unset($config['pool']);
                $class = new $class($config);
                return $class;
        }


}