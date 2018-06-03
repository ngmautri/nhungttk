<?php

namespace Application\Controller;

use Application\Entity\NmtApplicationPmtMethod;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PmtMethodController extends AbstractActionController {

	protected $doctrineEM;
	
	public function addAction() {
		$request = $this->getRequest ();
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		$u = $this->doctrineEM->find ( 'Application\Entity\MlaUsers', $user ['id'] );
			
		if ($request->isPost ()) {
			
			// $input->status = $request->getPost ( 'status' );
			// $input->remarks = $request->getPost ( 'description' );
			
			$pmt_method_code= $request->getPost ( 'pmt_method_code' );
			$pmt_method_name= $request->getPost ( 'pmt_method_name' );
			$description= $request->getPost ( 'description' );
			$status= $request->getPost ( 'status' );

			$errors = array ();
			
			if ($pmt_method_code=== '' or $pmt_method_code=== null) {
				$errors [] = 'Please give the name';
			}
			
			$r = $this->doctrineEM->getRepository ( 'Application\Entity\NmtApplicationPmtMethod' )->findBy ( array (
					'methodCode' => $pmt_method_code
			) );
			
			if (count($r)>=1) {
				$errors [] = $pmt_method_code. ' exists already';
			}
			
			
			if (count ( $errors ) > 0) {
				return new ViewModel ( array (
						'errors' => $errors,
				) );
			}
			
			// No Error
		
			$entity = new NmtApplicationPmtMethod();
			$entity->setMethodName($pmt_method_name);
			$entity->setMethodCode($pmt_method_code);
			$entity->setDescription($description);
			$entity->setStatus( $status);
			$entity->setCreatedOn( new \DateTime() );
			$entity->setCreatedBy( $u );
			
			$this->doctrineEM->persist ( $entity );
			$this->doctrineEM->flush ();
			}
		
		/*
		 * if ($request->isXmlHttpRequest ()) {
		 * $this->layout ( "layout/inventory/ajax" );
		 * }
		 */
		return new ViewModel ( array (
				'errors' => null,
		) );
	}
	
	/**
	 * 
	 *  @return \Zend\View\Model\ViewModel
	 */
	public function listAction() {
		$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtApplicationPmtMethod' )->findAll();
		$total_records= count($list);
		//$jsTree = $this->tree;
		return new ViewModel ( array (
				'list' => $list,
				'total_records'=>$total_records,
				'paginator'=>null,
		) );
	}
	
	
	/**
	 * 
	 *  @return \Zend\Http\Response|\Zend\View\Model\ViewModel
	 */
	public function list1Action() {
		
		$request = $this->getRequest ();
		
		// accepted only ajax request
		if (!$request->isXmlHttpRequest ()) {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
		$this->layout ( "layout/user/ajax" );
		
		$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtApplicationPmtMethod' )->findAll();
		$total_records= count($list);
		//$jsTree = $this->tree;
		return new ViewModel ( array (
				'list' => $list,
				'total_records'=>$total_records,
				'paginator'=>null,
		) );
	}
	
		public function getDoctrineEM() {
		return $this->doctrineEM;
	}
	public function setDoctrineEM(EntityManager $doctrineEM) {
		$this->doctrineEM = $doctrineEM;
		return $this;
	}
	
}
