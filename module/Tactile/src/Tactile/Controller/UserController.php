<?php

namespace Tactile\Controller;

use Omelettes\Controller;

class UserController extends Controller\AbstractController
{
	/**
	 * @var Model\UserPreferencesMapper
	 */
	protected $userPreferencesMapper;
	
	public function getUserPreferencesForm()
	{
		return $this->getForm('Tactile\Form\UserPreferences');
	}
	
	public function getUserPreferencesMapper()
	{
		if (!$this->userPreferencesMapper) {
			$mapper = $this->getServiceLocator()->get('Tactile\Model\UserPreferencesMapper');
			$this->userPreferencesMapper = $mapper;
		}
		
		return $this->userPreferencesMapper;
	}
	
	public function preferencesAction()
	{
		$form = $this->getUserPreferencesForm();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$form->setInputFilter($this->getContactFilter()->getInputFilter());
			$form->setData($request->getPost());
			if ($form->isValid()) {
				// Update user preferences
				$this->getUserPreferencesMapper()->updatePreferences($form->getData());
			}
		}
		
		return $this->returnViewModel(array(
			'form'	=> $form,
		));
	}
	
}
