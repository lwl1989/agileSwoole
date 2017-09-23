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
use Swoole\Process;

class ProcessProducer implements IProducer
{
        protected $producer = [];
        protected $server;
        protected $after;
        protected $processId = 0;
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

        public function addAfter(\Closure $closure):IProducer
        {
                $this->after[] = $closure;
                return $this;
        }


        public function run() : array
        {
                try {
                        $process = new Process(function () {
                                call_user_func([$this->producer['obj'], $this->producer['method']], $this->producer['args']);
                                return 0;
                        });
                        $process->name('test');
                        $process->start();
                } catch (\Exception $exception) {
                        return ['code'=>1];
                }
                \swoole_process::signal(SIGCHLD, function($sig) {
                        //必须为false，非阻塞模式
                        while($ret =  \swoole_process::wait(false)) {
                                echo "PID={$ret['pid']} exists\n";
                        }
                });
                $this->processId = $process->pid;
                if(!empty($this->after)) {
                      foreach ($this->after as $closure) {
                              call_user_func($closure);
                      }
                }
                return ['code'=>0, 'processId' => $process->pid];
        }

        public function getProcessId(): int
        {
                return $this->processId;
        }


}