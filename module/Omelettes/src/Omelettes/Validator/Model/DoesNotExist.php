<?php

namespace Omelettes\Validator\Model;

use Zend\Validator\Exception,
	Zend\Db\ResultSet\AbstractResultSet;

/**
 * Tests for the existance of a model by key; model must not already exist
 * 
 * @author dave
 */
class DoesNotExist extends AbstractModel
{
	/**
	 * Error constants
	 */
	const ERROR_MODEL_EXISTS = 'modelExists';
	
	/**
	 * @var array Message templates
	 */
	protected $messageTemplates = array(
		self::ERROR_MODEL_EXISTS			=> 'A matching record was found',
	);
	
	public function isValid($value)
	{
		if (!$this->mapper) {
			throw new Exception\RuntimeException('No mapper present');
		}
		
		$this->setValue($value);
		$valid = true;
	
		$result = call_user_func_array(array($this->mapper, $this->mapperMethod), array($value, $this->field, $this->exclude));
		if ($result instanceof AbstractResultSet && count($result) > 0) {
			// Multiple rows returned
			$valid = false;
			$this->error(self::ERROR_MODEL_EXISTS);
		} elseif (false !== $result) {
			// Single row returned
			$valid = false;
			$this->error(self::ERROR_MODEL_EXISTS);
		}
	
		return $valid;
	}
	
}
