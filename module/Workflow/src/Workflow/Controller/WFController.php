<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Workflow\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Workflow\Model\NmtWfWorkflowTable;
use Workflow\Model\NmtWfWorkflow;
use Doctrine\ORM\EntityManager;

/*
 * Control Panel Controller
 */
class WFController extends AbstractActionController {
	
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
	
	
	
	/*
	 * Defaul Action
	 */
	public function addAction()
	{
		$request = $this->getRequest ();
		if ($request->isPost ()) {
			$em = $this->doctrineEM;
			$input = new \Application\Entity\NmtWfWorkflow();
			$input->setWorkflowName($request->getPost ( 'workflow_name' ));
			$input->setWorkflowDescription($request->getPost ( 'workflow_description' ));
			
			
			$u = $this->doctrineEM->find ( 'Application\Entity\MlaUsers', 39 );
			$input->setWorkflowCreatedBy( $u );
			$input->setWorkflowCreatedOn( new \DateTime () );
			
			$em->persist ( $input);
			$em->flush ();
			
		}
		
		//$this->redirect ()->toUrl ( 'home' );
		
	}
	
	/*
	 * Defaul Action
	 */
	public function listAction()
	{
	}
	
	public function deleteAction()
	{
		$this->nmtWfWorkflowTable->fetchAll();
		var_dump($this->nmtWfWorkflowTable->fetchAll());
	}
	
		public function getDoctrineEM() {
		return $this->doctrineEM;
	}
	public function setDoctrineEM(EntityManager $doctrineEM) {
		$this->doctrineEM = $doctrineEM;
		return $this;
	}
	
	
}
