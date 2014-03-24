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
	protected $resourceFieldsMapper;

	/**
	 * @var Paginator
	 */
	protected $resourceFieldsPaginator;

	/**
	 * @var Model\ResourceField
	 */
	protected $resourceField;

	/**
	 * @var Form\ResourceFieldForm
	 */
	protected $ResourceFieldForm;

	/**
	 * @var Form\ResourceFieldFilter
	 */
	protected $resourceFieldFilter;

	/**
	 * @return Model\ResourceFieldsMapper
	 */
	public function getResourceFieldsMapper()
	{
		if (!$this->resourceFieldsMapper) {
			$resourceFieldsMapper = $this->getServiceLocator()->get('TactileAdmin\Model\ResourceFieldsMapper');
			$this->resourceFieldsMapper = $resourceFieldsMapper;
		}

		return $this->resourceFieldsMapper;
	}

	/**
	 * @return Paginator
	 */
	public function getResourceFieldsPaginator($page = 1)
	{
		if (!$this->resourceFieldsPaginator) {
			$resourceFieldsPaginator = $this->getResourceFieldsMapper()->fetchAll(true);
			$resourceFieldsPaginator->setCurrentPageNumber($page);
			$this->resourceFieldsPaginator = $resourceFieldsPaginator;
		}

		return $this->resourceFieldsPaginator;
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
		if (!$this->resourceFieldForm) {
			$form = $this->getServiceLocator()->get('FormElementManager')->get('TactileAdmin\Form\ResourceFieldForm');
			$this->resourceFieldForm = $form;
		}

		return $this->resourceFieldForm;
	}

	/**
	 * @return Filter\ResourceFieldFilter
	 */
	public function getResourceFieldFilter()
	{
		if (!$this->resourceFilter) {
			$filter = $this->getServiceLocator()->get('TactileAdmin\Form\ResourceFilter');
			$this->resourceFilter = $filter;
		}

		return $this->resourceFilter;
	}

}