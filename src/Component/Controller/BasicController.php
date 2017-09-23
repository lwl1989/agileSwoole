<?php

namespace Component\Controller;



use Component\Producer\Producer;
use Component\Producer\IProducer;
use Kernel\Server;

class BasicController
{
        protected $server;
        protected $producer;
        protected $producerType = 'process';
        public function __construct(Server $server)
        {
                $this->server = $server;
                $this->producer = Producer::getProducer($this->getProducerType());
        }

        /**
         * @return string
         */
        public function getProducerType(): string
        {
                return $this->producerType;
        }

        /**
         * @return IProducer
         */
        public function getProducer(): IProducer
        {
                return $this->producer;
        }


}