<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 17-9-9
 * Time: 下午4:01
 */

namespace Controller;


use Component\Controller\Controller;

class Welcome extends Controller
{
	protected $driver;
	protected $database;
	protected $table;
	protected $db;
	protected $producerType = 'sync';
	public function index()
	{

		return [
			'code'  =>      0,
			'view'  =>      realpath(__DIR__.'/../View/index.php')
		];
	}
}