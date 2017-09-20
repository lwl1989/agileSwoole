<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 17-8-29
 * Time: 下午11:56
 */

namespace Library\Task;


use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;


class CrawlerGutto implements ITask
{
	public function run()
	{
		$client = new Client();
		$goutteClient = new Client();
		$guzzleClient = new GuzzleClient(array(
			'timeout' => 60,
		));
		$goutteClient->setClient($guzzleClient);
		$client->setHeader('X-Test', 'test');
		$crawler = $client->request('GET', 'http://blog.csdn.net/zht666/article/details/10373923');

		$images = $crawler->images();
		var_dump($crawler);
		return $images;

	}

}