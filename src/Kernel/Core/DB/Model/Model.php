<?php

namespace Kernel\Core\DB\Model;


use Kernel\Core;
use Kernel\Core\Conf\Config;
use Kernel\Core\DB\Query\IQuery;
use Psr\Log\InvalidArgumentException;

class Model implements IModel
{
	protected $driver;
	protected $database;
	protected $table;
	/** @var IQuery */
	protected $db;
	protected $fields = '*';
	protected $configName;
	public function __construct()
	{
		/** @var $config Config */
		$core = Core::getInstant();
		$config = $core->get('config');
		$dbConfig = $config->get($this->configName);
		$this->driver = $dbConfig['driver'];
		$this->database = $dbConfig['database'];
		$this->table = $dbConfig['table'];
		switch ($this->driver) {
			case 'pdo':
				$this->db = $core->get(Core\DB\Query\Mysql::class);
				break;
			case 'mongodb':
				$this->db = $core->get(Core\DB\Query\Mongodb::class);
				break;
			default:
				throw new InvalidArgumentException('can\'t use '. $this->driver, Core\Exception\ErrorCode::DB_DRIVER_ERROR);
		}
	}

	public function insert(array $data): IQuery
	{
		return $this->db->insert($data, $this->database.'.'.$this->table);
	}

	public function update(array $data): IQuery
	{
		return $this->db->update($data)->from($this->database.'.'.$this->table);
	}

	public function delete(array $data = []): IQuery
	{
		if(empty($data)) {
			return $this->db->delete()->from($this->database.'.'.$this->table);
		}
		return $this->db->delete($data)->from($this->database.'.'.$this->table);
	}

	public function select(string $fields = ''): IQuery
	{
		if(!empty($fields)) {
			$this->fields = $fields;
		}
		return $this->db->select($this->fields);
	}

}