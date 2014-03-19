<?php

namespace TactileAdmin\Controller;

use TactileAdmin\Form,
	TactileAdmin\Model;
use Omelettes\Paginator\Paginator;

trait ResourceFieldsTrait
{
	/**
	 * @var Model\ResourceFieldsMapper
	 */
	protected $resourceMapper;

	/**
	 * @var Paginator
	 */
	protected $resourceFieldsPaginator;

	/**
	 * @var Model\Resource
	 */
	protected $resource;

	/**
	 * @var Form\ResourceFieldForm
	 */
	protected $ResourceFieldForm;

	/**
	 * @var Form\ResourceMetaForm
	 */
	protected $resourceMetaForm;

	/**
	 * @var Form\ResourceFilter
	 */
	protected $resourceFilter;

	/**
	 * @return Model\ResourceFieldsMapper
	 */
	public function getResourceFieldsMapper()
	{
		if (!$this->ResourceFieldsMapper) {
			$resourceFieldsMapper = $this->getServiceLocator()->get('TactileAdmin\Model\ResourceFieldsMapper');
			$this->ResourceFieldsMapper = $resourceFieldsMapper;
		}

		return $this->ResourceFieldsMapper;
	}

	/**
	 * @return Paginator
	 */
	public function getResourceFieldsPaginator($page = 1)
	{
		if (!$this->ResourceFieldsPaginator) {
			$resourceFieldsPaginator = $this->getResourceFieldsMapper()->fetchAll(true);
			$resourceFieldsPaginator->setCurrentPageNumber($page);
			$this->ResourceFieldsPaginator = $resourceFieldsPaginator;
		}

		return $this->ResourceFieldsPaginator;
	}

	/**
	 * @return Model\ResourceField
	 */
	public function getResourceField()
	{
		if (!$this->resourceField) {
			$model = new Model\ResourceField();
			$this->resourceField = $model;
		}

		return $this->resourceField;
	}

	/**
	 * @return Form\ResourceFieldForm
	 */
	public function getResourceFieldForm()
	{
		if (!$this->ResourceFieldForm) {
			$form = $this->getServiceLocator()->get('FormElementManager')->get('TactileAdmin\Form\ResourceFieldForm');
			$this->ResourceFieldForm = $form;
		}

		return $this->ResourceFieldForm;
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