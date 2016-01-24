<?php

namespace User\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/*
 *
 */
class NMTPlugin extends AbstractPlugin {
	public function test() {
		var_dump ( "NMT plugin test" );
	}
}
