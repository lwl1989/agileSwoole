<?php

/**
 * route
 */
return [
        'route' =>[
                'get'     =>      [
                        [
                                'path'          =>      '/',
                                'dispatch'      =>      'hello agile!'
                        ],
                        [
                                'path'          =>      '/t',
                                'dispatch'      =>     [\Controller\Welcome::class,'index']
                        ],
                        [
                                'path'          =>      '/model/:user',
                                'dispatch'      =>     [\Controller\Welcome::class,'userInsert']
                        ],
                        [
                                'path'          =>      '/process',
                                'dispatch'      =>      [\Controller\Process::class, 'run'],
                                'before'        =>      [\Controller\Process::class, 'before'],
                                'after'         =>      [\Controller\Process::class, 'after'],
                                'type'          =>      \Component\Producer\Producer::PRODUCER_PROCESS
                        ]

                ],
		'delete'	=>	[
			 [
                                'path'          =>      '/',
                                'dispatch'      =>      'hello agile!'
                        ],
		]
        ]
];
