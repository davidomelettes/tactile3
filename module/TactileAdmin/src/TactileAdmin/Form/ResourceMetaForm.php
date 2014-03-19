<?php

namespace TactileAdmin\Form;

use TactileAdmin\Model;
use Zend\Form\Form,
	Zend\Form\FormInterface,
	Zend\ServiceManager\ServiceLocatorAwareInterface,
	Zend\ServiceManager\ServiceLocatorInterface;

class ResourceMetaForm extends Form implements ServiceLocatorAwareInterface
{
	/**
	 * @var ServiceLocatorInterface
	 */
	protected $serviceLocator;
	
	public function __construct($name = 'form-resource-meta')
	{
		parent::__construct($name);
	}
	
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
	{
		$this->serviceLocator = $serviceLocator;
	}
	
	public function getServiceLocator()
	{
		return $this->serviceLocator;
	}
	
	public function getApplicationServiceLocator()
	{
		return $this->getServiceLocator()->getServiceLocator();
	}
	
	public function init()
	{
		$this->add(array(
			'name'		=> 'label_singular',
			'type'		=> 'Text',
			'options'	=> array(
				'label'		=> 'Singular Name',
				'feedback'	=> 'glyphicon glyphicon-arrow-down',
			),
			'attributes'=> array(
				'id'			=> $this->getName() . 'SingularName',
				'autocomplete'	=> 'off',
			),
		));
		
		$this->add(array(
			'name'		=> 'label_plural',
			'type'		=> 'Text',
			'options'	=> array(
				'label'		=> 'Plural Name',
			),
			'attributes'=> array(
				'id'			=> $this->getName() . 'PluralName',
				'autocomplete'	=> 'off',
			),
		));
		
		$config = $this->getApplicationServiceLocator()->get('config');
		$basePath = preg_replace('/^https?:\/\//', '', $config['view_manager']['base_path']);
		$this->add(array(
			'name'		=> 'name',
			'type'		=> 'Text',
			'options'	=> array(
				'label'		=> 'URL Slug',
				'prefix'	=> $basePath.'/',
			),
			'attributes'=> array(
				'id'		=> $this->getName() . 'Name',
			),
		));
	}
	
}
