<?php

namespace Omelettes\Migration\Fixture;

use Zend\Db\Adapter\Adapter as DbAdapter;

class Xml extends AbstractFixture
{
	/**
	 * @var \DOMDocument
	 */
	protected $xml;
	
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
			$this->insertRow($tableName, $data);
		}
		
		return true;
	}
	
}
