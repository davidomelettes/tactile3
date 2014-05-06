<?php

namespace OmelettesMigration;

use Omelettes\Migration\AbstractMigration;

class Migration002Locales extends AbstractMigration
{
	public function migrate()
	{
		$this->tableCreate('locale_countries', array(
			'code'		=> 'CHAR(2) PRIMARY KEY',
			'name'		=> 'VARCHAR NOT NULL',
		));
		
		$this->tableCreate('locale_languages', array(
			'code'		=> 'CHAR(2) PRIMARY KEY',
			'name'		=> 'VARCHAR NOT NULL',
			'native'	=> 'VARCHAR NOT NULL',
		));
		
		$this->tableCreate('locale_currencies', array(
			'code'					=> 'CHAR(3) PRIMARY KEY',
			'name'					=> 'VARCHAR NOT NULL',
			'symbol'				=> 'CHAR(1)',
			'symbol_prefix'			=> 'BOOLEAN NOT NULL DEFAULT true',
			'decimals'				=> 'INT NOT NULL',
			'decimal_separator'		=> 'CHAR(1) NOT NULL',
			'thousands_separator'	=> 'CHAR(1) NOT NULL',
		));
		
		$this->tableCreate('locale_date_formats', array(
			'code'			=> 'CHAR(3) PRIMARY KEY',
			'format'		=> 'VARCHAR NOT NULL',
			'php_format'	=> 'VARCHAR NOT NULL',
		));
		
		$this->tableCreate('locales', array(
			'code'			=> 'VARCHAR PRIMARY KEY',
			'country_code'	=> 'CHAR(2) NOT NULL REFERENCES locale_countries(code)',
			'language_code'	=> 'CHAR(2) NOT NULL REFERENCES locale_languages(code)',
			'currency_code'	=> 'CHAR(3) NOT NULL REFERENCES locale_currencies(code)',
			'date_code'		=> 'CHAR(3) NOT NULL REFERENCES locale_date_formats(code)',
		));
		
		$this->insertFixture('migrations/fixtures/002_locales.xml');
		
		$this->viewCreate('locales_view',
			"SELECT locales.*,
				locale_countries.name as country_name,
				locale_languages.name as language_name,
				locale_languages.native as language_native,
				locale_currencies.name as currency_name,
				locale_currencies.symbol as currency_symbol,
				locale_currencies.decimals as currency_decimals,
				locale_currencies.decimal_separator as currency_decimal_separator,
				locale_currencies.thousands_separator as currency_thousands_separator,
				locale_date_formats.format as date_format,
				locale_date_formats.php_format as date_php_format,
				locale_languages.name || ' (' || locale_countries.name || ')' as name
			FROM locales
			LEFT JOIN locale_countries ON locales.country_code = locale_countries.code
			LEFT JOIN locale_languages ON locales.language_code = locale_languages.code
			LEFT JOIN locale_currencies ON locales.currency_code = locale_currencies.code
			LEFT JOIN locale_date_formats ON locales.date_code = locale_date_formats.code"
		);
		
		return true;
	}
	
}
