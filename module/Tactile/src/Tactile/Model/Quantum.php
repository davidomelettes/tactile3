<?php

namespace Tactile\Model;

use Omelettes\Model\AccountBoundNamedItemModel;

class Quantum extends AccountBoundNamedItemModel
{
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
	
	public function setResource(Resource $resource)
	{
		$this->resource = $resource;
		
		return $this;
	}
	
	public function getResource()
	{
		return $this->resource;
	}
	
	public function setXmlSpecification($xml)
	{
		if (!empty($xml)) {
			// Attempt to parse XML
			
		}
		
		return $this;
	}
	
	public function toXml(array $data = array())
	{
		$xml = new \XMLWriter();
		$xml->openMemory();
		//$xml->startDocument('1.0', 'UTF-8');
		$xml->startElement('quantum');
		$xml->startElement('data');
		foreach ($data as $k => $v) {
			if (!is_null($v) && '' !== $v) {
				$xml->writeElement($k, $v);
			}
		}
		$xml->endElement();
		$xml->endElement();
		
		return $xml->outputMemory(true);
	}
	
	public function exchangeArray($data)
	{
		// Create XML string
		if (empty($data['xml_specification'])) {
			$data['xml_specification'] = $this->toXml($data);
		}
		parent::exchangeArray($data);
		
		return $this;
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
