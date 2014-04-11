<?php

namespace Omelettes\Controller;

use Omelettes\Logger;
use Zend\Console\Request as ConsoleRequest;

class ConsoleAssetsController extends AbstractController
{
	/**
	 * @var Logger
	 */
	protected $logger;
	
	public function getLogger()
	{
		if (!$this->logger) {
			$logger = $this->getServiceLocator()->get('Logger');
			$this->logger = $logger;
		}
	
		return $this->logger;
	}
	
	/**
	 * Compiles LESS to CSS
	 */
	protected function buildCss()
	{
		require_once __DIR__ . "/../../../../../vendor/oyejorge/less.php/lessc.inc.php";
		
		$lessPath = __DIR__ . "/../../../../../public/less/tactilecrm.less";
		$cssPath = __DIR__ . "/../../../../../public/css/tactilecrm.css";
		
		$this->getLogger()->debug('Compiling CSS from LESS...', array('tag' => 'console'));
		$less = new \Less_Parser();
		$less->parseFile($lessPath);
		file_put_contents($cssPath, $less->getCss());
		$this->getLogger()->debug('CSS Compiled', array('tag' => 'console'));
	}
	
	public function buildAction()
	{
		$this->getLogger()->info('Build Action', array('tag' => 'console'));
		
		switch ($this->params('assets')) {
			case 'css':
				$this->buildCss();
				break;
			default:
				throw new \Exception('Unexpected asset type: ' . $this->params('assets'));
		}
	}
	
}
