<?php

namespace Omelettes\Model;

use Omelettes\Uuid\V4 as Uuid;
use Zend\Db\Sql\Predicate;

class AuthUsersMapper extends QuantumMapper
{
	protected function getDefaultWhere()
	{
		$where = new Predicate\PredicateSet();
		$where->addPredicate(new Predicate\Operator('acl_role', '!=', 'system'));
	
		return $where;
	}
	
	public function signupUser(AuthUser $user, $plaintextPassword)
	{
		$config = $this->getServiceLocator()->get('config');
		$key = new Uuid();
		$salt = new Uuid();
		$data = array(
			'key'				=> $key,
			'name'				=> $user->name,
			'created_by'		=> $config['user_keys']['SYSTEM_SIGNUP'],
			'updated_by'		=> $config['user_keys']['SYSTEM_SIGNUP'],
			'full_name'			=> $user->fullName,
			'salt'				=> $salt,
			'password_hash'		=> $this->generatePasswordHash($plaintextPassword, $salt),
			'acl_role'			=> 'user',
		);
	
		$this->writeTableGateway->insert($data);
	
		// Load model with new values
		$user->exchangeArray($data);
	}
	
	protected function generatePasswordHash($plaintextPassword, $salt)
	{
		return hash('sha256', $plaintextPassword . $salt);
	}
	
	public function regeneratePasswordResetKey(AuthUser $user)
	{
		if (!$this->find($user->key)) {
			throw new \Exception('User with key ' . $user->key . ' does not exist');
		}
		
		$key = new Uuid();
		$data = array(
			'password_reset_key'		=> $key,
			'password_reset_requested'	=> 'now()',
		);
		$this->writeTableGateway->update($data, array('key' => $user->key));
		
		return (string)$key;
	}
	
	public function updatePassword(AuthUser $user, $plaintextPassword)
	{
		if (!$this->find($user->key)) {
			throw new \Exception('User with key ' . $user->key . ' does not exist');
		}
		$salt = new Uuid();
		$data = array(
			'salt'						=> $salt,
			'password_hash'				=> $this->generatePasswordHash($plaintextPassword, $salt),
			'password_reset_key'		=> null,
			'password_reset_requested'	=> null,
		);
		$this->writeTableGateway->update($data, array('key' => $user->key));
	}
	
	public function getSystemIdentity($systemIdentityKey)
	{
		// Don't use the default WHERE predicates
		$predicateSet = new Predicate\PredicateSet();
		$predicateSet->addPredicate(new Predicate\Operator('acl_role', '=', 'system'));
		$predicateSet->addPredicate(new Predicate\Operator('key', '=', $systemIdentityKey));
		
		return $this->findOneWhere($predicateSet);
	}
	
}
