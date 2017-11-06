<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 17-9-20
 * Time: 下午3:33
 */

namespace Component\Producer;


use Component\Controller\Controller;

interface IProducer
{
        public function addProducer(Controller $controller, string $method, array $args = []) : IProducer;
        public function addAfter(\Closure $closure): IProducer;
        public function addBefore(\Closure $closure):IProducer;
        public function run(): array ;
        public function getProcessId() : int;
}