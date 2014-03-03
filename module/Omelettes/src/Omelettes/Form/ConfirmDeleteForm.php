<?php

namespace Omelettes\Form;

class ConfirmDeleteForm extends QuantumForm
{
	public function __construct($name = 'confirm-delete')
	{
		parent::__construct($name);
		
		$this->addSubmitFieldset('Delete');
	}
	
}
