<?php

namespace Omelettes\Model;

class Account extends QuantumModel
{
	protected $planKey;
	
	protected $propertyMap = array(
		'planKey'	=> 'plan_key',
	);
	
}
