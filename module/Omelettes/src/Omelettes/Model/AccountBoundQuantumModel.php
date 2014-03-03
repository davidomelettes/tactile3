<?php

namespace Omelettes\Model;

class AccountBoundQuantumModel extends QuantumModel
{
	protected $quantumPropertyMap = array(
		'key'				=> 'key',
		'name'				=> 'name',
		'created'			=> 'created',
		'updated'			=> 'updated',
		'createdBy'			=> 'created_by',
		'updatedBy'			=> 'updated_by',
		'account'			=> 'account_key',
	);
	
	protected $account;
	
	
}
