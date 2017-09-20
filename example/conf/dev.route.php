<?php

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
			]

		],
		'post'  =>      [
			[
				'path'          =>      '/crawler',
				'dispatch'      =>      [\Controller\Crawler::class, 'run']
			],
			[
				'path'          =>      '/email',
				'dispatch'      =>      'email'
			]
		]
	]
];