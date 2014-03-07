<?php

namespace Tactile\Model;

use Omelettes\Model\AccountBoundQuantaMapper;

class ContactsMapper extends AccountBoundQuantaMapper
{
	public function createContact(Contact $contact)
	{
		return $this->createQuantum($contact);
	}
	
}
