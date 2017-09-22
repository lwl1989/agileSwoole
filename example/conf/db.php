<?php

/**
 * db connections
 */
return [
        'mongodb'       =>      [
                'uri'                   =>      'mongodb://54.222.155.203:56790,54.222.182.136:56790,54.223.193.154:56790/',
                'uriOptions'            =>      [],
                'driverOptions'         =>      [
                        'replicaSet'            => 'rs',
                        'readPreference'        => 'primary'
                ]
        ],
        'redis'         =>      [
                'host'  =>      '54.222.155.203',
                'port'  =>      '12346',
                'db'    =>      5
        ],
        'pdo'         =>      [
                'dns'           =>      '127.0.0.1',
                'user'          =>      'root',
                'password'      =>      'sa'
        ],
];