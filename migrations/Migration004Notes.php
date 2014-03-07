<?php

namespace OmelettesMigration;

use Omelettes\Migration\AbstractMigration;

class Migration004Notes extends AbstractMigration
{
	public function migrate()
	{
		$this->createAccountBoundQuantaTableWithView('notes', array(
			'note'	=> 'TEXT NOT NULL',
		));
		
		return true;
	}
	
}
