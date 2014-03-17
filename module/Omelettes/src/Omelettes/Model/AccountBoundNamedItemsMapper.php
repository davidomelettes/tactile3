<?php

namespace Omelettes\Model;

use Zend\Db\Sql\Predicate;

class AccountBoundNamedItemsMapper extends NamedItemsMapper
{
	protected $accountKey;
	
	public function getAccountKey()
	{
		if (!$this->accountKey) {
			$auth = $this->getServiceLocator()->get('AuthService');
			if (!$auth->hasIdentity()) {
				throw new \Exception('Tried to load account-bound objects without an identity');
			}
			$accountKey = $auth->getIdentity()->accountKey;
			if (empty($accountKey)) {
				throw new \Exception('Identity has no account key');
			}
			$this->accountKey = $accountKey;
		}
		
		return $this->accountKey;
	}
	
	protected function getDefaultWhere()
	{
		$where = new Predicate\PredicateSet();
		$where->addPredicate(new Predicate\IsNull('deleted'));
		$where->addPredicate(new Predicate\Operator('account_key', '=', $this->getAccountKey()));
	
		return $where;
	}
	
	public function saveNamedItem(AccountBoundNamedItemModel $model)
	{
		if ($this->isReadOnly()) {
			throw new \Exception(get_class($this) . ' is read-only');
		}
		
		$key = $model->key;
		$data = $this->prepareSaveData($model);
		if ($key) {
			// Updating
			$this->writeTableGateway->update($data, array('key' => $key, 'account_key' => $this->getAccountKey()));
			$data['key'] = $key;
		} else {
			// Creating
			$data['account_key'] = $this->getAccountKey();
			$this->writeTableGateway->insert($data);
		}
		
		// Rehydrate
		$model->exchangeArray($data);
	}
	
}
