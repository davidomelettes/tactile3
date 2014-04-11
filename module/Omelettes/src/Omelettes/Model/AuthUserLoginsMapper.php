<?php

namespace Omelettes\Model;

use Omelettes\Uuid\V4 as Uuid;
use Omelettes\Exception\UserLoginTheftException;
use Zend\Db\Sql\Predicate,
	Zend\Validator\StringLength;

class AuthUserLoginsMapper extends AbstractMapper
{
	const EXPIRY_STRING = '+2 weeks';
	
	protected function getDefaultWhere()
	{
		$where = new Predicate\PredicateSet();
		
		return $where;
	}
	
	protected function getDefaultOrder()
	{
		return 'created';
	}
	
	public function find($id)
	{
		throw new \Exception('Method not used');
	}
	
	public function fetchAll()
	{
		throw new \Exception('Method not used');
	}
	
	public function splitCookieData($string)
	{
		$data = explode(',', $string);
		if (4 !== count($data)) {
			return false;
		}
		return array(
			'name'		=> $data[0],
			'series'	=> $data[1],
			'token'		=> $data[2],
			'expiry'	=> $data[3],
		);
	}
	
	/**
	 * @param string $name
	 * @param string $series
	 * @return string
	 */
	public function saveLogin($name, $series = null, $expiry = null)
	{
		$token = new Uuid();
		if (null === $series) {
			$series = new Uuid();
		}
		if (null === $expiry) {
			$expiry = date('U', strtotime(self::EXPIRY_STRING));
		}
		$data = array(
			'name'		=> $name,
			'series'	=> (string)$series,
			'token'		=> (string)$token,
			'expiry'	=> (int)$expiry,
		);
		$this->writeTableGateway->insert($data);
		
		return implode(',', array($data['name'], $data['series'], $data['token'], $data['expiry']));
	}
	
	/**
	 * Removes all login tokens for a given name and series
	 * Used when logging out, or when a series has been suspected of theft
	 * 
	 * @param string $name
	 * @throws UserLoginTheftException
	 */
	public function deleteForNameWithSeries($name, $series)
	{
		$this->writeTableGateway->delete(array('name' => $name, 'series' => $series));
	}
	
	/**
	 * @param string $cookieData
	 * @throws UserLoginTheftException
	 * @return boolean|string
	 */
	public function verifyCookie($cookieData)
	{
		$data = explode(',', $cookieData);
		if (4 !== count($data)) {
			return false;
		}
		$name = $data[0];
		$series = $data[1];
		$token = $data[2];
		$expiry = $data[3];
		
		$where = $this->getWhere();
		$where->andPredicate(new Predicate\Operator('name', '=', $name));
		$where->andPredicate(new Predicate\Operator('series', '=', $series));
		$where->andPredicate(new Predicate\Operator('token', '=', $token));
		
		$result = $this->findOneWhere($where);
		if ($result) {
			// Delete the triplet
			$this->writeTableGateway->delete(array('name' => $name, 'series' => $series, 'token' => $token));
			
			// Issue a new token in this series
			return $this->saveLogin($name, $series, $expiry);
			
		} else {
			// Check for series theft
			// If two parties attempt to use the same series, theft has occurred
			$where = $this->getWhere();
			$where->andPredicate(new Predicate\Operator('name', '=', $name));
			$where->andPredicate(new Predicate\Operator('series', '=', $series));
			
			$result = $this->findOneWhere($where);
			if ($result) {
				// Panic! This series is expecting a different token!
				// Delete all login tokens for this series
				$this->getServiceLocator()->get('Logger')->warn("UserLoginTheftException; $name; series $series; token was $token not {$result['token']}", array('tag'=>'Auth'));
				$this->deleteForNameWithSeries($name, $series);
				throw new UserLoginTheftException();
			}
			
			return false;
		}
	}
	
}
