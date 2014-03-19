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
	 * @var Form\ResourceForm
	 */
	protected $resourceForm;
	
	/**
	 * @var Form\ResourceMetaForm
	 */
	protected $resourceMetaForm;
	
	/**
	 * @var Form\ResourceFilter
	 */
	protected $resourceFilter;
	
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
	 * @return Form\ResourceForm
	 */
	public function getResourceForm()
	{
		if (!$this->resourceForm) {
			$form = $this->getServiceLocator()->get('FormElementManager')->get('TactileAdmin\Form\ResourceForm');
			$this->resourceForm = $form;
		}
	
		return $this->resourceForm;
	}
	
	/**
	 * @return Filter\ResourceFilter
	 */
	public function getResourceFilter()
	{
		if (!$this->resourceFilter) {
			$filter = $this->getServiceLocator()->get('TactileAdmin\Form\ResourceFilter');
			$this->resourceFilter = $filter;
		}
	
		return $this->resourceFilter;
	}
	
	/**
	 * @return Form\ResourceMetaForm
	 */
	public function getResourceMetaForm()
	{
		if (!$this->resourceMetaForm) {
			$form = $this->getServiceLocator()->get('FormElementManager')->get('TactileAdmin\Form\ResourceMetaForm');
			$this->resourceMetaForm = $form;
		}
	
		return $this->resourceMetaForm;
	}
	
	/**
	 * @return Form\ResourceMetaFilter
	 */
	public function getResourceMetaFilter()
	{
		if (!$this->resourceMetaFilter) {
			$filter = $this->getServiceLocator()->get('TactileAdmin\Form\ResourceMetaFilter');
			$this->resourceMetaFilter = $filter;
		}
	
		return $this->resourceMetaFilter;
	}
	
}