<?php

namespace Omelettes\Model;

class AccountBoundNamedItemModel extends NamedItemModel
{
	protected $accountKey;
	
	protected $namedItemPropertyMap = array(
		'key'				=> 'key',
		'name'				=> 'name',
		'created'			=> 'created',
		'updated'			=> 'updated',
		'createdBy'			=> 'created_by',
		'updatedBy'			=> 'updated_by',
		'accountKey'		=> 'account_key',
	);
	
}
