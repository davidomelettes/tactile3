<?php

namespace Omelettes\View\Helper;

class PrettyTime extends AbstractPrettifier
{
	public function __invoke($iso8601)
	{
		$now = time();
		$then = strtotime($iso8601);
		$diff = $then - $now;
		
		if ($diff < 0) {
			// Past
			$diff = abs($diff);
			switch (1) {
				case $diff < 30:
					return 'Just now';
				case $diff < 3600:
					$minutes = ceil($diff / 60);
					return sprintf('%d minute%s ago', $minutes, $minutes === 1 ? '' : 's');
				case $diff < 86400:
					$hours = ceil($diff / 3600);
					return sprintf('%d hours%s ago', $hours, $hours === 1 ? '' : 's');
				case $diff < 172800:
					return sprintf('Yesterday at %s', date('H:i', $then));
				default:
					return date('Y-m-d', $then);
			}
		} else {
			// Present/Future
			switch (1) {
				default:
					return date('Y-m-d', $then);
			}
		}
	}
	
}
