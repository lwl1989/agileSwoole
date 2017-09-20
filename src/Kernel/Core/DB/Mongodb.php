<?php


namespace Kernel\Core\DB;


use Kernel\Core\Conf\Config;
use MongoDB\Client;


class Mongodb extends Client
{

        public function __construct(Config $config)
        {
                $config = $config->get('mongodb');

                parent::__construct($config['uri'], $config['uriOptions']??[], $config['driverOptions']??[]);
        }



}