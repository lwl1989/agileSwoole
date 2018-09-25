<?php

/**
 *
 */
return [
        'crawlerModel'  =>      [
                'driver'        =>      'mongodb',
                'database'      =>      'crawler',
                'table'         =>      date('Y-m-d')
        ],
        'users'  =>      [
                'driver'        =>      'pdo',
                'database'      =>      'test',
                'table'         =>      'users'
        ]
];