<?php

namespace Tactile\Model;

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
	protected $searchable;
	protected $priority;
	
	protected $propertyMap = array(
		'type'				=> 'type',
		'label'				=> 'label',
		'protected'			=> 'protected',
		'required'			=> 'required',
		'searchable'		=> 'searchable',
	);
	
	public function getInputElementSpecification()
	{
		$spec = array();
		
		switch ($this->type) {
			case 'text':
				$spec = array(
					'name'		=> $this->name,
					'priority'	=> $this->priority,
					'type'		=> 'Textarea',
					'options'	=> array(
						'label'		=> $this->label,
					),
					'attributes'=> array(
						'id'		=> $this->name,
					),
				);
				break;
			case 'user':
				$fieldOptions = array('' => '-- Select User --');
				foreach ($this->getServiceLocator()->get('UsersService')->getUsers() as $user) {
					$fieldOptions[$user->key] = $user->fullName;
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
					),
				);
				break;
			case 'datetime':
				$spec = array(
					'name'		=> $this->name,
					'priority'	=> $this->priority,
					'type'		=> 'Text',
					'options'	=> array(
						'label'		=> $this->label,
						'feedback'	=> 'glyphicon glyphicon-calendar', 
					),
					'attributes'=> array(
						'id'			=> $this->name,
						'placeholder'	=> 'e.g. '. date('Y-m-d'),
					),
				);
				break;
			default:
				throw new \Exception('Unexpected field type: ' . $this->type);
		}
		
		return $spec;
	}
	
	public function getInputFilterSpecification()
	{
		$spec = array();
		
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
						array(
							'name'		=> 'Date',
							'options'	=> array(
								'format'	=> 'Y-m-d',
								'locale'	=> 'gb',
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
