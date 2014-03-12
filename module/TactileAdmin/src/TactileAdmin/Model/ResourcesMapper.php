<?php

namespace TactileAdmin\Model;

use Omelettes\Model\AccountBoundQuantaMapper;
use Zend\Db\Sql;

class ResourcesMapper extends AccountBoundQuantaMapper
{
	public function installSystemResources()
	{
		$auth = $this->getServiceLocator()->get('AuthService');
		$config = $this->getServiceLocator()->get('config');
		$adapter = $this->writeTableGateway->getAdapter();
		
		$dom = \DOMDocument::load($config['paths']['resource-fixture']);
		$xpath = new \DOMXPath($dom);
		$nodes = $xpath->query('/dataset/*');
		foreach ($nodes as $node) {
			$tableName = $node->tagName;
			$data = array(
				'account_key'	=> $auth->getIdentity()->accountKey,
				'created_by'	=> $auth->getIdentity()->key,
				'updated_by'	=> $auth->getIdentity()->key,
			);
			foreach ($node->attributes as $attribute) {
				$data[$attribute->nodeName] = $attribute->nodeValue;
			}
			$sql = new Sql\Sql($this->writeTableGateway->getAdapter());
			$insert = $sql->insert($tableName)->values($data);
			$statement = $sql->prepareStatementForSqlObject($insert);
			$results = $statement->execute();
		}
		
	}
	
}
