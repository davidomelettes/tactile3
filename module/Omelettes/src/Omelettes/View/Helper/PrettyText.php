<?php

namespace Omelettes\View\Helper;

class PrettyText extends AbstractPrettifier
{
	public function __invoke($text, $htmlEscape = true)
	{
		if (null === $text || $text === '') {
			return self::EMPTY_TEXT;
		}
		
		if ($htmlEscape) {
			$htmlEscaper = $this->view->plugin('escapeHtml');
			return $htmlEscaper($text);
		} else {
			return $text;
		}
	}
	
}
