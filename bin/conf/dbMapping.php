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
                'driver'        =>      'sMysql',
                'database'      =>      'test',
                'table'         =>      'users'
        ]
];