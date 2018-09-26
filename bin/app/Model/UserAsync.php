<?php

namespace Model;


use Component\Orm\Model\Model;

class UserAsync extends Model
{
        protected $driver = 'async';
        public function __construct()
        {
                $this->configName = 'asyncUsers';
                parent::__construct();
        }
}