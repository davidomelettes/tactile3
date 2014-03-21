<?php

namespace Omelettes\Controller;

use OmelettesMigration;
use Omelettes\Logger;
use Zend\Console\Request as ConsoleRequest,
	Zend\Db\Adapter\Adapter as DbAdapter;

class ConsoleMigrationController extends AbstractController
{
	protected $migrationPath = 'migrations/';
	protected $migrationFilePattern = '/^(Migration(\d{3}).+)\.php$/';
	protected $migrationFiles;
	
	protected $dbName;
	
	/**
	 * @var DbAdapter
	 */
	protected $dbAdapter;
	
	/**
	 * @var Logger
	 */
	protected $logger;
	
	public function getDbAdapter()
	{
		if (!$this->dbAdapter) {
			$adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
			$this->dbAdapter = $adapter;
		}
		
		return $this->dbAdapter;
	}
	
	public function getLogger()
	{
		if (!$this->logger) {
			$logger = $this->getServiceLocator()->get('Logger');
			$this->logger = $logger;
		}
		
		return $this->logger;
	}
	
	protected function scanMigrationDirectory()
	{
		$migrationPathFiles = scandir($this->migrationPath);
		$migrationFiles = array();
		foreach ($migrationPathFiles as $file) {
			if (preg_match($this->migrationFilePattern, $file, $m)) {
				// Remove leading zeroes
				$fileSequence = preg_replace('/^0+/', '', $m[2]);
				$migrationFiles[$fileSequence] = $m[1];
			}
		}
		$this->migrationFiles = $migrationFiles;
	}

	protected function getMigrationFile($sequenceNumber)
	{
		if (!is_array($this->migrationFiles)) {
			$this->scanMigrationDirectory();
		}
		
		$sequenceNumber = preg_replace('/^0+/', '', $sequenceNumber);
		if (isset($this->migrationFiles[$sequenceNumber])) {
			return "OmelettesMigration\\".$this->migrationFiles[$sequenceNumber];
		}
		
		return false;
	}
	
	public function getMigration($sequenceNumber)
	{
		$migrationClass = $this->getMigrationFile($sequenceNumber);
		if (!$migrationClass) {
			return false;
		}
		
		$migration = new $migrationClass(
			$this->getDbAdapter(),
			$this->getLogger()
		);
		
		return $migration;
	}
	
	protected function getLastSequenceNumber()
	{
		$statement = $this->getDbAdapter()->query("SELECT max(sequence) FROM migration_history");
		$result = $statement->execute();
		$row = $result->current();
		if (!is_array($row) || !isset($row['max'])) {
			throw new \Exception('Failed to get last sequence number');
		}
		
		return (int) $row['max'];
	}
	
	protected function updateSequenceHistory($sequenceNumber, $name)
	{
		$statement = $this->getDbAdapter()->query("INSERT INTO migration_history (sequence, name) VALUES (?, ?)");
		$result = $statement->execute(array($sequenceNumber, $name));
		
		return $this;
	}
	
	protected function getConnection()
	{
		return $this->getDbAdapter()->getDriver()->getConnection();
	}
	
	protected function beginTransaction()
	{
		$this->getConnection()->beginTransaction();
	}
	
	protected function commitTransaction()
	{
		$this->getConnection()->commit();
	}
	
	protected function rollbackTransaction()
	{
		$this->getConnection()->rollback();
	}
	
	public function migrateAction()
	{
		$this->getLogger()->info('Migration Action');
		$request = $this->getRequest();
		if (!$request->getParam('commit')) {
			$this->getLogger()->info('TEST MODE: Use --commit to commit changes');
		}
		
		$this->beginTransaction();
		try {
			$this->getLogger()->debug('Current last sequence number: ' . $this->getLastSequenceNumber());
			do {
				$sequenceNumber = $this->getLastSequenceNumber()+1;
				$migration = $this->getMigration($sequenceNumber);
				if (!$migration) {
					$this->getLogger()->debug('No migration for sequence: ' . $sequenceNumber);
					break;
				}
				$this->getLogger()->debug("Running migration $sequenceNumber: " . get_class($migration));
				$migration->migrate();
				$this->updateSequenceHistory($sequenceNumber, get_class($migration));
			} while ($request->getParam('all') && $this->getMigration($this->getLastSequenceNumber()+1));
			$this->getLogger()->debug('No further migrations to execute');
		} catch (\Exception $e) {
			$this->rollbackTransaction();
			$this->getLogger()->warn("Exception occurred during migration $sequenceNumber: " . $e->getMessage());
			throw $e;
		}
		if ($request->getParam('commit')) {
			$this->commitTransaction();
		}
		
		$this->logger->info('Action complete');
	}
	
}
