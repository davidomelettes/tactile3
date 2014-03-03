<?php

namespace Omelettes\Form\Element;

use Zend\Form\Element\Text;

class Autocomplete extends Text
{
	protected $source;
	
	protected $sourceOptions = array();
	
	protected $attributes = array(
		'type' => 'autocomplete',
	);
	
	public function setOptions($options)
	{
		parent::setOptions($options);
	
		if (!isset($options['source'])) {
			throw new \Exception('Expected a source');
		}
		$this->setSource($options['source']);
		
		if (isset($options['source_options'])) {
			$this->setSourceOptions($options['source_options']);
		}
	
		return $this;
	}
	
	public function setSource($source)
	{
		$this->source = $source;
		
		return $this;
	}
	
	public function getSource()
	{
		return $this->source;
	}
	
	public function setSourceOptions(array $options = array())
	{
		$this->sourceOptions = $options;
	
		return $this;
	}
	
	public function getSourceOptions()
	{
		return $this->sourceOptions;
	}
	
}
