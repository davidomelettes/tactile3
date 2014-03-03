<?php

namespace Omelettes\View\Helper;

use Omelettes\Validator\Uuid\V4 as UuidValidator;
use Zend\View\Helper\AbstractHelper;

class PrettyUuid extends AbstractHelper
{
	public function __invoke($uuid)
	{
		$validator = new UuidValidator();
		if (!$validator->isValid($uuid)) {
			return self::EMPTY_TEXT;
		}
		
		return preg_replace('/-/', '', strtoupper($uuid));
	}
	
}
