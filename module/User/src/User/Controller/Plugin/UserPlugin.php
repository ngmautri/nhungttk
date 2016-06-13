<?php

namespace User\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Session\Container;

/*
 *
 */
class UserPlugin extends AbstractPlugin {
	
	public function getUser() {
		$session = new Container ( 'MLA_USER' );
		$hasUser = $session->offsetExists ( 'user' );
		if ($hasUser) {
			$user = $session->offsetGet ( 'user' );
			return $user;
		}
		return null;
	}
	
	public function getUserDepartment() {
		$session = new Container ( 'MLA_USER' );
		$hasUser = $session->offsetExists ( 'user_department' );
		if ($hasUser) {
			$user = $session->offsetGet ( 'user_department' );
			return $user;
		}
		return null;
	}
}
