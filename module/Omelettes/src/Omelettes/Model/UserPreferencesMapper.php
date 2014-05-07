<?php

namespace Omelettes\Model;

use Omelettes\Uuid\V4 as Uuid;
use Zend\Db\Sql;

class UserPreferencesMapper extends AbstractMapper
{
	protected function getDefaultWhere()
	{
		$where = new Sql\Predicate\PredicateSet();
		$auth = $this->getServiceLocator()->get('AuthService');
		if ($auth->hasIdentity()) {
			$where->addPredicate(new Sql\Predicate\Operator('user_key', '=', $auth->getIdentity()->key));
			$where->addPredicate(new Sql\Predicate\IsNull('user_key'), Sql\Predicate\Predicate::OP_OR);
		} else {
			throw new \Exception('Tried to load user preferences without an identity');
		}
		
		return $where;
	}
	
	protected function getDefaultOrder()
	{
		return 'name';
	}
	
	public function fetchAll()
	{
		return $this->fetchAllWhere($this->getWhere());
	}
	
	public function find($name)
	{
		$where = $this->getWhere();
		$where->andPredicate(new Sql\Predicate\Operator('code', '=', $code));
	
		return $this->findOneWhere($where);
	}
	
	protected function prepareSaveData(UserPreference $pref)
	{
		$auth = $this->getServiceLocator()->get('AuthService');
		if (!$auth->hasIdentity()) {
			throw new \Exception('Missing auth identity');
		}
		
		$key = $pref->key;
		$data = array(
			'user_key'	=> $auth->getIdentity()->key,
			'name'		=> $pref->name,
			'type'		=> $pref->type,
		);
		switch ($pref->type) {
			case 'varchar':
			case 'integer':
			case 'numeric':
			case 'datetime':
			case 'uuid':
			case 'boolean':
				$data[$pref->type.'_value'] = $pref->value;
				break;
			default:
				throw new \Exception('Unrecognised preference type: ' . $pref->type);
		}
		if (!$key) {
			// Creating
			$key = new Uuid();
			$data['key'] = (string)$key;
		}
	
		return $data;
	}
	
	public function savePreference(UserPreference $pref)
	{
		$key = $pref->key;
		$data = $this->prepareSaveData($pref);
		if ($key) {
			// Updating
			$this->writeTableGateway->update($data, array('key' => $key));
			$data['key'] = $key;
		} else {
			// Creating
			$this->writeTableGateway->insert($data);
		}
		
		// Rehydrate
		$pref->exchangeArray($data);
	}
	
}
