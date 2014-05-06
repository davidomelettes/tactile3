<?php

namespace Omelettes\Form;

class NamedItemFilter extends AbstractFilter
{
	public function getInputFilter()
	{
		if (!$this->inputFilter) {
			$inputFilter = $this->getDefaultInputFilter();
			$factory = $inputFilter->getFactory();
				
			$inputFilter->add($factory->createInput(array(
				'name'			=> 'name',
				'required'		=> true,
				'filters'		=> array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators'	=> array(
					array(
						'name'		=> 'StringLength',
						'options'	=> array(
							'encoding'	=> 'UTF-8',
							'min'		=> 1,
							'max'		=> 255,
						),
					),
				),
			)));
				
			$this->inputFilter = $inputFilter;
		}
	
		return $this->inputFilter;
	}
	
}
