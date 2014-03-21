<?php

namespace Omelettes\Validator\Model;

use Zend\Validator\Exception;

/**
 * Tests for the existance of a model by key; model must already exist
 *
 * @author dave
 */
class Exists extends AbstractModel
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
		
		$result = call_user_func_array(array($this->mapper, $this->mapperMethod), array($value, $this->field, $this->exclude));
		if ($result === false || count($result) < 1) {
			$valid = false;
			$this->error(self::ERROR_MODEL_DOES_NOT_EXIST);
		}
		
		return $valid;
	}
	
}
