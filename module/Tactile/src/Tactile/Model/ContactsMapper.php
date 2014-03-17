<?php

namespace Tactile\Model;

class ContactsMapper extends QuantaMapper
{
	public function createContact(Contact $contact)
	{
		return $this->createQuantum($contact);
	}
	
}
