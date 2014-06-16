<?php

namespace Tactile\Model;

use Omelettes\Model\AccountBoundNamedItemModel,
	Omelettes\Model\XmlInflatableInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface,
	Zend\ServiceManager\ServiceLocatorAwareTrait;

class Quantum extends AccountBoundNamedItemModel implements ServiceLocatorAwareInterface, XmlInflatableInterface
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
				return $this->fieldData[$name]->getScalarValue();
			}
			throw new \Exception('Invalid ' . get_class($this) . ' property: ' . $name);
		}

		return $this->$getterMethodName();
	}
	
	public function setResource(Resource $resource)
	{
		$this->resource = $resource;
		$this->fieldData = $this->resource->getDefaultFieldValues();
		
		return $this;
	}
	
	public function getResource()
	{
		return $this->resource;
	}
	
	public function xmlDeflate()
	{
		$userPrefsService = $this->getServiceLocator()->get('UserPreferencesService');
		$tz = $userPrefsService->get('time_zone');
		
		$xml = new \XMLWriter();
		$xml->openMemory();
		$xml->startElement('qt');
		$xml->writeAttribute('v', '1.0');
		$xml->startElement('data');
		foreach ($this->fieldData as $key => $fieldValue) {
			$v = $fieldValue->getScalarValue();
			if (is_array($v)) {
				throw new \Exception('Cannot deflate array to XML');
			} elseif (!is_null($v) && '' !== $v) {
				switch ($fieldValue->getType()) {
					case 'datetime':
						$xml->startElement($key);
						$xml->writeAttribute('time', $fieldValue->getDateTimeHasTime() ? 'true' : 'false');
						$xml->text($v);
						$xml->endElement();
						break;
					default:
						$xml->writeElement($key, $v);
				}
			}
		}
		$xml->endElement();
		$xml->endElement();
		
		$this->xmlSpecification = $xml->outputMemory(true);
		
		return $this;
	}
	
	public function xmlInflate()
	{
		if (!empty($this->xmlSpecification)) {
			$dom = new \DOMDocument('1.0', 'UTF-8');
			
			// Supress warnings
			$result = @$dom->loadXML($this->xmlSpecification);
			if (!$result) {
				// Do something?!
				return $this;
			}
			$xpath = new \DOMXPath($dom);
			$version = $xpath->evaluate('/qt/@v');
			switch ($version) {
				default:
					// 1.0
					$nodes = $xpath->query('/qt/data/*');
					foreach ($nodes as $node) {
						if (array_key_exists($node->tagName, $this->fieldData)) {
							switch ($this->fieldData[$node->tagName]->getType()) {
								case 'datetime':
									$datetime = \DateTime::createFromFormat('Y-m-d H:i:sO', $node->nodeValue);
									if ($datetime !== false && \DateTime::getLastErrors()['warning_count'] < 1) {
										$value = array('date' => $datetime->format('Y-m-d'));
										if ($node->getAttribute('time') === 'true') {
											$value['time'] = $datetime->format('H:i');
										}
										$this->fieldData[$node->tagName]->setValue($value);
									}
									break;
								default:
									$this->fieldData[$node->tagName]->setValue($node->nodeValue);
							}
						}
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
		foreach ($this->fieldData as $fieldName => $fieldValue) {
			switch ($fieldValue->getType()) {
				case 'datetime':
					$copy[$fieldName] = $fieldValue->getValue();
					break;
				default:
					$copy[$fieldName] = $fieldValue->getValue();
			}
		}
		var_dump($copy);
	
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
