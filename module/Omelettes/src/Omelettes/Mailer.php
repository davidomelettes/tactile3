<?php

namespace Omelettes;

use Zend\Mail as ZendMail,
	Zend\Mime\Message as MimeMessage,
	Zend\Mime\Mime as MimeType,
	Zend\Mime\Part as MimePart,
	Zend\ServiceManager\ServiceLocatorAwareInterface,
	Zend\ServiceManager\ServiceLocatorInterface,
	Zend\View\Model\ViewModel,
	Zend\View\Renderer\PhpRenderer;

class Mailer implements ServiceLocatorAwareInterface
{
	/**
	 * @var ZendMail\Message
	 */
	protected $lastMessage;
	
	/**
	 * @var PhpRenderer
	 */
	protected $view;
	
	/**
	 * @var ViewModel
	 */
	protected $htmlLayoutView;

	/**
	 * @var ViewModel
	 */
	protected $htmlTemplateView;
	
	/**
	 * @var ViewModel
	 */
	protected $textLayoutView;
	
	/**
	 * @var ViewModel
	 */
	protected $textTemplateView;
	
	/**
	 * @var ServiceLocatorInterface
	 */
	protected $serviceLocator;
	
	/**
	 * Message character encoding
	 * @var string
	 */
	protected $encoding = 'UTF-8';
	
	/**
	 * Email address the message is sent from
	 * @var string
	 */
	protected $fromAddress;
	
	/**
	 * Friendly name for labelling the from address
	 * @var string
	 */
	protected $fromName;
	
	public function setEncoding($encoding)
	{
		$this->encoding = $encoding;
		
		return $this;
	}
	
	public function setFromAddress($address)
	{
		$this->fromAddress = $address;
		
		return $this;
	}
	
	public function setFromName($name)
	{
		$this->fromName = $name;
		
		return $this;
	}
	
	/**
	 * Set the service locator
	 *
	 * @param  ServiceLocatorInterface $serviceLocator
	 */
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
	{
		$this->serviceLocator = $serviceLocator;
	}
	
	/**
	 * Get the service locator.
	 *
	 * @return ServiceLocatorInterface
	 */
	public function getServiceLocator()
	{
		return $this->serviceLocator;
	}
	
	public function getView()
	{
		if (!$this->view) {
			$view = $this->getServiceLocator()->get('Zend\View\Renderer\RendererInterface');
			$this->view = $view;
		}
		
		return $this->view;
	}
	
	public function setHtmlLayout($layoutTemplate, array $variables = array())
	{
		$layoutModel = new ViewModel();
		$layoutModel->setTemplate($layoutTemplate)
			->setVariables($variables);
		$this->htmlLayoutView = $layoutModel;
		
		return $this;
	}
	
	public function getHtmlLayoutView()
	{
		return $this->htmlLayoutView;
	}
	
	public function setHtmlTemplate($htmlTemplate, array $variables = array())
	{
		$viewModel = new ViewModel();
		$viewModel->setTemplate($htmlTemplate)
			->setVariables($variables);
		$this->htmlTemplateView = $viewModel;
		
		return $this;
	}
	
	public function getHtmlTemplateView()
	{
		return $this->htmlTemplateView;
	}
	
	public function setTextLayout($layoutTemplate, array $variables = array())
	{
		$layoutModel = new ViewModel();
		$layoutModel->setTemplate($layoutTemplate)
			->setVariables($variables);
		$this->textLayoutView = $layoutModel;
	
		return $this;
	}
	
	public function getTextLayoutView()
	{
		return $this->textLayoutView;
	}
	
	public function setTextTemplate($textTemplate, array $variables = array())
	{
		$viewModel = new ViewModel();
		$viewModel->setTemplate($textTemplate)
			->setVariables($variables);
		$this->textTemplateView = $viewModel;
	
		return $this;
	}
	
	public function getTextTemplateView()
	{
		return $this->textTemplateView;
	}
	
	public function setLastMessage(ZendMail\Message $message)
	{
		$this->lastMessage = $message;
		
		return $this;
	}
	
	public function getLastMessage()
	{
		return $this->lastMessage;
	}
	
	public function send($subject, $to)
	{
		if (!$this->getTextTemplateView()) {
			throw new \Exception('Missing text template');
		}
		if (!$this->fromAddress) {
			throw new \Exception('Missing From address');
		}
		
		$message = new ZendMail\Message();
		$body = new MimeMessage();
		
		$textBody = $this->getView()->render($this->getTextTemplateView());
		if ($this->getTextLayoutView()) {
			$textBody = $this->getView()->render($this->getTextLayoutView()->setVariables(array('content' => $textBody)));
		}
		$textPart = new MimePart($textBody);
		$textPart->type = MimeType::TYPE_TEXT;
		$parts = array($textPart);
		
		if ($this->getHtmlTemplateView()) {
			$htmlBody = $this->getView()->render($this->getHtmlTemplateView());
			if ($this->getHtmlLayoutView()) {
				$htmlBody = $this->getView()->render($this->getHtmlLayoutView()->setVariables(array('content' => $htmlBody)));
			}
			$htmlPart = new MimePart($htmlBody);
			$htmlPart->type = MimeType::TYPE_HTML;
			$parts[] = $htmlPart;
		}
		$body->setParts($parts);
		
		$message->setSubject($subject)
			->setTo($to)
			->setFrom($this->fromAddress)
			->setEncoding($this->encoding)
			->setBody($body);
		
		$message->getHeaders()->get('content-type')->setType(MimeType::MULTIPART_ALTERNATIVE);
		
		$transport = new ZendMail\Transport\Sendmail();
		$transport->send($message);
		
		$this->setLastMessage($message);
	}
	
}
