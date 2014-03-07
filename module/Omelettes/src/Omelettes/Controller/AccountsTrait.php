<?php

namespace Omelettes\Controller;

use Omelettes\Model;

trait AccountsTrait
{
	/**
	 * @var Model\AccountsMapper
	 */
	protected $accountsMapper;
	
	/**
	 * @var Model\AccountPlansMapper
	 */
	protected $accountPlansMapper;
	
	public function getAccountsMapper()
	{
		if (!$this->accountsMapper) {
			$accountsMapper = $this->getServiceLocator()->get('Omelettes\Model\AccountsMapper');
			$this->accountsMapper = $accountsMapper;
		}
		
		return $this->accountsMapper;
	}
	
	public function getAccountPlansMapper()
	{
		if (!$this->accountPlansMapper) {
			$accountPlansMapper = $this->getServiceLocator()->get('Omelettes\Model\AccountPlansMapper');
			$this->accountPlansMapper = $accountPlansMapper;
		}
	
		return $this->accountPlansMapper;
	}
	
}
