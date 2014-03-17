<?php

namespace Tactile\Controller;

use Tactile\Form,
	Tactile\Model;
use Omelettes\Paginator\Paginator;

trait ContactsTrait
{
	use ResourceTrait;
	
	/**
	 * @var Model\ContactsMapper
	 */
	protected $contactsMapper;
	
	/**
	 * @var Paginator
	 */
	protected $contactsPaginator;
	
	/**
	 * @var Model\Contact
	 */
	protected $contact;
	
	/**
	 * @var Form\ContactForm
	 */
	protected $contactForm;
	
	/**
	 * @var Form\ContactFilter
	 */
	protected $contactFilter;
	
	/**
	 * @return Model\ContactsMapper
	 */
	public function getContactsMapper()
	{
		if (!$this->contactsMapper) {
			$contactsMapper = $this->getServiceLocator()->get('Tactile\Model\ContactsMapper');
			$this->contactsMapper = $contactsMapper;
		}
		
		return $this->contactsMapper;
	}
	
	/**
	 * @return Paginator
	 */
	public function getContactsPaginator($page = 1)
	{
		if (!$this->contactsPaginator) {
			$contactsPaginator = $this->getContactsMapper()->fetchAll(true);
			$contactsPaginator->setCurrentPageNumber($page);
			$this->contactsPaginator = $contactsPaginator;
		}
	
		return $this->contactsPaginator;
	}
	
	/**
	 * @return Model\Contact
	 */
	public function getContact()
	{
		if (!$this->contact) {
			$model = new Model\Contact();
			$this->contact = $model;
		}
		
		return $this->contact;
	}
	
	/**
	 * @return Form\ContactForm
	 */
	public function getContactForm()
	{
		if (!$this->contactForm) {
			$form = $this->getServiceLocator()->get('FormElementManager')->get('Tactile\Form\ContactForm');
			$this->contactForm = $form;
		}
		
		return $this->contactForm;
	}
	
	/**
	 * @return Filter\ContactFilter
	 */
	public function getContactFilter()
	{
		if (!$this->contactFilter) {
			$filter = $this->getServiceLocator()->get('Tactile\Form\ContactFilter');
			$this->contactFilter = $filter;
		}
	
		return $this->contactFilter;
	}
	
}
