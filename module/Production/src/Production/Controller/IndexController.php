<?php

namespace Production\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class IndexController extends AbstractActionController {
	
	protected $doctrineEM;
	
	
	
	/**
	 * 
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function getDoctrineEM() {
		return $this->doctrineEM;
	}
	
	/**
	 * 
	 * @param EntityManager $doctrineEM
	 * @return \PM\Controller\IndexController
	 */
	public function setDoctrineEM(EntityManager $doctrineEM) {
		$this->doctrineEM = $doctrineEM;
		return $this;
	}
	
	
}
