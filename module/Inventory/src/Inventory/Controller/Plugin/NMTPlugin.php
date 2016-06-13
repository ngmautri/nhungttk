<?php

namespace User\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Session\Container;

/*
 *
 */
class NMTPlugin extends AbstractPlugin {
	
	public function test() {
		
		$session = new Container ( 'MLA_USER' );
		$hasUser = $session->offsetExists ( 'user' );
		if ($hasUser) {
			$user = $session->offsetGet ( 'user' );
			return $user;
		}
		return null;
	}
}
