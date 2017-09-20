<?php

namespace Kernel\Core\DB\Model;


use Kernel\Core\DB\Query\IQuery;

interface IModel
{
	public function insert(array $data):IQuery;
	public function update(array $data):IQuery;
	public function delete(array $data = []):IQuery;
	public function select(string $fields):IQuery;
}