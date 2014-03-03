<?php

namespace Omelettes\Migration\Fixture;

use Omelettes\Logger,
	Omelettes\Migration\AbstractMigration;
use Zend\Db\Adapter\Adapter as DbAdapter,
	Zend\Db\Sql;

abstract class AbstractFixture
{
	protected $fixture;
	
	/**
	 * @var Logger
	 */
	protected $logger;
	
	/**
	 * @var DbAdapter
	 */
	protected $dbAdapter;
	
	/**
	 * @var Sql\Sql
	 */
	protected $sql;
	
	public function __construct(DbAdapter $adapter, Logger $logger)
	{
		$this->dbAdapter = $adapter;
		$this->logger = $logger;
	}
	
	abstract public function parse($fixture);
	
	abstract public function insert();
	
	public function getAdapter()
	{
		return $this->dbAdapter;
	}
	
	protected function getSql()
	{
		if (!$this->sql) {
			$sql = new Sql\Sql($this->getAdapter());
			$this->sql = $sql;
		}
	
		return $this->sql;
	}
	
	protected function insertRow($tableName, $data)
	{
		$this->logger->debug("Inserting $tableName row");
		
		$sql = $this->getSql();
		$insert = $sql->insert($tableName)->values($data);
		$statement = $this->getSql()->prepareStatementForSqlObject($insert);
		$results = $statement->execute();
		
		return $results;
	}
	
}
