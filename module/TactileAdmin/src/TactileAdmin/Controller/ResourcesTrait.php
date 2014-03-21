<?php

namespace TactileAdmin\Controller;

use TactileAdmin\Form,
	TactileAdmin\Model;
use Omelettes\Paginator\Paginator;

trait ResourcesTrait
{
	/**
	 * @var Model\ResourcesMapper
	 */
	protected $resourcesMapper;
	
	/**
	 * @var Paginator
	 */
	protected $resourcesPaginator;
	
	/**
	 * @var Model\Resource
	 */
	protected $resource;
	
	/**
	 * @return Model\ResourcesMapper
	 */
	public function getResourcesMapper()
	{
		if (!$this->resourcesMapper) {
			$resourcesMapper = $this->getServiceLocator()->get('TactileAdmin\Model\ResourcesMapper');
			$this->resourcesMapper = $resourcesMapper;
		}
	
		return $this->resourcesMapper;
	}
	
	/**
	 * @return Paginator
	 */
	public function getResourcesPaginator($page = 1)
	{
		if (!$this->resourcesPaginator) {
			$resourcesPaginator = $this->getResourcesMapper()->fetchAll(true);
			$resourcesPaginator->setCurrentPageNumber($page);
			$this->resourcesPaginator = $resourcesPaginator;
		}
	
		return $this->resourcesPaginator;
	}
	
	/**
	 * @return Model\Resource
	 */
	public function getResource()
	{
		if (!$this->resource) {
			$model = new Model\Resource();
			$this->resource = $model;
		}
	
		return $this->resource;
	}
	
	/**
	 * @return Form\Resource\AddForm
	 */
	public function getAddResourceForm()
	{
		return $this->getForm('TactileAdmin\Form\Resource\AddForm');
	}
	
	public function getEditProtectedResourceForm()
	{
		return $this->getForm('TactileAdmin\Form\Resource\EditProtectedForm');
	}
	
	public function getEditUnprotectedResourceForm()
	{
		return $this->getForm('TactileAdmin\Form\Resource\EditUnprotectedForm');
	}
	
	/**
	 * @return Filter\ResourceFilter
	 */
	public function getResourceFilter(Model\Resource $resource)
	{
		$filter = $this->getFilter('TactileAdmin\Form\ResourceFilter');
		$filter->setResource($resource);
		return $filter;
	}
	
	/**
	 * @return Form\ResourceMetaForm
	 */
	public function getResourceMetaForm()
	{
		return $this->getForm('TactileAdmin\Form\ResourceMetaForm');
	}
	
	/**
	 * @return Form\ResourceMetaFilter
	 */
	public function getResourceMetaFilter()
	{
		return $this->getFilter('TactileAdmin\Form\ResourceMetaFilter');
	}
	
}