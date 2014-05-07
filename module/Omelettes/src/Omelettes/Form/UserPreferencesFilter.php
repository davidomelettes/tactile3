<?php

namespace Omelettes\Form;

class UserPreferencesFilter extends AbstractFilter
{
	public function getInputFilter()
	{
		if (!$this->inputFilter) {
			$inputFilter = $this->getDefaultInputFilter();
			$factory = $inputFilter->getFactory();
	
			$this->inputFilter = $inputFilter;
		}
	
		return $this->inputFilter;
	}
	
}
