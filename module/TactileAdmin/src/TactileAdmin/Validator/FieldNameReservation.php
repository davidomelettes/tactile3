<?php

namespace TactileAdmin\Validator;

use Zend\Validator\AbstractValidator;

class FieldNameReservation extends AbstractValidator
{
	const RESERVED_WORD = 'reservedWord';
	
	protected $reservedWords = array(
		'key',
		'name',
		'created',
		'updated',
		'created_by',
		'updated_by',
		'deleted',
		'account_key',
		'resource_name',
		'current_version_key',
		'xml_specification',
	);
	
	protected $messageTemplates = array(
		self::RESERVED_WORD => "The key '%value%' is a reserved word",
	);
	
	public function isValid($value)
	{
		$this->setValue($value);
		
		if (in_array($value, $this->reservedWords)) {
			$this->error(self::RESERVED_WORD);
			return false;
		}
		
		return true;
	}
	
}
