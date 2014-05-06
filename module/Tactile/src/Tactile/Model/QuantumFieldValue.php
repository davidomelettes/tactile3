<?php

namespace Tactile\Model;

use Omelettes\Model\XmlMultiTypeValue;

class QuantumFieldValue extends XmlMultiTypeValue
{
	protected $validTypes = array('varchar', 'text', 'datetime', 'integer', 'numeric', 'user', 'option');
	
}
