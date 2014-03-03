<?php

namespace Omelettes\Session\SaveHandler;

use Zend\Session\SaveHandler\DbTableGateway as ZendDbSessionSaveHandler;

class DbTableGateway extends ZendDbSessionSaveHandler
{
	const NULL_CHAR_SUBSTITUTION = '~~_NULL_~~';
	
	public function write($id, $data)
	{
		// Preprocess NULL characters
		$data = (string) $data;
		$data = preg_replace('/\x00/', self::NULL_CHAR_SUBSTITUTION, $data);
		
		return parent::write($id, $data);
	}
	
	public function read($id)
	{
		$data = parent::read($id);
		
		// Handle NULL character substitutions returned from database
		return preg_replace('/'.preg_quote(self::NULL_CHAR_SUBSTITUTION).'/', "\x00", $data);
	}
	
}
