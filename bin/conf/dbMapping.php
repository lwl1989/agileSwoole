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
                'driver'        =>      'mongodb',
                'database'      =>      'test',
                'table'         =>      'users'
        ]
];