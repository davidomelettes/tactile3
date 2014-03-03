<?php

namespace Omelettes\Model;

use Zend\Db\Sql\Predicate,
	Zend\Db\Sql\Select,
	Zend\Db\TableGateway\TableGateway,
	Zend\ServiceManager\ServiceLocatorAwareInterface,
	Zend\ServiceManager\ServiceLocatorAwareTrait;

abstract class AbstractMapper implements ServiceLocatorAwareInterface
{
	use ServiceLocatorAwareTrait;
	
	/**
	 * @var TableGateway
	 */
	protected $readTableGateway;

	/**
	 * @var TableGateway
	 */
	protected $writeTableGateway;
	
	/**
	 * @var Predicate\PredicateSet
	 */
	protected $defaultPredicateSet;
	
	/**
	 * @var \Closure
	 */
	protected $defaultOrder;
	
	/**
	 * Array of table_names => gateways
	 * 
	 * @var array
	 */
	protected $dependantTables = array();
	
	public function __construct(TableGateway $readTableGateway, TableGateway $writeTableGateway = null, array $dependantTables = array())
	{
		$this->readTableGateway = $readTableGateway;
		if ($writeTableGateway) {
			$this->writeTableGateway = $writeTableGateway;
		}
		$this->dependantTables = $dependantTables;
	}
	
	/**
	 * Returns whether or not this mapper may be used to make changes to the database
	 * 
	 * return boolean
	 */
	public function isReadOnly()
	{
		return (!$this->writeTableGateway instanceof TableGateway);
	}
	
	/**
	 * Returns the default clauses against which all queries must be run
	 *
	 * @return Predicate\PredicateSet
	 */
	abstract protected function getDefaultWhere();
	
	/**
	 * Returns the default sort order for all queries executed by this mapper
	 * 
	 * @return string|array
	 */
	abstract protected function getDefaultOrder();
	
	/**
	 * Returns a single result row object, or false if none found
	 *
	 * @param string $id
	 * @return ArrayObject|boolean
	 */
	abstract public function find($id);
	
	/**
	 * Returns all results
	 *
	 * @return ResultSet
	*/
	abstract public function fetchAll();
	
	protected function getConnection()
	{
		return $this->writeTableGateway->getAdapter()->getDriver()->getConnection();
	}
	
	/**
	 * Begin a database transaction
	 */
	public function beginTransaction()
	{
		$this->getConnection()->beginTransaction();
	}
	
	/**
	 * Roll back a database transaction
	 */
	public function rollbackTransaction()
	{
		$this->getConnection()->rollback();
	}
	
	/**
	 * Commit a database transaction
	 */
	public function commitTransaction()
	{
		$this->getConnection()->commit();
	}
	
	/**
	 * Returns a PredicateSet for use in Zend\Db\Sql selects
	 * 
	 * @return Predicate\PredicateSet
	 */
	final public function getWhere()
	{
		if (!$this->defaultPredicateSet) {
			$defaultWhere = $this->getDefaultWhere();
			if (!$defaultWhere instanceof Predicate\PredicateSet) {
				throw new \Exception('Expected a PredicateSet');
			}
			$this->defaultPredicateSet = $defaultWhere;
		}
		
		return clone $this->defaultPredicateSet;
	}
	
	/**
	 * @return string|array
	 */
	final public function getOrder()
	{
		return $this->getDefaultOrder();
	}
	
	/**
	 * Returns a single row object, or false if none found
	 * 
	 * @param Predicate\PredicateSet $where
	 * @return boolean|ArrayObject
	 */
	protected function findOneWhere(Predicate\PredicateSet $where)
	{
		$resultSet = $this->select($this->generateSqlSelect($where));
		$result = $resultSet->current();
		if (!$result) {
			return false;
		}
		
		return $result;
	}
	
	/**
	 * Returns all results matching specified predicates
	 * 
	 * @param PredicateInterface $where
	 * @return ResultSet
	 */
	protected function fetchAllWhere(Predicate\PredicateInterface $where)
	{
		return $this->select($this->generateSqlSelect($where));
	}
	
	/**
	 * Generates a Select instance
	 * 
	 * @param Predicate\PredicateSet|\Closure $where
	 * @param string|array $order
	 * @return \Zend\Db\Sql\Select
	 */
	protected function generateSqlSelect($where, $order = null)
	{
		$select = $this->readTableGateway->getSql()->select();
		if ($where instanceof Predicate\PredicateSet) {
			if (count($where) < 1) {
				// Prevent empty PredicateSets from generating bad SQL
				$where = null;
			}
			$select->where($where);
		}
		if ($where instanceof \Clousure) {
			$where($select);
		}
		if (!is_null($order)) {
			$select->order($order);
		}
		
		return $select;
	}
	
	/**
	 * Performs a select on the tableGateway
	 * 
	 * @param Select $select
	 * @return ResultSet
	 */
	protected function select(Select $select)
	{
		return $this->readTableGateway->selectWith($select);
	}
	
	/**
	 * Allows access to a dependent table gatweway
	 * 
	 * @param string $name
	 * @throws \Exception
	 * @return TableGateway
	 */
	protected function getDependentTable($name)
	{
		if (!isset($this->dependantTables[$name])) {
			throw new \Exception($name . ' is not a dependent table');
		}
		$gateway = $this->getServiceLocator()->get($this->dependantTables[$name]);
		if (!$gateway instanceof TableGateway) {
			throw new \Exception('Expected a TableGateway');
		}
		return $gateway;
	}
	
}