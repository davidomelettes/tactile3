<?php

namespace TactileAdmin\Model;

use Tactile\Model\ResourcesMapper as TactileResourcesMapper;
use Zend\Db\Sql;

class ResourcesMapper extends TactileResourcesMapper
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
	
	protected function prepareSaveData(Resource $model)
	{
		$data = parent::prepareSaveData($model);
		
		$data = array_merge($data, array(
			'label_plural'		=> $model->labelPlural,
			'label_singular'	=> $model->labelPlural,
			'name_label'		=> 'Name',
			'protected'			=> $model->protected ? 'true' : 'false',
		));
		
		return $data;
	}
	
	public function createUnprotectedResource(Resource $model)
	{
		$name = $model->name;
		$data = $this->prepareSaveData($model);
		if (empty($data['created_by'])) {
			// Updating
			$this->writeTableGateway->update($data, array('name' => $name, 'account_key' => $this->getAccountKey()));
		} else {
			// Creating
			$data['account_key'] = $this->getAccountKey();
			$data['protected'] = 'false';
			$this->writeTableGateway->insert($data);
		}
		
		// Rehydrate
		$model->exchangeArray($data);
	}
	
	public function updateResource(Resource $model)
	{
		$name = $model->name;
		$data = $this->prepareSaveData($model);
		$this->writeTableGateway->update($data, array('name' => $name, 'account_key' => $this->getAccountKey()));
		
		// Rehydrate
		$model->exchangeArray($data);
	}
	
}
