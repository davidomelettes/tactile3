<?php

namespace Omelettes\Validator\Model;

use Zend\Validator\Exception;

/**
 * Tests for the existance of a model by key; model must already exist
 *
 * @author dave
 */
class ModelExists extends AbstractModel
{
	/**
	 * Error constants
	 */
	const ERROR_MODEL_DOES_NOT_EXIST = 'modelDoesNotExist';
	
	/**
	 * @var array Message templates
	 */
	protected $messageTemplates = array(
		self::ERROR_MODEL_DOES_NOT_EXIST	=> 'No matching record was found',
	);
	
	public function isValid($value)
	{
		if (!$this->mapper) {
			throw new Exception\RuntimeException('No mapper present');
		}
		
		$this->setValue($value);
		$valid = true;
		
		$result = $this->mapper->{$this->mapperMethod}($value);
		if ($result === false || count($result) < 1) {
			$valid = false;
			$this->error(self::ERROR_MODEL_DOES_NOT_EXIST);
		}
		
		return $valid;
	}
	
}
