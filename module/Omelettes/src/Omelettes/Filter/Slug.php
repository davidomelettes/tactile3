<?php

namespace Omelettes\Filter;

use Zend\Filter\AbstractFilter;

class Slug extends AbstractFilter
{
	public function filter($value)
	{
		if (null === $value) {
			return $value;
		}
		
		$value = (string)$value;
		$value = strtolower($value);
		$replacements = array(
			'/[\s-_]+/'		=> '-',
			'/[^a-z0-9-]/'	=> '',
		);
		$slug = preg_replace(array_keys($replacements), array_values($replacements), $name);
		
		return $value;
	}
	
}
