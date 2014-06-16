<?php

namespace Tactile\Model;

use Tactile\Form;
use Omelettes\Model\AccountBoundNamedItemModel,
	Omelettes\Validator;
use Zend\ServiceManager\ServiceLocatorAwareInterface,
	Zend\ServiceManager\ServiceLocatorAwareTrait;

class ResourceField extends AccountBoundNamedItemModel implements ServiceLocatorAwareInterface
{
	use ServiceLocatorAwareTrait;
	
	protected $type;
	protected $label;
	protected $protected;
	protected $required;
	protected $defaultValue;
	protected $searchable;
	protected $priority;
	
	protected $propertyMap = array(
		'type'				=> 'type',
		'label'				=> 'label',
		'protected'			=> 'protected',
		'required'			=> 'required',
		'defaultValue'		=> 'default_value',
		'searchable'		=> 'searchable',
	);
	
	public function getInputElementSpecification()
	{
		$spec = array();
		
		switch ($this->type) {
			case 'text':
				$default = $this->defaultValue;
				$spec = array(
					'name'		=> $this->name,
					'priority'	=> $this->priority,
					'type'		=> 'Textarea',
					'options'	=> array(
						'label'		=> $this->label,
					),
					'attributes'=> array(
						'id'		=> $this->name,
						'value'		=> $default,
						'required'		=> $this->required,
					),
				);
				break;
			case 'user':
				$fieldOptions = array('' => '-- Select User --');
				foreach ($this->getServiceLocator()->get('UsersService')->getUsers() as $user) {
					$fieldOptions[$user->key] = $user->fullName;
				}
				$default = $this->defaultValue;
				if ($default === 'me') {
					$default = $this->getServiceLocator()->get('AuthService')->getIdentity()->key;
				}
				$spec = array(
					'name'		=> $this->name,
					'priority'	=> $this->priority,
					'type'		=> 'Select',
					'options'	=> array(
						'label'		=> $this->label,
						'options'	=> $fieldOptions,
					),
					'attributes'=> array(
						'id'		=> $this->name,
						'value'		=> $default,
					),
				);
				break;
			case 'datetime':
				$spec = new Form\Fieldset\DateTimeFieldset($this);
				break;
			default:
				throw new \Exception('Unexpected field type: ' . $this->type);
		}
		
		return $spec;
	}
	
	public function getInputFilterSpecification()
	{
		$spec = array();
		
		$prefsService = $this->getServiceLocator()->get('UserPreferencesService');
		
		switch ($this->type) {
			case 'varchar':
				$spec = array(
					'name'			=> $this->name,
					'required'		=> $this->required,
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
				);
				break;
			case 'text':
				$spec = array(
					'name'			=> $this->name,
					'required'		=> $this->required,
					'filters'		=> array(
						array('name' => 'StripTags'),
						array('name' => 'StringTrim'),
					),
					'validators'	=> array(
					),
				);
				break;
			case 'datetime':
				$spec = array(
					'date' => array(
						'name'		=> 'date',
						'required'	=> $this->required,
						'filters'		=> array(
							array('name' => 'StripTags'),
							array('name' => 'StringTrim'),
							array(
								'name'		=> 'Omelettes\Filter\DateStringToIso8601',
								'options'	=> array(
									'format'	=> $prefsService->get('date_format'),
								),
							),
						),
						'validators'	=> array(
							array(
								'name'		=> 'StringLength',
								'options'	=> array(
									'encoding'	=> 'UTF-8',
									'max'		=> 255,
								),
							),
							array(
								'name'		=> 'Date',
								'options'	=> array(
									'format'		=> 'Y-m-d',
								),
							),
						),
					),
					'time' => array(
						'name'		=> 'time',
						'required'	=> false,
						'filters'		=> array(
							array('name' => 'StripTags'),
							array('name' => 'StringTrim'),
						),
						'validators'	=> array(
							array(
								'name'		=> 'StringLength',
								'options'	=> array(
									'encoding'	=> 'UTF-8',
									'max'		=> 255,
								),
							),
							array(
								'name'		=> 'Date',
								'options'	=> array(
									'format'		=> 'H:i',
									'messages'	=> array(
										\Zend\Validator\Date::INVALID_DATE => "The input does not appear to be a valid time",
									),
								),
							),
						),
					),
				);
				break;
			case 'user':
				$spec = array(
					'name'			=> $this->name,
					'required'		=> $this->required,
					'filters'		=> array(
						array('name' => 'StripTags'),
						array('name' => 'StringTrim'),
					),
					'validators'	=> array(
						array(
							'name'		=> 'Omelettes\Validator\Uuid\V4',
							'break_chain_on_failure'=> true,
							'options'	=> array(
								'messages'	=> array(
									\Omelettes\Validator\Uuid\V4::NOT_MATCH => "Not a valid key value",
								),
							),
						),
						array(
							'name'		=> 'Omelettes\Validator\Model\Exists',
							'options'	=> array(
								'table'		=> 'users',
								'field'		=> 'key',
								'mapper'	=> $this->getServiceLocator()->get('Omelettes\Model\AuthUsersMapper'),
								'messages'	=> array(
									Validator\Model\Exists::ERROR_MODEL_DOES_NOT_EXIST => 'User not found',
								),
							),
						),
					),
				);
				break;
			default:
				throw new \Exception('Unexpected field type: ' . $this->type);
		}
		
		return $spec;
	}
	
}
