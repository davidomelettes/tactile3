<?php

namespace Omelettes\Model;

class AccountBoundQuantumModel extends QuantumModel
{
	protected $accountKey;
	
	protected $quantumPropertyMap = array(
		'key'				=> 'key',
		'name'				=> 'name',
		'created'			=> 'created',
		'updated'			=> 'updated',
		'createdBy'			=> 'created_by',
		'updatedBy'			=> 'updated_by',
		'accountKey'		=> 'account_key',
	);
	
}
