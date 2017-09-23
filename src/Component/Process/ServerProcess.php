<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/23 0023
 * Time: 14:26
 */

namespace Component\Process;


use Kernel\Core;
use Swoole\Process;

class ServerProcess implements IProcess
{
        public function setClosure(\Closure $closure)
        {
                $process = new Process($closure);
                Core::getInstant()->get('server')->getServer()->addProcess($process);
        }

}