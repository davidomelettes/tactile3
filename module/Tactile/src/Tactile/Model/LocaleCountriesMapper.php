<?php

namespace Tactile\Model;

use Omelettes\Model;
use Zend\Db\Sql;

class LocaleCountriesMapper extends Model\AbstractMapper
{
	protected function getDefaultWhere()
	{
		$where = new Sql\Predicate\PredicateSet();

		return $where;
	}

	protected function getDefaultOrder()
	{
		return 'name desc';
	}

	public function fetchAll()
	{
		return $this->fetchAllWhere($this->getWhere());
	}

	public function find($code)
	{
		$where = $this->getWhere();
		$where->andPredicate(new Sql\Predicate\Operator('code', '=', $code));

		return $this->findOneWhere($where);
	}

}
