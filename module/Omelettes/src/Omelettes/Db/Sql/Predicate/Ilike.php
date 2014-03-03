<?php

namespace Omelettes\Db\Sql\Predicate;

use Zend\Db\Sql\Predicate\Like;

class Ilike extends Like
{
	protected $specification = '%1$s ILIKE %2$s';
	
}
