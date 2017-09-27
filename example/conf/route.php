<?php

/**
 * route
 */
return [
        'route' =>[
                'get'     =>      [
                        [
                                'path'          =>      '/',
                                'dispatch'      =>      [\Controller\Welcome::class, 'index']
                        ],
                        [
                                'path'          =>      '/test',
                                'dispatch'      =>      [\Library\Task\CrawlerGutto::class, 'run']
                        ],
                        [
                                'path'          =>      '/crawler/:name',
                                'dispatch'      =>      [\Controller\CrawlerAction::class, 'get']
                        ]

                ],
                'post'  =>      [
                        [
                                'path'          =>      '/crawler',
                                'dispatch'      =>      [\Controller\Crawler::class, 'create']
                        ],
                        [
                                'path'          =>      '/email',
                                'dispatch'      =>      'email'
                        ]
                ],
                'put'   =>      [
                        [
                                'path'          =>      '/crawler',
                                'dispatch'      =>      [\Controller\Crawler::class, 'update']
                        ]
                ],
                'delete'=>      [
                        [
                                'path'          =>      '/crawler/:name',
                                'dispatch'      =>      [\Controller\CrawlerAction::class, 'delete']
                        ]
                ]
        ]
];