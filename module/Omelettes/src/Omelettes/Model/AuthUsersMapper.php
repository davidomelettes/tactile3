<?php

namespace Omelettes\Model;

use Omelettes\Uuid\V4 as Uuid;
use Zend\Db\Sql\Predicate;

class AuthUsersMapper extends QuantaMapper
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
			'acl_role'			=> $user->aclRole,
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
	
	public function tieUserToAccount(AuthUser $user, Account $account)
	{
		$data = array(
			'account_key' => $account->key,
		);
		
		$this->writeTableGateway->update($data, array('key' => $user->key));
		$user->accountKey = $account->key;
	}
	
	public function generateRandomPassword()
	{
		if (rand(0, 1)) {
			$mods = preg_split('/\s+/', 'notso very quite really truly extra');
			$adjs = preg_split('/\s+/', 'big small quiet loud large tiny hot cold old young slow fast cute scary
				strong weak long short common rare near far odd strange wide narrow happy jolly silly witty
				tasty cool dusty fluffy warm tidy elegant plain quaint fancy');
		} else {
			$mods = preg_split('/\s+/', 'dark light pale warm faded bright');
			$adjs = preg_split('/\s+/', 'amber aquamarine auburn azure beige blue bronze brown burgundy cerulean crimson cyan
				ebony eggshell fuchsia ginger green indigo ivory lavender magenta mustard navy ochre olive
				pink puce purple red saffron silver tan teal turquoise umber vermillion violet yellow');
		}
		$nouns = preg_split('/\s+/', 'animals balls birds boats books cakes cars cats cloths clouds coats cups dogs dresses
			eggs flags fruit gloves hats inks jams kites leaves moons nets oars pegs quotes rooms salads scarves shoes stones
			twigs users vases walls yetis');
		
		shuffle($mods);
		shuffle($adjs);
		shuffle($nouns);
		$n = rand('10', '99');
		$mod = $mods[0];
		$adj = $adjs[0];
		$noun = $nouns[0];
		
		$plaintextPassword = sprintf('%s%s%s%s', $n, $mod, $adj, $noun);
		
		return $plaintextPassword;
	}
	
}
