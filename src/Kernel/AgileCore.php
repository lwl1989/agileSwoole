<?php


namespace Kernel;

use Component\Orm\Connection\Mongodb;
use Component\Orm\Connection\Mysql;
use Component\Orm\Pool\ConnectionPool;
use Kernel\Core\Conf\Config;
use Kernel\Core\Di\Container;
use Kernel\Core\Di\IContainer;

class AgileCore
{
    /* @var AgileCore $core */
    public static $core = null;
    protected $container;
    protected $reflection;

    protected $workerClassMap = [
        'pool' => ConnectionPool::class,
        'mysql' => Mysql::class,
        'mongodb' => Mongodb::class
    ];

    /**
     * 核心类构造
     * AgileCore constructor.
     * @param array $paths
     * @param array $confPath
     * @throws \Exception
     */
    public function __construct(array $paths = [], array $confPath = [])
    {
        if(!defined('APP_PATH')) {
            define('APP_PATH', $paths[0]);
        }
        $this->isOne();
        $this->autoload($paths);
        $this->container = new Container();
        $this->container->bind('container', $this->container);
        /** @var Config $config */
        $config = $this->container->bind('config', Config::class)->get('config');
        $config->setLoadPath($confPath);
        $this->container->alias('Psr\Container\ContainerInterface', $this->container);
    }

    /**
     * 判断core类有没有被重复实例化
     * @throws \Exception
     */
    private function isOne()
    {
        if (self::$core !== null) {
            throw new \Exception('core has construct');
        }
        self::$core = $this;
    }

    /**
     * 获取Core对象
     * @return AgileCore
     * @throws \Exception
     */
    public static function getInstant(): AgileCore
    {
        if (self::$core === null) {
            throw new \Exception('core is not construct');
        }
        return self::$core;
    }

    /**
     * 注册加载SRC下文件
     * @param array $paths
     */
    public function autoload(array $paths = [])
    {
        if (empty($paths)) {
            return;
        }
        spl_autoload_register(function (string $class) use ($paths) {

            $file = DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';

            foreach ($paths as $path) {
                if (is_file($path . $file)) {
                    include($path . $file);
                    return true;
                }
            }
            return false;
        });
    }

    /**
     * 获取指定对象
     * @param $name
     * @return mixed|object
     * @throws Core\Di\ObjectNotFoundException
     */
    public function get($name)
    {
        if (isset($this->workerClassMap[$name])) {
            return $this->container->get($this->workerClassMap[$name]);
        }
        return $this->container->get($name);
    }

    /**
     * @param Server $server
     */
    public function serverStart(Server $server)
    {
        $server->start();
    }

    /**
     * 獲取容器
     * @return IContainer
     */
    public function getContainer(): IContainer
    {
        return $this->container;
    }

    /**
     * @param string $name
     * @return string
     */
    public function getWorkerStartClassName(string $name): string
    {
        return isset($this->workerClassMap[$name]) ? $this->workerClassMap[$name] : '';
    }

}