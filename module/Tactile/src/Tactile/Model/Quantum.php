<?php

namespace Tactile\Model;

use Omelettes\Model\AccountBoundNamedItemModel;
use Zend\ServiceManager\ServiceLocatorAwareInterface,
	Zend\ServiceManager\ServiceLocatorAwareTrait;

class Quantum extends AccountBoundNamedItemModel implements ServiceLocatorAwareInterface
{
	use ServiceLocatorAwareTrait;
	
	/**
	 * @var Resource
	 */
	protected $resource;
	
	/**
	 * @var string
	 */
	protected $xmlSpecification;
	
	/**
	 * @var array
	 */
	protected $fieldData = array();
	
	protected $propertyMap = array(
		'xmlSpecification'	=> 'xml_specification',
	);
	
	public function __get($name)
	{
		$map = $this->getPropertyMap();
		$getterMethodName = 'get' . ucfirst($name);
		if (!method_exists($this, $getterMethodName) && !isset($map[$name])) {
			if (array_key_exists($name, $this->fieldData)) {
				// Field value
				return $this->fieldData[$name]->getValue();
			}
			throw new \Exception('Invalid ' . get_class($this) . ' property: ' . $name);
		}

		return $this->$getterMethodName();
	}
	
	public function setResource(Resource $resource)
	{
		$this->resource = $resource;
		$this->fieldData = $this->resource->getBlankFieldValues();
		
		return $this;
	}
	
	public function getResource()
	{
		return $this->resource;
	}
	
	public function toXml()
	{
		$xml = new \XMLWriter();
		$xml->openMemory();
		$xml->startElement('quantum');
		$xml->startElement('data');
		foreach ($this->fieldData as $key => $fieldValue) {
			$v = $fieldValue->getValue();
			if (!is_null($v) && '' !== $v) {
				$xml->writeElement($key, $v);
			}
		}
		$xml->endElement();
		$xml->endElement();
		
		return $xml->outputMemory(true);
	}
	
	public function inflate()
	{
		if (!empty($this->xmlSpecification)) {
			$dom = new \DOMDocument('1.0', 'UTF-8');
			$dom->loadXML(''.$this->xmlSpecification);
			$xpath = new \DOMXPath($dom);
			$nodes = $xpath->query('/quantum/data/*');
			foreach ($nodes as $node) {
				if (array_key_exists($node->tagName, $this->fieldData)) {
					$this->fieldData[$node->tagName]->setValue($node->nodeValue);
				}
			}
		}
		
		return $this;
	}
	
	public function exchangeArray($data)
	{
		if (!$this->resource) {
			if (!isset($data['resource_name'])) {
				throw new \Exception('Resource not set');
			}
			$this->setResource($this->getServiceLocator()->get('ResourceService')->getResource($data['resource_name']));
			unset($data['resource_name']);
		}
		$map = $this->getPropertyMap();
		foreach ($data as $key => $value) {
			if (false !== ($property = array_search($key, $map))) {
				$setterMethodName = 'set'.ucfirst($property);
				$this->$setterMethodName($value);
			} elseif (array_key_exists($key, $this->fieldData)) {
				// It's a resource field value
				$this->fieldData[$key]->setValue($value); 
			} else {
				// Trying to set an unrecognised property; ignore
			}
		}
		
		return $this;
	}
	
	public function getArrayCopy()
	{
		$copy = array();
		$map = $this->getPropertyMap();
		foreach ($map as $property => $column) {
			$getterMethodName = 'get'.ucfirst($property);
			$copy[$column] = $this->$getterMethodName();
		}
		foreach ($this->fieldData as $name => $field) {
			$copy[$name] = $field->getValue();
		}
	
		return $copy;
	}
	
	public function getTableHeadings()
	{
		return array(
			'name'				=> 'Name',
		);
	}
	
	public function getTableRowPartial()
	{
		return 'tabulate/quantum';
	}
	
}
