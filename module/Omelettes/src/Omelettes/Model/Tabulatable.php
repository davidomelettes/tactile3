<?php

namespace Omelettes\Model;

interface Tabulatable
{
	public function getTableHeadings();
	
	public function getTableRowPartial();
	
}
