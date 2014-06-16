<?php

namespace Tactile\Service;

use Tactile\Model;
use Zend\ServiceManager\ServiceLocatorAwareInterface,
	Zend\ServiceManager\ServiceLocatorAwareTrait;

class LocalesService implements ServiceLocatorAwareInterface
{
	use ServiceLocatorAwareTrait;
	
	/**
	 * @var Model\LocalesMapper
	 */
	protected $localesMapper;
	
	/**
	 * @var Model\LocaleCountriesMapper
	 */
	protected $countriesMapper;
	
	protected $timeZones;
	
	public function __construct(Model\LocalesMapper $localesMapper,
		Model\LocaleCountriesMapper $countriesMapper)
	{
		$this->localesMapper = $localesMapper;
		$this->countriesMapper = $countriesMapper;
	}
	
	public function getLocales()
	{
		return $this->localesMapper->fetchAll();
	}
	
	public function getTimeZones()
	{
		if (!$this->timeZones) {
			$timeZoneGroups = array(
				\DateTimeZone::AFRICA,
				\DateTimeZone::AMERICA,
				\DateTimeZone::ASIA,
				\DateTimeZone::ATLANTIC,
				\DateTimeZone::EUROPE,
				\DateTimeZone::INDIAN,
				\DateTimeZone::PACIFIC,
			);
			
			$zones = array();
			foreach ($timeZoneGroups as $group) {
				foreach (\DateTimeZone::listIdentifiers($group) as $tzName) {
					$zones[$tzName] = str_replace('_', ' ', $tzName);
				}
			}
			$this->timeZones = $zones;
		}
		
		return $this->timeZones;
	}
	
}
