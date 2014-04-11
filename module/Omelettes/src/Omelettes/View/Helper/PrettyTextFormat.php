<?php

namespace Omelettes\View\Helper;

class PrettyTextFormat extends AbstractPrettifier
{
	public function __invoke($text, $format = 'nl2br')
	{
		if (null === $text || $text === '') {
			return self::EMPTY_TEXT;
		}
		
		$output = '';
		switch ($format) {
			case 'nl2br':
				$htmlEscaper = $this->view->plugin('escapeHtml');
				$output = $htmlEscaper($text);
				$output = nl2br($output);
				break;
			default:
				echo 'Unrecognised format: ' . $format;
		}
		
		return $output;
	}
	
}
