<?php

namespace Omelettes\Model;

use Omelettes\Db\Sql\Predicate as OmelettesPredicate,
	Omelettes\Paginator\Adapter\DbTableGateway as DbTableGatewayAdapter,
	Omelettes\Paginator\Paginator,
	Omelettes\Uuid\V4 as Uuid,
	Omelettes\Validator\Uuid\V4 as UuidValidator;
use Zend\Db\ResultSet\ResultSet,
	Zend\Db\Sql,
	Zend\Validator\StringLength;

abstract class NamedItemsMapper extends AbstractMapper
{
	/**
	 * @var Paginator
	 */
	protected $paginator;
	
	/**
	 * @return Sql\Predicate\PredicateSet
	 */
	protected function getDefaultWhere()
	{
		$where = new Sql\Predicate\PredicateSet();
		$where->addPredicate(new Sql\Predicate\IsNull('deleted'));
		
		return $where;
	}
	
	/**
	 * @return string
	 */
	protected function getDefaultOrder()
	{
		return 'name';
	}
	
	/**
	 * Returns a single result row object, or false if none found
	 *
	 * @param string $id
	 * @return NamedItemModel|boolean
	 */
	public function find($key)
	{
		$validator = new UuidValidator();
		if (!$validator->isValid($key)) {
			return false;
		}
		
		$where = $this->getWhere();
		$where->andPredicate(new Sql\Predicate\Operator('key', '=', $key));
		
		return $this->findOneWhere($where);
	}
	
	/**
	 * @param Sql\Predicate\PredicateSet $where
	 * @param string $order
	 * @return Paginator
	 */
	protected function getPaginator($where, $order = null)
	{
		if (!$this->paginator) {
			if ($where instanceof Sql\Predicate\PredicateSet && count($where) < 1) {
				$where = null;
			}
			$paginationAdapter = new DbTableGatewayAdapter(
				$this->readTableGateway,
				$where,
				$order
			);
			$paginator = new Paginator($paginationAdapter);
			$this->paginator = $paginator;
		}
		
		return $this->paginator;
	}
	
	/**
	 * @param boolean $paginated
	 * @return ResultSet|Paginator
	 */
	public function fetchAll($paginated = false)
	{
		return $this->fetchAllWhere($this->getWhere(), $paginated);
	}
	
	/**
	 * @param Sql\Predicate\PredicateSet $where
	 * @param boolean $paginated
	 * @return ResultSet|Paginator
	 */
	protected function fetchAllWhere(Sql\Predicate\PredicateSet $where, $paginated = false)
	{
		if ($paginated) {
			return $this->getPaginator($where, $this->getOrder());
		}
		
		return $this->select($this->generateSqlSelect($where, $this->getOrder()));
	}
	
	/**
	 * Used for model-existence validation
	 * 
	 * @param mixed $value
	 * @param string $field
	 * @param array $exclude
	 * @return Ambigous <boolean, \Omelettes\Model\ArrayObject, multitype:, ArrayObject, NULL, \ArrayObject, unknown>
	 */
	public function existanceValidationSearch($value, $field = 'key', $exclude = array())
	{
		$where = $this->getWhere();
		$where->addPredicate(new Sql\Predicate\Operator($field, '=', $value));
		if (isset($exclude['field']) && isset($exclude['value'])) {
			$where->addPredicate(new Sql\Predicate\Operator($exclude['field'], '!=', $exclude['value']));
		}
		
		return $this->findOneWhere($where);
	}
	
	/**
	 * @param string $term
	 * @param boolean $paginated
	 * @return ResultSet|Paginator
	 */
	public function fetchAllWhereNameLike($term, $paginated = false)
	{
		$where = $this->getWhere();
		if (!is_null($term) && '' !== $term) {
			$where->addPredicate(new OmelettesPredicate\Ilike('name', $term.'%'));
		}
		
		return $this->fetchAllWhere($where, $paginated);
	}
	
	public function findByName($name)
	{
		$validator = new StringLength(array('min' => 1, 'encoding' => 'UTF-8'));
		if (!$validator->isValid($name)) {
			return false;
		}
		
		$where = $this->getWhere();
		$where->addPredicate(new Sql\Predicate\Operator('name', '=', $name));
		
		return $this->findOneWhere($where);
	}
	
	public function findBySlug($slug)
	{
		$where = $this->getWhere();
		$where->addPredicate(new Sql\Predicate\Operator('slug', '=', $slug));
	
		return $this->findOneWhere($where);
	}
	
	protected function prepareSaveData(NamedItemModel $model)
	{
		$auth = $this->getServiceLocator()->get('AuthService');
		if (!$auth->hasIdentity()) {
			throw new \Exception('Missing auth identity');
		}
		
		$key = $model->key;
		$data = array(
			'name'				=> $model->name,
			'updated_by'		=> $auth->getIdentity()->key,
			'updated'			=> new Sql\Expression('now()'),
		);
		if (!$key) {
			// Creating
			$key = new Uuid();
			$data = array_merge($data, array(
				'key'			=> (string)$key,
				'created_by'	=> $auth->getIdentity()->key,
			));
		}
		
		return $data;
	}
	
	public function saveModel(NamedItemModel $model)
	{
		if ($this->isReadOnly()) {
			throw new \Exception(get_class($this) . ' is read-only');
		}
		
		$key = $model->key;
		$data = $this->prepareSaveData($model);
		if ($key) {
			// Updating
			$this->writeTableGateway->update($data, array('key' => $key));
			$data['key'] = $key;
		} else {
			// Creating
			$this->writeTableGateway->insert($data);
		}
		
		// Rehydrate
		$model->exchangeArray($data);
	}
	
	public function createModel(NamedItemModel $model)
	{
		return $this->saveModel($model);
	}
	
	public function updateModel(NamedItemModel $model)
	{
		return $this->saveModel($model);
	}
	
	public function deleteModel(NamedItemModel $model)
	{
		if ($this->isReadOnly()) {
			throw new \Exception(get_class($this) . ' is read-only');
		}
		
		$data = array(
			'deleted' => new Sql\Expression('now()'),
		);
		$this->writeTableGateway->update($data, array('key'=> $model->key));
	}
	
	public function processModels(array $keys, $action, $data = array())
	{
		if ($this->isReadOnly()) {
			throw new \Exception(get_class($this) . ' is read-only');
		}
		
		$successCount = 0;
		
		if (empty($keys)) {
			return $successCount;
		}
		switch ($action) {
			case 'delete':
				foreach ($keys as $key) {
					if (FALSE !== ($model = $this->find($key))) {
						$this->deleteNamedItem($model);
						$successCount++;
					}
				}
				break;
			default:
				throw new Exception\UnknownProcessActionException('Unknown action: ' . $action);
		}
		
		return $successCount;
	}
	
}
