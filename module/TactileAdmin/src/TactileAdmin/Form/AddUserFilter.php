<?php

namespace TactileAdmin\Form;

use Omelettes\Form\QuantumFilter,
	Omelettes\Model,
	Omelettes\Validator;
use Zend\Validator as ZendValidator;

class AddUserFilter extends QuantumFilter
{
	/**
	 * @var Model\AuthUsersMapper
	 */
	protected $usersMapper;
	
	public function __construct(Model\AuthUsersMapper $usersMapper)
	{
		$this->usersMapper = $usersMapper;
	}
	
	public function getInputFilter()
	{
		if (!$this->inputFilter) {
			$inputFilter = parent::getInputFilter();
			$factory = $inputFilter->getFactory();
				
			$inputFilter->add($factory->createInput(array(
				'name'			=> 'full_name',
				'required'		=> 'true',
				'filters'		=> array(
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
	
			$inputFilter->add($factory->createInput(array(
				'name'			=> 'name',
				'required'		=> 'true',
				'filters'		=> array(
					array('name' => 'StringTrim'),
				),
				'validators'	=> array(
					array(
						'name'		=> 'EmailAddress',
						'options'	=> array(
							'messages'	=> array(
								ZendValidator\EmailAddress::INVALID_FORMAT => 'Please enter a valid email address',
							),
						),
					),
					array(
						'name'		=> 'Omelettes\Validator\Model\DoesNotExist',
						'options'	=> array(
							'mapper'	=> $this->usersMapper,
							'method'	=> 'findByName',
							'messages'	=> array(
								Validator\Model\DoesNotExist::ERROR_MODEL_EXISTS => 'A user with that email address already exists',
							),
						),
					),
				),
			)));
	
			$inputFilter->add($factory->createInput(array(
				'name'			=> 'password',
				'required'		=> false,
				'filters'		=> array(
					array('name' => 'StringTrim'),
				),
				'validators'	=> array(
					array(
						'name'		=> 'StringLength',
						'options'	=> array(
							'encoding'	=> 'UTF-8',
							'min'		=> 6,
							'max'		=> 255,
						),
					),
				),
			)));
				
			$this->inputFilter = $inputFilter;
		}
	
		return $inputFilter;
	}
	
}
