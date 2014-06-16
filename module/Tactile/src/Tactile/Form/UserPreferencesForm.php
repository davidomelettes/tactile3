<?php

namespace Tactile\Form;

use Omelettes\Form;

class UserPreferencesForm extends Form\UserPreferencesForm
{
	public function init()
	{
		$localesService = $this->getApplicationServiceLocator()->get('LocalesService');
		
		$localeOptions = array();
		foreach ($localesService->getLocales() as $locale) {
			$localeOptions[$locale->code] = $locale->name;
		} 
		$this->add(array(
			'name'		=> 'locale',
			'type'		=> 'Select',
			'options'	=> array(
				'label'		=> 'Language',
				'options'	=> $localeOptions,
			),
			'attributes'=> array(
				'id'		=> $this->getName() . 'Locale',
			),
		));
		
		$timeZoneOptions = array();
		foreach ($localesService->getTimeZones() as $name => $label) {
			$timeZoneOptions[$name] = $label;
		}
		$this->add(array(
			'name'		=> 'time_zone',
			'type'		=> 'Select',
			'options'	=> array(
				'label'		=> 'Time Zone',
				'options'	=> $timeZoneOptions,
			),
			'attributes'=> array(
				'id'		=> $this->getName() . 'TimeZone',
			),
		));
	
		$now = time();
		$dateFormatOptions = array(
			'dmy' => date('d/m/Y', $now) . ' (dd/mm/yyyy)',
			'mdy' => date('m/d/Y', $now) . ' (mm/dd/yyyy)',
			'ymd' => date('Y-m-d', $now) . ' (yyyy-mm-dd)',
		);
		$this->add(array(
			'name'		=> 'date_format',
			'type'		=> 'Select',
			'options'	=> array(
				'label'		=> 'Date Format',
				'options'	=> $dateFormatOptions,
			),
			'attributes'=> array(
				'id'		=> $this->getName() . 'Name',
			),
		));
	
		$this->addSubmitFieldset('Save Preferences', 'btn btn-success', 'Saving...');
	}
	
}
