<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 17-9-20
 * Time: 下午3:33
 */

namespace Component\Producer;


use Component\Controller\BasicController;
use Kernel\Server;

class TaskProducer implements IProducer
{
        protected $producer = [];
        protected $server;
        public function __construct(Server $server)
        {
                $this->server = $server;
        }

        public function addProducer(BasicController $controller, string $method, array $args = []): IProducer
        {
                $this->producer = [
                        'obj'           =>      $controller,
                        'method'        =>      $method,
                        'args'          =>      $args
                ];
                return $this;
        }


        public function run() : array
        {
               $this->server->setTask('task', function (){
                        return call_user_func([$this->producer['obj'], $this->producer['method']], $this->producer['args']);
               });
               return ['code'=>0];
        }
        public function addAfter(\Closure $closure) :IProducer
        {
                // TODO: Implement addAfter() method.
                return $this;
        }
}