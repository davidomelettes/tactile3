<?php

namespace Tactile\Model;

use Omelettes\Model\AccountBoundNamedItemModel;

class ResourceField extends AccountBoundNamedItemModel
{
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
				$spec = array(
					'name'		=> $this->name,
					'priority'	=> $this->priority,
					'type'		=> 'Select',
					'options'	=> array(
						'label'		=> $this->label,
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
					),
					'attributes'=> array(
						'id'		=> $this->name,
					),
				);
				break;
			default:
				throw new \Exception('Unexpected field type: ' . $this->type);
		}
		
		return $spec;
	}
	
}
