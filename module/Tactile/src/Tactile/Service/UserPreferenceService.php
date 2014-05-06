<?php

namespace Tactile\Service;

use Tactile\Model;
use Zend\ServiceManager\ServiceLocatorAwareInterface,
	Zend\ServiceManager\ServiceLocatorAwareTrait;

class UserPreferenceService implements ServiceLocatorAwareInterface
{
	use ServiceLocatorAwareTrait;
	
	/**
	 * @var Model\UserPreferencesMapper
	 */
	protected $userPreferencesMapper;
	
	protected $userPreferences = array();
	
	public function __construct(Model\UserPreferencesMapper $mapper)
	{
		$this->userPreferencesMapper = $mapper;
	
		$this->loadPreferences();
	}
	
	public function loadPreferences()
	{
		$this->userPreferences = array();
		$prefs = $this->userPreferencesMapper->fetchAll();
		foreach ($prefs as $pref) {
			$this->userPreferences[$pref->name] = $pref;
		}
		
		return $this;
	}
	
	public function getPreferenceOptions()
	{
		$options = array();
		foreach ($this->userPreferences as $name => $pref) {
			$v = $pref->value;
			if (is_null($v)) {
				$v = $pref->default;
			}
			$options[$name] = $v;
		}
		
		return $options;
	}
	
	public function savePreferences(array $prefs = array())
	{
		foreach ($prefs as $name => $value) {
			if (is_array($value)) {
				continue;
			}
			$this->savePreference($name, $value);
		}
		
		return $this;
	}
	
	public function savePreference($name, $value)
	{
		$type = 'varchar';
		if (!isset($this->userPreferences[$name])) {
			// Assume varchar
			$pref = new Model\UserPreference(array('name' => $name));
			$this->userPreferences[$name] = $pref;
		}
		$this->userPreferences[$name]->value = $value;
		$this->userPreferencesMapper->savePreference($this->userPreferences[$name]);
		
		return $this;
	}
	
	public function get($name)
	{
		if (!isset($this->userPreferences[$name])) {
			// Assume varchar
			return null;
		}
		
		$v = $this->userPreferences[$name]->value;
		if (is_null($v)) {
			$v = $this->userPreferences[$name]->default;
		}
		
		return $v;
	}
	
}