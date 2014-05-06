<?php

namespace Omelettes\Migration;

use Omelettes\Logger;
use Zend\Db\Adapter\Adapter as DbAdapter,
	Zend\Db\Sql;

abstract class AbstractMigration
{
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
	
	abstract public function migrate();
	
	public function getAdapter()
	{
		return $this->dbAdapter;
	}
	
	protected function getConnection()
	{
		return $this->getAdapter()->getDriver()->getConnection();
	}
	
	public function beginTransaction()
	{
		$this->getConnection()->beginTransaction();
	}
	
	public function commitTransaction()
	{
		$this->getConnection()->commit();
	}
	
	public function rollbackTransaction()
	{
		$this->getConnection()->rollback();
	}
	
	protected function getSql()
	{
		if (!$this->sql) {
			$sql = new Sql\Sql($this->getAdapter());
			$this->sql = $sql;
		}
		
		return $this->sql;
	}
	
	protected function executeQueryString($sql)
	{
		$statement = $this->getAdapter()->query($sql);
		$statement->execute();
		
		return $this;
	}
	
	protected function tableExists($tableName)
	{
		$this->logger->debug("Checking for $tableName table", array('tag' => 'migration'));
		
		$select = $this->getSql()->select('pg_tables')->where(array('schemaname' => 'public', 'tablename' => $tableName));
		$statement = $this->getSql()->prepareStatementForSqlObject($select);
		$results = $statement->execute();
		
		return (count($results) > 0);
	}
	
	protected function tableHasColumn($tableName, $columnName)
	{
		$this->logger->debug("Checking for $columnName column on $tableName table", array('tag' => 'migration'));
		
		$table = new Sql\TableIdentifier('columns', 'information_schema');
		$select = $this->getSql()->select($table)->where(array('table_name' => $tableName, 'column_name' => $columnName));
		$statement = $this->getSql()->prepareStatementForSqlObject($select);
		$results = $statement->execute();
		
		return (count($results) > 0);
	}
	
	protected function tableCreate($tableName, $columns, array $primaryKeyColumns = array(), $extraDef = '')
	{
		if ($this->tableExists($tableName)) {
			throw new \Exception("Table $tableName already exists");
		}
		$this->logger->info("Creating $tableName table", array('tag' => 'migration'));
		
		$tableDef = '';
		foreach ($columns as $columnName => $columnDef) {
			$tableDef[] = sprintf('%s %s', $columnName, $columnDef);
		}
		if (!empty($primaryKeyColumns)) {
			$tableDef[] = 'PRIMARY KEY (' . implode(', ', $primaryKeyColumns) . ')';
		}
		if (!empty($extraDef)) {
			$tableDef[] = $extraDef;
		}
		$this->executeQueryString(sprintf('CREATE TABLE %s (%s)', $tableName, implode(', ', $tableDef)));
		
		return $this;
	}
	
	protected function createNamedTableWithView($tableName, array $columns = array(), array $viewExtraFields = array())
	{
		$columns = array_merge($this->getNamedItemsTableColumns(), $columns);
		$this->tableCreate($tableName, $columns);
		
		$viewName = $tableName . '_view';
		$selectFields = array_merge(array("$tableName.*"), array_keys($viewExtraFields));
		$viewDefinition = "SELECT " . implode(', ', $selectFields) .
			" FROM $tableName " . implode(' ', array_values($viewExtraFields));
		$this->viewCreate($viewName, $viewDefinition);
		
		return $this;
	}
	
	protected function createAccountBoundQuantaTableWithView($tableName, array $columns = array(), array $viewExtraFields = array())
	{
		$columns = array_merge($this->getAccountBoundNamedItemsTableColumns(), $columns);
		$this->tableCreate($tableName, $columns);
	
		$viewName = $tableName . '_view';
		$selectFields = array_merge(array("$tableName.*"), array_keys($viewExtraFields));
		$viewDefinition = "SELECT " . implode(', ', $selectFields) .
			" FROM $tableName " . implode(' ', array_values($viewExtraFields));
		$this->viewCreate($viewName, $viewDefinition);
	
		return $this;
	}
	
	protected function tableAddColumns($tableName, $columns)
	{
		if (!$this->tableExists($tableName)) {
			throw new \Exception("Table $tableName does not exist");
		}
		
		foreach ($columns as $columnName => $columnDef) {
			if ($this->tableHasColumn($tableName, $columnName)) {
				throw new \Exception("Table $tableName already has column $columnName");
			}
			$this->logger->info("Adding $columnName column to $tableName table", array('tag' => 'migration'));
			
			$this->executeQueryString(sprintf('ALTER TABLE %s ADD COLUMN %s %s', $tableName, $columnName, $columnDef));
		}
		
		return $this;
	}
	
	protected function insertFixture($fixturePath, array $overrideValues = array())
	{
		$this->logger->info('Inserting fixture: ' . $fixturePath, array('tag' => 'migration'));
		
		$fixture = new Fixture\Xml($this->getAdapter(), $this->logger);
		$fixture->parse($fixturePath);
		$fixture->setOverrideValues($overrideValues);
		
		return $fixture->insert();
	}
	
	protected function viewExists($viewName)
	{
		$this->logger->debug("Checking for $viewName view", array('tag' => 'migration'));
		
		$select = $this->getSql()->select('pg_views')->where(array('schemaname' => 'public', 'viewname' => $viewName));
		$statement = $this->getSql()->prepareStatementForSqlObject($select);
		$results = $statement->execute();
		
		return (count($results) > 0);
	}
	
	protected function viewCreate($viewName, $as, $replace = false)
	{
		if ($this->viewExists($viewName) && !$replace) {
			throw new \Exception("View $viewName already exists");
		}
		$this->logger->info("Creating $viewName view", array('tag' => 'migration'));
			
		$this->executeQueryString(sprintf('CREATE OR REPLACE VIEW %s AS %s', $viewName, $as));
		
		return $this;
	}
	
	protected function ruleExists($ruleName)
	{
		$this->logger->debug("Checking for $ruleName rule", array('tag' => 'migration'));
		
		$select = $this->getSql()->select('pg_rules')->where(array('schemaname' => 'public', 'rulename' => $ruleName));
		$statement = $this->getSql()->prepareStatementForSqlObject($select);
		$results = $statement->execute();
		
		return (count($results) > 0);
	}
	
	protected function ruleCreate($ruleName, $on, $to, $doInstead, $replace = false)
	{
		if ($this->ruleExists($ruleName) && !$replace) {
			throw new \Exception("Rule $ruleName already exists");
		}
		if (!$this->viewExists($to) && !$this->tableExists($to)) {
			throw new \Exception("Unable to find table or view with name: " . $on);
		}
		$this->logger->info("Creating $ruleName rule", array('tag' => 'migration'));
		
		$validOns = array('INSERT', 'UPDATE', 'DELETE');
		$on = strtoupper($on);
		if (!in_array($on, $validOns)) {
			throw new \Exception('Invalid ON condition: ' . $on);
		}
		$this->executeQueryString(sprintf("CREATE OR REPLACE RULE %s AS ON %s TO %s DO INSTEAD %s", $ruleName, strtoupper($on), $to, $doInstead));
		
		return $this;
	}
	
	protected function getNamedItemsTableColumns()
	{
		return array(
			'key'			=> 'UUID PRIMARY KEY DEFAULT uuid_generate_v4()',
			'name'			=> 'VARCHAR NOT NULL',
			'created'		=> 'TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now()',
			'updated'		=> 'TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now()',
			'created_by'	=> 'UUID NOT NULL REFERENCES users(key)',
			'updated_by'	=> 'UUID NOT NULL REFERENCES users(key)',
			'deleted'		=> 'TIMESTAMP WITH TIME ZONE',
		);
	}
	
	protected function getAccountBoundNamedItemsTableColumns()
	{
		return array_merge($this->getNamedItemsTableColumns(), array(
			'account_key'	=> 'UUID NOT NULL REFERENCES accounts(key)',
		));
	}
	
}
