<?php

namespace Omelettes\Model;

use Zend\Db\Sql\Predicate;

class AccountBoundQuantumMapper extends QuantumMapper
{
	protected function getDefaultWhere()
	{
		$auth = $this->getServiceLocator()->get('AuthService');
		if (!$auth->hasIdentity()) {
			throw new \Exception('Tried to load account-bound objects without an identity');
		}
		$accountKey = $auth->getIdentity()->account;
		
		$where = new Predicate\PredicateSet();
		$where->addPredicate(new Predicate\IsNull('deleted'));
		$where->addPredicate(new Predicate\Operator('account_key', '=', $accountKey));
	
		return $where;
	}
	
}
