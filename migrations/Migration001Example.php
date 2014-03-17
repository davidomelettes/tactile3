<?php

namespace OmelettesMigration;

use Omelettes\Migration\AbstractMigration;

class Migration001Example extends AbstractMigration
{
	public function migrate()
	{
		/*
		$this->tableAddColumns('users', array(
			'name_reset_name'			=> 'VARCHAR',
			'name_reset_key'			=> 'UUID',
			'name_reset_requested'		=> 'TIMESTAMP WITH TIME ZONE',
			'password_reset_key'		=> 'UUID',
			'password_reset_requested'	=> 'TIMESTAMP WITH TIME ZONE',
		));
		
		$this->insertFixture('migration/fixtures/001_users.xml');
		
		$this->tableCreate('sessions', array(
			'id'		=> 'CHAR(32) NOT NULL',
			'name'		=> 'CHAR(32) NOT NULL',
			'modified'	=> 'INT NOT NULL',
			'lifetime'	=> 'INT NOT NULL',
			'data'		=> 'TEXT',
		), array('id', 'name'));
		
		$this->tableCreate('invitation_codes', array_merge($this->getNamedItemsTableColumns(), array(
			'full_name'	=> 'VARCHAR',
		)));
		*/
		
		return true;
	}
	
}
