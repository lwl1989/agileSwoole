<?php
namespace Kernel\Core\Route;

use Component\Controller\BasicController;
use Component\Producer\Producer;
use Kernel\Core\Route\Cute\Router;
use Kernel\Core;
use Kernel\Core\Conf\Config;


class CuteRoute implements IRoute
{
	protected $routes = [];
	/* @var $router Router */
	private  $router;

	public static function getInstance() : IRoute
	{
		return Core::getInstant()->getContainer()->get('route');
	}

	public function __construct(Config $config)
	{
		$this->routes = $config->get('route');
		$this->router = new Router();
		$this->_init();
	}

	protected function _init()
	{
		$methods = ['get','post','head','options','delete','put','patch'];
		foreach ($this->routes as $method=>$routes) {
			if(!in_array($method, $methods)) {
				throw new \Exception('router method error: '. $method);
			}
			foreach ($routes as $route) {
				if(isset($route['path']) and isset($route['dispatch'])) {
					$this->add($method, $route['path'], $route['dispatch']);
				}
			}

		}
	}

	public function add(string $method, string $path, $closure): IRoute
	{
		$this->router->$method($path, $closure);
		return $this;
	}


	public function get(string $path, \Closure $closure = null)
	{
		return $this->router->get($path, $closure);
	}

	public function post(string $path, \Closure $closure = null)
	{
		return $this->router->post($path, $closure);
	}

	public function head(string $path, \Closure $closure = null)
	{
		return '';
	}

	public function options(string $path, \Closure $closure = null)
	{
		return '';
	}

	public function delete(string $path, \Closure $closure = null)
	{
		return $this->router->delete($path, $closure);
	}

	public function patch(string $path, \Closure $closure = null)
	{
		return $this->router->pathch($path, $closure);
	}

	public function put(string $path, \Closure $closure = null)
	{
		return $this->router->put($path, $closure);
	}

	/**
	 * @return Router
	 */
	public function getRouter()
	{
		return $this->router;
	}

        /**
         * @param string $path
         * @param string $method
         * @return false|Cute\Route
         */
	private function _match(string $path, string $method = 'get')
	{
		return $this->router->match($path, $method);
	}

        /**
         * 解析路由
         * @param string $path
         * @param string $method
         * @return array
         */
	public function dispatch(string $path, string $method = 'get')
	{
		$route = $this->_match($path, $method);
		$obj = [];

		if($route) {
			$call = $route->getStorage();
			$params = $route->getParams();
			if (is_array($call)) {
				if (class_exists($call[0]) and $call[0] instanceof BasicController) {
                                        /** @var BasicController $obj */
					$obj = Core::getInstant()->getContainer()->build($call[0]);
					$type = $obj->getProducerType();
					return $this->_runProducer($obj, $call[1], $params, $type);
				}else{
                                        $obj = $call;
                                }
			}
			if (is_string($call)) {
				$obj = [$call];
			}
		}
		return $obj;
	}

        /**
         * 執行任務
         * @param BasicController $controller
         * @param string $method
         * @param array $params
         * @param string $type
         * @return array
         */
	private function _runProducer(BasicController $controller, string $method, array $params , string $type) : array
        {
                $producer = Producer::getProducer($type);
                $producer->addProducer($controller, $method, $params);
                return $producer->run();
        }
}