<?php

namespace Omelettes\Migration\Fixture;

use Zend\Db\Adapter\Adapter as DbAdapter;

class Xml extends AbstractFixture
{
	/**
	 * @var \DOMDocument
	 */
	protected $xml;
	
	/**
	 * An array of tablename => (column => value)
	 * @var array
	 */
	protected $overrideValues = array();
	
	public function parse($fixture)
	{
		$this->fixture = $fixture;
		if (!file_exists($this->fixture)) {
			throw new \Exception('Expected accessible file at location: ' . $this->fixture);
		}
		
		$dom = \DOMDocument::load($this->fixture);
		if (!$dom instanceof \DOMDocument) {
			throw new \Exception('Failed to load XML from source: ' . $this->fixture);
		}
		$this->xml = $dom;
		
		return $this;
	}
	
	public function setOverrideValues(array $values = array())
	{
		$this->overrideValues = $values;
		
		return $this;
	}
	
	public function insert()
	{
		$xpath = new \DOMXPath($this->xml);
		$nodes = $xpath->query('/dataset/*');
		foreach ($nodes as $node) {
			$tableName = $node->tagName;
			$data = array();
			foreach ($node->attributes as $attribute) {
				$data[$attribute->nodeName] = $attribute->nodeValue;
			}
			if (isset($this->overrideValues[$tableName]) && is_array($this->overrideValues[$tableName])) {
				foreach ($this->overrideValues[$tableName] as $k => $v) {
					$data[$k] = $v;
				}
			}
			$this->insertRow($tableName, $data);
		}
		
		return true;
	}
	
}
