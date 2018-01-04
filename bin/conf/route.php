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

                ],
		'delete'	=>	[
			 [
                                'path'          =>      '/',
                                'dispatch'      =>      'hello agile!'
                        ],
		]
        ]
];
