<?php

namespace Omelettes\Form;

use Zend\InputFilter\InputFilter,
	Zend\InputFilter\InputFilterInterface,
	Zend\InputFilter\InputFilterAwareInterface,
	Zend\ServiceManager\ServiceLocatorAwareInterface,
	Zend\ServiceManager\ServiceLocatorAwareTrait,
	Zend\Validator\ValidatorChain;

abstract class AbstractFilter implements InputFilterAwareInterface, ServiceLocatorAwareInterface
{
	use ServiceLocatorAwareTrait;
	
	/**
	 * @var InputFilter
	 */
	protected $inputFilter;
	
	public function setInputFilter(InputFilterInterface $inputFilter)
	{
		throw new \Exception('Not used');
	}
	
	protected function getDefaultInputFilter()
	{
		$inputFilter = new InputFilter();
	
		// We've got to do all this jiggery-pokery because otherwise
		// the input factory would not be aware of validators defined in the module config
		$factory = $inputFilter->getFactory();
		$validatorMananger = $this->getServiceLocator()->get('ValidatorManager');
		$chain = new ValidatorChain();
		$chain->setPluginManager($validatorMananger);
		$factory->setDefaultValidatorChain($chain);
	
		return $inputFilter;
	}
	
}
