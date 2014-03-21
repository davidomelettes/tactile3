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
		$where = parent::getDefaultWhere();
		$where->addPredicate(new Predicate\Operator('account_key', '=', $this->getAccountKey()));
	
		return $where;
	}
	
	protected function prepareSaveData(AccountBoundNamedItemModel $model)
	{
		$data = parent::prepareSaveData($model);
		$data['account_key'] = $this->getAccountKey();
		
		return $data;
	}
	
	public function saveModel(AccountBoundNamedItemModel $model)
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
			$this->writeTableGateway->insert($data);
		}
		
		// Rehydrate
		$model->exchangeArray($data);
	}
	
}
