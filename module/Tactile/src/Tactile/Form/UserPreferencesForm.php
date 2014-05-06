<?php

namespace Tactile\Form;

use Omelettes\Form;

class UserPreferencesForm extends Form\AbstractForm
{
	public function init()
	{
		$localeOptions = $this->getApplicationServiceLocator()->get('LocaleService')->getLocaleOptions();
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
	
		$this->addSubmitFieldset('Save', 'btn btn-success', 'Saving...');
	}
	
}
