<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/23 0023
 * Time: 14:23
 */

namespace Component\Process;


interface IProcess
{
        public function setClosure(\Closure $closure);
}