<?php

namespace OmelettesAuth\Form;

use Omelettes\Form\NamedItemFilter,
	Omelettes\Validator\Model\Exists;
use OmelettesAuth\Model\UsersMapper;
use Zend\InputFilter\InputFilter,
	Zend\Validator\EmailAddress;

class ForgotPasswordFilter extends NamedItemFilter
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
						'name'		=> 'Omelettes\Validator\Model\Exists',
						'options'	=> array(
							'table'		=> 'users',
							'field'		=> 'name',
							'mapper'	=> $this->usersMapper,
							'method'	=> 'findByName',
							'messages'	=> array(
								Exists::ERROR_MODEL_DOES_NOT_EXIST => 'No user with that email address was found',
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
