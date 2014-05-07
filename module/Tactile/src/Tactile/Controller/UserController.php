<?php

namespace Tactile\Controller;

use Omelettes\Controller;

class UserController extends Controller\AbstractController
{
	use Controller\AuthUsersTrait;
	
	public function getUserPreferencesForm()
	{
		return $this->getForm('Tactile\Form\UserPreferencesForm');
	}
	
	public function getUserPreferencesFilter()
	{
		return $this->getFilter('Tactile\Form\UserPreferencesFilter');
	}
	
	public function preferencesAction()
	{
		$form = $this->getUserPreferencesForm();
		
		$prefService = $this->getServiceLocator()->get('UserPreferencesService');
		$prefs = $prefService->getPreferenceOptions();
		$form->setData($prefs);
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$form->setInputFilter($this->getUserPreferencesFilter()->getInputFilter());
			$form->setData($request->getPost());
			if ($form->isValid()) {
				// Update user preferences
				$prefService->savePreferences($form->getData());
			}
		}
		
		return $this->returnViewModel(array(
			'form'	=> $form,
		));
	}
	
}
