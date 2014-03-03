<?php

namespace OmelettesMigration;

use Omelettes\Migration\AbstractMigration;

class Migration002Accounts extends AbstractMigration
{
	public function migrate()
	{
		$this->tableCreate('accounts', array_merge($this->getQuantumTableColumns(), array(
			'suspended'			=> 'TIMESTAMP WITH TIMEZONE',
		)));
		
		$this->tableAddColumns('users', array(
			'account_key'		=> 'UUID REFERENCES accounts(key)',
		));
		
		return true;
	}
	
}
