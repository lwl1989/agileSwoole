<?php

namespace Component\Orm\Pool;


use Component\Orm\Connection\AsynMysql;
use Component\Orm\Connection\Mongodb;
use Component\Orm\Connection\Mysql;
use Kernel\AgileCore;
use Kernel\Core\Conf\Config;
use Kernel\Core\IComponent\IConnection;
use Kernel\Core\IComponent\IConnectionPool;

class ConnectionPool implements IConnectionPool
{
    protected $runtime = [];
    protected $mysqlPool = [];
    protected $mongoPool = [];

    protected $config = [];

    public function __construct(Config $config)
    {
        $this->config = $config->get('pool', false);
        if (empty($this->config)) {
            $this->config = [
                'mysql' => ['start' => 3, 'max' => 5],
            //    'mongo' => ['start' => 3, 'max' => 5]
            ];
        }
    }

    public function init(): IConnectionPool
    {
        $coreConfig = AgileCore::getInstance()->get('config');
        foreach ($this->config as $key => $value) {
            for ($i = 0; $i < $value['start']; $i++) {
                $this->addConnection(new AsynMysql($coreConfig), $key);
            }
        }
        return $this;
    }

    public function getConnection(string $connection)
    {
        return $this->generatorConnection($connection)->current();
    }

    protected function generatorConnection(string $connection)
    {
        switch ($connection) {
            case 'mysql':
                $connections = $this->mysqlPool;
                break;
            case 'mongo':
                $connections = $this->mongoPool;
                break;
            default:
                throw new \Exception('not support this connection with name: ' . $connection);
        }
        while (true) {
            if (empty($connections['conn'])) {
                //$connections['conn'] = [];
                continue;
            }
            $code = array_rand($connections['conn']);
            $con = $connections['conn'][$code];
            //$this->runtime[$code] = $con;
            unset($this->mysqlPool['conn'][$code]);
            yield $con;
        }
    }


    protected function addConnection(IConnection $connection, $connectionType = 'mysql')
    {
        if($connectionType == 'mysql') {
            $this->mysqlPool['conn'][$connection->hashCode()] = $connection;
        }
        if($connectionType == 'mongo') {
            $this->mongoPool['conn'][$connection->hashCode()] = $connection;
        }
    }


    public function free(IConnection $connection)
    {
//        $hash = $connection->hashCode();
//        if(isset($this->mysqlPool[$hash])) {
//            unset($this->runtime[$hash]);
//        }
        echo 'free with '.$connection->hashCode().PHP_EOL;
        $this->addConnection($connection);
    }
}

/**
 *
 * 直接使用mysql
Server Software:        swoole-http-server
Server Hostname:        localhost
Server Port:            9550

Document Path:          /welcome
Document Length:        2236 bytes

Concurrency Level:      10
Time taken for tests:   46.704 seconds
Complete requests:      1000
Failed requests:        0
Total transferred:      2386000 bytes
HTML transferred:       2236000 bytes
Requests per second:    21.41 [#/sec] (mean)
Time per request:       467.037 [ms] (mean)
Time per request:       46.704 [ms] (mean, across all concurrent requests)
Transfer rate:          49.89 [Kbytes/sec] received

 *
 * Server Software:        swoole-http-server
Server Hostname:        localhost
Server Port:            9550

Document Path:          /welcome
Document Length:        2236 bytes

Concurrency Level:      10
Time taken for tests:   0.348 seconds
Complete requests:      1000
Failed requests:        995
(Connect: 0, Receive: 0, Length: 995, Exceptions: 0)
Total transferred:      195010 bytes
HTML transferred:       47000 bytes
Requests per second:    2872.61 [#/sec] (mean)
Time per request:       3.481 [ms] (mean)
Time per request:       0.348 [ms] (mean, across all concurrent requests)
Transfer rate:          547.06 [Kbytes/sec] received

Connection Times (ms)
min  mean[+/-sd] median   max
Connect:        0    0   0.2      0       3
Processing:     0    1  12.1      0     177
Waiting:        0    1  12.1      0     177
Total:          0    2  12.2      1     178

 */