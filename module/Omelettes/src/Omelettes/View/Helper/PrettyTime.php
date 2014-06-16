<?php

namespace Omelettes\View\Helper;

class PrettyTime extends AbstractPrettifier
{
	public function __invoke($iso8601)
	{
		$now = time();
		$then = strtotime($iso8601);
		if (false === $then) {
			return 'never';
		}
		$diff = $then - $now;
		
		if ($diff < 0) {
			// Past
			$diff = abs($diff);
			switch (1) {
				case $diff < 30:
					// 30 seconds ago
					$string = 'just now';
					break;
				case $diff < 3600:
					// 1 hour ago 
					$minutes = (int)ceil($diff / 60);
					$string = sprintf('%d minute%s ago', $minutes, ($minutes === 1 ? '' : 's'));
					break;
				case $diff < 86400:
					// 1 day ago
					$hours = (int)ceil($diff / 3600);
					$string = sprintf('%d hour%s ago', $hours, $hours === 1 ? '' : 's');
					break;
				case $diff < 172800:
					// Yesterday
					$string = sprintf('Yesterday at %s', date('H:i', $then));
					break;
				case date('Y', $now) === date('Y', $then):
					// This year
					$string = date('d F', $then);
					break;
				default:
					// Not this year
					$string = date('d F Y', $then);
					break;
			}
		} else {
			// Present/Future
			switch (1) {
				case $diff < 10:
					$string = 'just now';
					break;
				default:
					$string = date('Y-m-d', $then);
					break;
			}
		}
		
		return sprintf('<span title="%s">%s</span>', date('l, d F Y \a\t H:i', $then), $string);
	}
	
}
