<?php

namespace OmelettesMigration;

use Omelettes\Migration\AbstractMigration;

class Migration004PreferencesSettings extends AbstractMigration
{
	public function migrate()
	{
		$this->insertFixture('migrations/fixtures/004_preferences_settings.xml');
		
		return true;
	}
	
}
