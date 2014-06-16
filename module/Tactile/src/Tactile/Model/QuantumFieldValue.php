<?php

namespace Tactile\Model;

use Omelettes\Model\XmlMultiTypeValue;

class QuantumFieldValue extends XmlMultiTypeValue
{
	protected $validTypes = array('varchar', 'text', 'datetime', 'integer', 'numeric', 'user', 'option');
	
	/**
	 * @var Resource
	 */
	protected $resource;
	
	public function __construct(Resource $resource, $type, $value = null)
	{
		$this->resource = $resource;
		
		$this->setType($type);
		$this->setValue($value);
	}
	
	public function getScalarValue()
	{
		switch ($this->type) {
			case 'datetime':
				$value = $this->getValue();
				$userPrefs = $this->resource->getServiceLocator()->get('UserPreferencesService');
				$timeZoneName = $userPrefs->get('time_zone');
		
				if (is_array($value) && isset($value['date']) && '' !== $value['date']) {
					// Date-time value is a form array
					$date = $value['date'];
					if (isset($value['time']) && !empty($value['time'])) {
						$this->dateTimeHasTime = true;
					} else {
						$value['time'] = '00:00';
					}
					$time = $value['time'];
		
					// User input is relative to user time zone
					try {
						$tz = new \DateTimeZone($timeZoneName);
					} catch (\Exception $e) {
						// Application default
						$tz = new \DateTimeZone(date_default_timezone_get());
					}
					$dt = \DateTime::createFromFormat('Y-m-d H:i', "$date $time", $tz);
					if (\DateTime::getLastErrors()['warning_count'] < 1) {
						// Valid date-time
						$dt->setTimezone($tz);
						$value = $dt->format('Y-m-d H:i:00O');
					} else {
						// Invalid date-time
						$value = null;
					}
				} elseif (is_scalar($value)) {
					// Date-time value is a scalar; do nothing
				} else {
					// Date-time value is an array, but missing the date
					// Ignore
					$value = null;
				}
				break;
			default:
				$value = $this->getValue();
		}
		
		return $value;
	}
	
}
