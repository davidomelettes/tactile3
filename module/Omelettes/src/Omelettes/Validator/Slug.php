<?php

namespace Omelettes\Validator;

use Zend\Validator\Regex;

class Slug extends Regex
{
	const SLUG_REGEX_PATTERN = '[a-z0-9-]+';
	
	public function __construct($options = array())
	{
		if (is_array($options)) {
			$options['pattern'] = '/^'.self::SLUG_REGEX_PATTERN.'$/';
			parent::__construct($options);
		} else {
			parent::__construct('/^'.self::SLUG_REGEX_PATTERN.'$/');
		}
	}
	
}
