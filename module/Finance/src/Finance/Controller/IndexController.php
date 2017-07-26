<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Finance\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;

/*
 * Control Panel Controller
 */
class IndexController extends AbstractActionController {
	
	protected $doctrineEM;
	
	/*
	 * Defaul Action
	 */
	public function indexAction()
	{
		$em = $this->doctrineEM;
		$data = $em->getRepository('Application\Entity\NmtWfWorkflow')->findAll();
		foreach($data as $row)
		{
			echo $row->getWorkflowName();
			echo '<br />';
		}
	}
	
	
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
