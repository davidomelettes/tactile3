<?php

namespace Omelettes\Model;

use Omelettes\Uuid\V4 as Uuid;
use Zend\Db\Sql\Predicate,
	Zend\Validator\StringLength;

class AccountsMapper extends NamedItemsMapper
{
	protected function getDefaultWhere()
	{
		$where = new Predicate\PredicateSet();
		$where->addPredicate(new Predicate\Expression('deleted', '=', 'false'));
	
		return $where;
	}
	
	protected function prepareSaveData(Account $model)
	{
		$data = parent::prepareSaveData($model);
		$data = array_merge($data, array(
			'plan_key'	=> $model->planKey ? $model->planKey : null,
		));
	
		return $data;
	}
	
	public function createAccount(Account $account, AuthUser $user)
	{
		$key = new Uuid();
		$data = array(
			'key'				=> $key,
			'name'				=> $account->name,
			'created_by'		=> $user->key,
			'updated_by'		=> $user->key,
			'plan_key'			=> $account->planKey,
		);
		
		$this->writeTableGateway->insert($data);
		
		// Load model with new values
		$account->exchangeArray($data);
	}
	
}
