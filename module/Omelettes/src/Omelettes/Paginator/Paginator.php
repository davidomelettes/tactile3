<?php

namespace Omelettes\Paginator;

use Zend\Paginator\Paginator as ZendPaginator;

class Paginator extends ZendPaginator implements \JsonSerializable
{
	public function jsonSerialize()
	{
		$pages = $this->getPages();
		
		$json = array(
			'totalItemCount'		=> $this->getTotalItemCount(),
			'currentItemCount'		=> $this->getCurrentItemCount(),
			'itemCountPerPage'		=> $this->getItemCountPerPage(),
			'pages'					=> array(
				'first'					=> isset($pages->first) ? $pages->first : null,
				'prev'					=> isset($pages->previous) ? $pages->previous : null,
				'current'				=> $this->getCurrentPageNumber(),
				'next'					=> isset($pages->next) ? $pages->next : null,
				'last'					=> isset($pages->last) ? $pages->last : null,
			),
			'items'					=> array(),
		);
		
		foreach ($this as $item)
		{
			$json['items'][] = $item->jsonSerialize();
		}
		
		return $json;
	}
	
}
