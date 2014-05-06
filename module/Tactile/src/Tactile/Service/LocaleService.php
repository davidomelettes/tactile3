<?php

namespace Tactile\Service;

use Tactile\Model;
use Zend\ServiceManager\ServiceLocatorAwareInterface,
	Zend\ServiceManager\ServiceLocatorAwareTrait;

class LocaleService implements ServiceLocatorAwareInterface
{
	use ServiceLocatorAwareTrait;
	
	/**
	 * @var Model\LocalesMapper
	 */
	protected $localesMapper;
	
	protected $locales = array();
	
	public function __construct(Model\LocalesMapper $mapper)
	{
		$this->localesMapper = $mapper;
	
		$this->loadLocales();
	}
	
	public function loadLocales()
	{
		$this->locales = $this->localesMapper->fetchAll();
		
		return $this;
	}
	
	public function getLocaleOptions()
	{
		$options = array();
		foreach ($this->locales as $locale) {
			$options[$locale->code] = $locale->name;
		}
		
		return $options;
	}
	
}