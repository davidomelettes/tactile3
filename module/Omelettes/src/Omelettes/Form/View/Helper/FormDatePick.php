<?php

namespace Omelettes\Form\View\Helper;

use Omelettes\View\Helper\AbstractHelper;
use Zend\Form\ElementInterface,
	Zend\Form\View\Helper\FormInput;

class FormDatePick extends FormInput
{
	protected $validTagAttributes = array(
		'name'           => true,
		'autocomplete'   => true,
		'disabled'       => true,
		'placeholder'    => true,
		'required'       => true,
		'type'           => true,
		'value'          => true,
	);
	
	public function __invoke(ElementInterface $element)
	{
		return $this->render($element);
	}
	
	public function render(ElementInterface $element)
	{
		$attributes = $element->getAttributes();
		$attributes['type'] = 'text';
		
		$value = $element->getValue();
		$attributes['value'] = $value;
		
		return sprintf(
			'<input %s%s',
			$this->createAttributesString($attributes),
			$this->getInlineClosingBracket()
		);
	}
	
	protected function getType(ElementInterface $element)
	{
		return 'datepick';
	}
	
}
