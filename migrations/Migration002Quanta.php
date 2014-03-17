<?php

namespace OmelettesMigration;

use Omelettes\Migration\AbstractMigration;

class Migration002Quanta extends AbstractMigration
{
	public function migrate()
	{
		$this->tableCreate('resources',
			array(
				'account_key'		=> 'UUID NOT NULL REFERENCES accounts(key)',
				'name'				=> 'VARCHAR(256) NOT NULL',
				'created'			=> 'TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now()',
				'updated'			=> 'TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now()',
				'created_by'		=> 'UUID NOT NULL REFERENCES users(key)',
				'updated_by'		=> 'UUID NOT NULL REFERENCES users(key)',
				'deleted'			=> 'TIMESTAMP WITH TIME ZONE',
				'protected'			=> 'BOOLEAN NOT NULL DEFAULT true',
				'label_singular'	=> 'VARCHAR(256) NOT NULL',
				'label_plural'		=> 'VARCHAR(256) NOT NULL',
				'name_label'		=> 'VARCHAR(256) NOT NULL',
			),
			array('account_key', 'name')
		);
		$this->viewCreate('resources_view', 'SELECT * FROM resources');
		
		$this->tableCreate('resource_fields',
			array(
				'account_key'		=> 'UUID NOT NULL REFERENCES accounts(key)',
				'resource_name'		=> 'VARCHAR(256) NOT NULL',
				'name'				=> 'VARCHAR(256) NOT NULL',
				'created'			=> 'TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now()',
				'updated'			=> 'TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now()',
				'created_by'		=> 'UUID NOT NULL REFERENCES users(key)',
				'updated_by'		=> 'UUID NOT NULL REFERENCES users(key)',
				'deleted'			=> 'TIMESTAMP WITH TIME ZONE',
				'protected'			=> 'BOOLEAN NOT NULL DEFAULT true',
				'label'				=> 'VARCHAR(256) NOT NULL',
				'type'				=> 'VARCHAR(256) NOT NULL',
				'required'			=> 'BOOLEAN NOT NULL DEFAULT false',
				'searchable'		=> 'BOOLEAN NOT NULL DEFAULT false',
			),
			array('account_key', 'resource_name', 'name'),
			// Reference involves multiple columns
			'FOREIGN KEY (account_key, resource_name) REFERENCES resources (account_key, name)'
		);
		$this->viewCreate('resource_fields_view', 'SELECT * FROM resource_fields');
		
		$this->tableCreate('resource_field_options',
			array(
				'account_key'		=> 'UUID NOT NULL REFERENCES accounts(key)',
				'resource_name'		=> 'VARCHAR(256) NOT NULL',
				'field_name'		=> 'VARCHAR(256) NOT NULL',
				'name'				=> 'VARCHAR(256) NOT NULL',
				'created'			=> 'TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now()',
				'updated'			=> 'TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now()',
				'created_by'		=> 'UUID NOT NULL REFERENCES users(key)',
				'updated_by'		=> 'UUID NOT NULL REFERENCES users(key)',
				'deleted'			=> 'TIMESTAMP WITH TIME ZONE',
			),
			array('account_key', 'resource_name', 'field_name', 'name'),
			// Reference involves multiple columns
			'FOREIGN KEY (account_key, resource_name, field_name) REFERENCES resource_fields (account_key, resource_name, name)'
		);
		$this->viewCreate('resource_field_options_view', 'SELECT * FROM resource_field_options');
		
		$this->tableCreate('quanta',
			array_merge($this->getAccountBoundNamedItemsTableColumns(),
				array(
					'resource_name'			=> 'VARCHAR(256) NOT NULL',
					'xml_specification'		=> 'TEXT',
				)
			),
			array(),
			// Reference involves multiple columns
			'FOREIGN KEY (account_key, resource_name) REFERENCES resources (account_key, name)'
		);
		$this->viewCreate('quanta_view', 'SELECT * FROM quanta');
		
		// Search tables
		$this->tableCreate('quantum_search_varchar',
			array(
				'account_key'		=> 'UUID NOT NULL REFERENCES accounts(key)',
				'resource_name'		=> 'VARCHAR(256) NOT NULL',
				'field_name'		=> 'VARCHAR(256) NOT NULL',
				'quantum_key'		=> 'UUID NOT NULL REFERENCES quanta(key)',
				'value'				=> 'VARCHAR(256)',
			),
			array('quantum_key', 'field_name'),
			// Reference involves multiple columns
			'FOREIGN KEY (account_key, resource_name, field_name) REFERENCES resource_fields (account_key, resource_name, name)'
		);
		$this->tableCreate('quantum_search_integer',
			array(
				'account_key'		=> 'UUID NOT NULL REFERENCES accounts(key)',
				'resource_name'		=> 'VARCHAR(256) NOT NULL',
				'field_name'		=> 'VARCHAR(256) NOT NULL',
				'quantum_key'		=> 'UUID NOT NULL REFERENCES quanta(key)',
				'value'				=> 'INTEGER',
			),
			array('quantum_key', 'field_name'),
			// Reference involves multiple columns
			'FOREIGN KEY (account_key, resource_name, field_name) REFERENCES resource_fields (account_key, resource_name, name)'
		);
		$this->tableCreate('quantum_search_boolean',
			array(
				'account_key'		=> 'UUID NOT NULL REFERENCES accounts(key)',
				'resource_name'		=> 'VARCHAR(256) NOT NULL',
				'field_name'		=> 'VARCHAR(256) NOT NULL',
				'quantum_key'		=> 'UUID NOT NULL REFERENCES quanta(key)',
				'value'				=> 'BOOLEAN',
			),
			array('quantum_key', 'field_name'),
			// Reference involves multiple columns
			'FOREIGN KEY (account_key, resource_name, field_name) REFERENCES resource_fields (account_key, resource_name, name)'
		);
		$this->tableCreate('quantum_search_numeric',
			array(
				'account_key'		=> 'UUID NOT NULL REFERENCES accounts(key)',
				'resource_name'		=> 'VARCHAR(256) NOT NULL',
				'field_name'		=> 'VARCHAR(256) NOT NULL',
				'quantum_key'		=> 'UUID NOT NULL REFERENCES quanta(key)',
				'value'				=> 'NUMERIC(16,4)',
			),
			array('quantum_key', 'field_name'),
			// Reference involves multiple columns
			'FOREIGN KEY (account_key, resource_name, field_name) REFERENCES resource_fields (account_key, resource_name, name)'
		);
		$this->tableCreate('quantum_search_text',
			array(
				'account_key'		=> 'UUID NOT NULL REFERENCES accounts(key)',
				'resource_name'		=> 'VARCHAR(256) NOT NULL',
				'field_name'		=> 'VARCHAR(256) NOT NULL',
				'quantum_key'		=> 'UUID NOT NULL REFERENCES quanta(key)',
				'value'				=> 'TEXT',
			),
			array('quantum_key', 'field_name'),
			// Reference involves multiple columns
			'FOREIGN KEY (account_key, resource_name, field_name) REFERENCES resource_fields (account_key, resource_name, name)'
		);
		$this->tableCreate('quantum_search_timestamp',
			array(
				'account_key'		=> 'UUID NOT NULL REFERENCES accounts(key)',
				'resource_name'		=> 'VARCHAR(256) NOT NULL',
				'field_name'		=> 'VARCHAR(256) NOT NULL',
				'quantum_key'		=> 'UUID NOT NULL REFERENCES quanta(key)',
				'value'				=> 'TIMESTAMP WITH TIME ZONE',
			),
			array('quantum_key', 'field_name'),
			// Reference involves multiple columns
			'FOREIGN KEY (account_key, resource_name, field_name) REFERENCES resource_fields (account_key, resource_name, name)'
		);
		$this->tableCreate('quantum_search_user',
			array(
				'account_key'		=> 'UUID NOT NULL REFERENCES accounts(key)',
				'resource_name'		=> 'VARCHAR(256) NOT NULL',
				'field_name'		=> 'VARCHAR(256) NOT NULL',
				'quantum_key'		=> 'UUID NOT NULL REFERENCES quanta(key)',
				'value'				=> 'UUID NOT NULL REFERENCES users(key)',
			),
			array('quantum_key', 'field_name'),
			// Reference involves multiple columns
			'FOREIGN KEY (account_key, resource_name, field_name) REFERENCES resource_fields (account_key, resource_name, name)'
		);
		$this->tableCreate('quantum_search_option',
			array(
				'account_key'		=> 'UUID NOT NULL REFERENCES accounts(key)',
				'resource_name'		=> 'VARCHAR(256) NOT NULL',
				'field_name'		=> 'VARCHAR(256) NOT NULL',
				'quantum_key'		=> 'UUID NOT NULL REFERENCES quanta(key)',
				'value'				=> 'VARCHAR(256) NOT NULL',
			),
			array('quantum_key', 'field_name'),
			// Reference involves multiple columns
			'FOREIGN KEY (account_key, resource_name, field_name, value) REFERENCES resource_field_options (account_key, resource_name, field_name, name)'
		);
		
		return true;
	}
	
}
