<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 17-9-20
 * Time: 下午3:33
 */

namespace Component\Producer;


use Component\Controller\BasicController;

interface IProducer
{
        public function addProducer(BasicController $controller, string $method, array $args = []) : IProducer;
        public function addAfter(\Closure $closure): IProducer;
        public function run(): array ;
        public function getProcessId() : int;
}