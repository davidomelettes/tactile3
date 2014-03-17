<?php

namespace Omelettes\Model;

use Omelettes\Uuid\V4 as Uuid;
use Zend\Db\Sql\Predicate,
	Zend\Validator\StringLength;

class AccountPlansMapper extends AbstractMapper
{
	/**
	 * @return Predicate\PredicateSet
	 */
	protected function getDefaultWhere()
	{
		$where = new Predicate\PredicateSet();
		
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
		$where->andPredicate(new Predicate\Operator('key', '=', $key));
	
		return $this->findOneWhere($where);
	}
	
	/**
	 * @param boolean $paginated
	 * @return ResultSet
	 */
	public function fetchAll()
	{
		return $this->select($this->generateSqlSelect($this->getWhere(), $this->getOrder()));
	}
	
	/**
	 * @return AccountPlan
	 */
	public function getFreeAccountPlan()
	{
		$where = $this->getWhere();
		$where->andPredicate(new Predicate\Operator('name', '=', 'Free'));
		
		return $this->findOneWhere($where);
	}
	
}
