<?php

namespace OmelettesAuth\Form;

use Omelettes\Form\QuantumFilter,
	Omelettes\Validator\Model\ModelExists;
use OmelettesAuth\Model\UsersMapper;
use Zend\InputFilter\InputFilter,
	Zend\Validator\EmailAddress;

class ForgotPasswordFilter extends QuantumFilter
{
	/**
	 * @var UsersMapper
	 */
	protected $usersMapper;
	
	public function __construct(UsersMapper $usersMapper)
	{
		$this->usersMapper = $usersMapper;
	}
	
	public function getInputFilter()
	{
		if (!$this->inputFilter) {
			$inputFilter = new InputFilter();
			$factory = $inputFilter->getFactory();
			
			$inputFilter->add($factory->createInput(array(
				'name'			=> 'name',
				'required'		=> 'true',
				'filters'		=> array(
					array('name' => 'StringTrim'),
				),
				'validators'	=> array(
					array(
						'name'					=> 'EmailAddress',
						'break_chain_on_failure'=> true,
						'options'				=> array(
							'messages'	=> array(
								EmailAddress::INVALID_FORMAT => 'Please enter a valid email address',
							),
						),
					),
					array(
						'name'		=> 'Omelettes\Validator\Model\ModelExists',
						'options'	=> array(
							'table'		=> 'users',
							'field'		=> 'name',
							'mapper'	=> $this->usersMapper,
							'method'	=> 'findByName',
							'messages'	=> array(
								ModelExists::ERROR_MODEL_DOES_NOT_EXIST => 'No user with that email address was found',
							),
						),
					),
				),
			)));
			
			$this->inputFilter = $inputFilter;
		}
		
		return $this->inputFilter;
	}
	
}
