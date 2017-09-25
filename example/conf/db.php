<?php

/**
 * db connections
 */
return [
        'mongodb'       =>      [
                'uri'                   =>      'mongodb://127.0.0.1:56790,127.0.0.1:56791,127.0.0.1:56790/',
                'uriOptions'            =>      [],
                'driverOptions'         =>      [
                        'replicaSet'            => 'rs',
                        'readPreference'        => 'primary'
                ]
        ],
        'redis'         =>      [
                'host'  =>      '127.0.0.1',
                'port'  =>      '6379',
                'db'    =>      5
        ],
        'pdo'         =>      [
                'dns'           =>      '127.0.0.1',
                'user'          =>      'root',
                'password'      =>      'sa'
        ],
];