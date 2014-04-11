<?php

namespace Omelettes\Form\Fieldset;

use Zend\Form\Fieldset;

class SubmitFieldset extends Fieldset
{
	public function __construct($name = 'submit', $options = array())
	{
		parent::__construct($name, $options);
	}
	
	public function addSubmitElement($buttonText = 'Save', $buttonClass = 'btn btn-primary', $loadingText = null)
	{
		$this->add(array(
			'name'		=> 'submit',
			'type'		=> 'Submit',
			'attributes'=> array(
				'value'					=> $buttonText,
				'class'					=> $buttonClass,
				'data-loading-text'		=> $loadingText,
			),
		));
	
		return $this;
	}
	
}
