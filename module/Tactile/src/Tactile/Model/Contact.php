<?php

namespace Tactile\Model;

class Contact extends Quantum
{
	public function getTableHeadings()
	{
		return array(
			'name'				=> 'Name',
			'last_contacted'	=> 'Last Contacted',
		);
	}
	
	public function getTableRowPartial()
	{
		return 'tabulate/contact';
	}
	
}
