<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 17-9-20
 * Time: 下午6:17
 */

namespace Component\Producer;


use Kernel\Core;

class Producer
{
        public static function getProducer(string $type) : IProducer
        {
                switch ($type) {
                        case 'process':
                                $producer = Core::getInstant()->get(ProcessProducer::class);
                                break;
                        case 'sync':
                                $producer = Core::getInstant()->get(SyncProducer::class);
                                break;
                        case 'task':
                        default:
                                $producer = Core::getInstant()->get(TaskProducer::class);
                }
                return $producer;
        }
}