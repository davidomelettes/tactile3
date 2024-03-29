<?php

namespace TactileAdmin\Form;

use TactileAdmin\Model;
use Omelettes\Form\NamedItemFilter,
	Omelettes\Validator;
use Zend\Validator as ZendValidator;

class ResourceFilter extends NamedItemFilter
{
	/**
	 * @var Model\ResourcesMapper
	 */
	protected $resourcesMapper;
	
	/**
	 * @var Model\Resource
	 */
	protected $resource;
	
	public function __construct(Model\ResourcesMapper $resourcesMapper)
	{
		$this->resourcesMapper = $resourcesMapper;
	}
	
	public function setResource(Model\Resource $resource)
	{
		$this->resource = $resource;
	}
	
	public function getInputFilter()
	{
		if (!$this->resource) {
			throw new \Exception('Resource not set');
		}
		
		if (!$this->inputFilter) {
			$inputFilter = parent::getInputFilter();
			$factory = $inputFilter->getFactory();
				
			$inputFilter->add($factory->createInput(array(
				'name'			=> 'label_singular',
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
				'name'			=> 'label_plural',
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
			
			$config = $this->getServiceLocator()->get('config');
			$routeList = array_keys($config['router']['routes']);
			$inputFilter->add($factory->createInput(array(
				'name'			=> 'name',
				'required'		=> 'true',
				'filters'		=> array(
					array('name' => 'StringTrim'),
					array('name' => 'StringToLower'),
				),
				'validators'	=> array(
					array(
						'name'		=> 'StringLength',
						'options'	=> array(
							'encoding'	=> 'UTF-8',
							'min'		=> 4,
							'max'		=> 255,
						),
					),
					array(
						'name'		=> 'Regex',
						'options'	=> array(
							'pattern'	=> '/^[a-z][a-z0-9-]*$/',
							'messages'	=> array(
								ZendValidator\Regex::NOT_MATCH => 'Must start with a letter, and only contain letters, numbers, and hyphens',
							),
						),
					),
					array(
						'name'		=> 'NotRoute',
						'options'	=> array(
							'routeList'	=> $routeList,
						),
					),
					array(
						'name'		=> 'Omelettes\Validator\Model\DoesNotExist',
						'options'	=> array(
							'mapper'	=> $this->resourcesMapper,
							'field'		=> 'name',
							'exclude'	=> array(
								'field'	=> 'key',
								'value'	=> $this->resource->key,
							),
							'messages'	=> array(
								Validator\Model\DoesNotExist::ERROR_MODEL_EXISTS => 'A Resource with that route already exists',
							),
						),
					),
				),
			)));
	
				
			$this->inputFilter = $inputFilter;
		}
	
		return $inputFilter;
	}
	
}
