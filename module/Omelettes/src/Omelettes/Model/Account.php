<?php

namespace Omelettes\Model;

class Account extends NamedItemModel
{
	protected $planKey;
	
	protected $propertyMap = array(
		'planKey'	=> 'plan_key',
	);
	
}
