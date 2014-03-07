<?php

namespace OmelettesMigration;

use Omelettes\Migration\AbstractMigration;

class Migration002Quanta extends AbstractMigration
{
	public function migrate()
	{
		$this->tableCreate('resources', array_merge($this->getQuantumTableColumns(), array(
			'label_singular'	=> 'VARCHAR NOT NULL',
			'label_plural'		=> 'VARCHAR NOT NULL',
		)));
		
		$this->tableCreate('quanta', array_merge($this->getQuantumTableColumns(), array(
			'resource_key'		=> 'UUID NOT NULL REFERENCES resources(key)',
		)));
		
		$this->tableCreate('resource_fields', array_merge($this->getQuantumTableColumns(), array(
			'type'				=> 'VARCHAR NOT NULL',
			'label'				=> 'VARCHAR NOT NULL',
		)));
		
		$this->tableCreate('quantum_varchar_values', array(
			'quantum_key'		=> 'UUID NOT NULL REFERENCES quanta(key)',
			'field_key'			=> 'UUID NOT NULL REFERENCES resource_fields(key)',
			'value'				=> 'VARCHAR(256)',
		));
		
		$this->tableCreate('quantum_values_integer', array(
			'quantum_key'		=> 'UUID NOT NULL REFERENCES quanta(key)',
			'field_key'			=> 'UUID NOT NULL REFERENCES resource_fields(key)',
			'value'				=> 'INTEGER',
		));
		
		$this->tableCreate('quantum_values_boolean', array(
			'quantum_key'		=> 'UUID NOT NULL REFERENCES quanta(key)',
			'field_key'			=> 'UUID NOT NULL REFERENCES resource_fields(key)',
			'value'				=> 'BOOLEAN',
		));
		
		$this->tableCreate('quantum_values_numeric', array(
			'quantum_key'		=> 'UUID NOT NULL REFERENCES quanta(key)',
			'field_key'			=> 'UUID NOT NULL REFERENCES resource_fields(key)',
			'value'				=> 'NUMERIC(16,4)',
		));
		
		$this->tableCreate('quantum_values_text', array(
			'quantum_key'		=> 'UUID NOT NULL REFERENCES quanta(key)',
			'field_key'			=> 'UUID NOT NULL REFERENCES resource_fields(key)',
			'value'				=> 'TEXT',
		));
		
		$this->tableCreate('quantum_values_timestamp', array(
			'quantum_key'		=> 'UUID NOT NULL REFERENCES quanta(key)',
			'field_key'			=> 'UUID NOT NULL REFERENCES resource_fields(key)',
			'value'				=> 'TIMESTAMP',
		));
		
		return true;
	}
	
}
