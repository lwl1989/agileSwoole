<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 17-9-20
 * Time: 下午3:33
 */

namespace Component\Producer;


use Component\Controller\Controller;
use Kernel\Server;

class TaskProducer implements IProducer
{
        protected $producer = [];
        protected $server;
        public function __construct(Server $server)
        {
                $this->server = $server;
        }

        public function addProducer(Controller $controller, string $method, array $args = []) : IProducer
        {
                $this->producer = [
                        'obj'           =>      $controller,
                        'method'        =>      $method,
                        'args'          =>      $args
                ];
                return $this;
        }


        public function getProcessId(): int
        {
            return 0;
        }

        public function run() : array
        {
               $data = json_encode($this->producer);
               $this->server->getServer()->task($data);
               return ['code'=>0];
        }


        public function addBefore(\Closure $closure):IProducer
        {
                return $this;
        }

        public function addAfter(\Closure $closure) :IProducer
        {
                // TODO: Implement addAfter() method.
                return $this;
        }

}