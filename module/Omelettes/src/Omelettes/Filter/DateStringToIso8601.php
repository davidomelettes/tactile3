<?php

namespace Omelettes\Filter;

use Zend\Filter\AbstractFilter;

class DateStringToIso8601 extends AbstractFilter
{
	protected $options = array(
		'format'	=> 'Y-m-d',
	);
	
	public function filter($value)
	{
		// Does value match expected format?
		$date = \DateTime::createFromFormat($this->options['format'], $value);
		if (false === $date || \DateTime::getLastErrors()['warning_count'] > 0) {
			// Does not match expected format
			return '';
		}
		
		// Is a match; return ISO8601 formatted date
		return $date->format('Y-m-d');
	}

}
