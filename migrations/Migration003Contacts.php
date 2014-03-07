<?php

namespace OmelettesMigration;

use Omelettes\Migration\AbstractMigration;

class Migration003Contacts extends AbstractMigration
{
	public function migrate()
	{
		$this->createAccountBoundQuantaTableWithView('contacts', array(
			
		));
		
		return true;
	}
	
}
