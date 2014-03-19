<?php

namespace Tactile\Model;

class ContactsMapper extends QuantaMapper
{
	public function createContact(Contact $contact)
	{
		return $this->createNamedItem($contact);
	}
	
	public function saveContact(Contact $contact)
	{
		return $this->updateNamedItem($contact);
	}
	
}
