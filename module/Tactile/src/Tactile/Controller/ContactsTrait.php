<?php

namespace Tactile\Controller;

use Tactile\Model;
use Omelettes\Paginator\Paginator;

trait ContactsTrait
{
	/**
	 * @var Model\ContactsMapper
	 */
	protected $contactsMapper;
	
	/**
	 * @var Paginator
	 */
	protected $contactsPaginator;
	
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
	
}
