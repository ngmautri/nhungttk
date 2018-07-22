<?php

namespace Inventory\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use Zend\View\Model\ViewModel;
use Application\Entity\NmtInventoryWarehouse;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */

class WarehouseController extends AbstractActionController {
	protected $doctrineEM;
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
	}
	
	/*
	 * Defaul Action
	 */
	public function addAction() {
		$identity = $this->identity();
		$u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array('email'=>$identity));
		
		$request = $this->getRequest ();
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		
		
		if ($request->isPost ()) {
			
			// $input->status = $request->getPost ( 'status' );
			// $input->remarks = $request->getPost ( 'description' );
			
			
			$company_id= $request->getPost ( 'company_id' );
			$wh_code= $request->getPost ( 'wh_code' );
			$is_locked= $request->getPost ( 'is_locked' );
			
			$is_default= $request->getPost ( 'is_default' );
			$wh_name= $request->getPost ( 'wh_name' );
			
			$wh_address= $request->getPost ( 'wh_address' );
			$country_id= $request->getPost ( 'country_id' );
			$wh_contract_person= $request->getPost ( 'wh_contact_person' );
			$status = $request->getPost ( 'wh_status' );
			
			$errors = array ();
			
			if ($wh_code=== '' or $wh_code=== null) {
				$errors [] = 'Please give code';
			}
			
			if ($wh_name=== '' or $wh_name=== null) {
				$errors [] = 'Please give the code';
			}
			
			$r = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryWarehouse' )->findBy ( array (
					'whCode' => $wh_code
			) );
			
			if (count ( $r ) >= 1) {
				$errors [] = $wh_code. ' exists';
			}
			
			if (count ( $errors ) > 0) {
				return new ViewModel ( array (
						'errors' => $errors
				) );
			}
			
			// No Error
			
			$entity = new NmtInventoryWarehouse();
			$entity->setWhCode( $wh_code);
			$entity->setWhName( $wh_name);
			$entity->setWhAddress( $wh_address);
			$company = $this->doctrineEM->find ( 'Application\Entity\NmtApplicationCompany', $company_id );
			
			$entity->setCompany( $company);
			$country = $this->doctrineEM->find('Application\Entity\NmtApplicationCountry',$country_id);
			$entity->setWhCountry($country);
			$entity->setWhContactPerson($wh_contract_person);
			
			$entity->setIsDefault( $is_default);
			$entity->setIsLocked( $is_locked);
			
			$entity->setCreatedOn ( new \DateTime () );
			$entity->setCreatedBy ( $u );
			$entity->setWhStatus( $status );
			$this->doctrineEM->persist ( $entity );
			$this->doctrineEM->flush ();
			
			$redirectUrl = $request->getPost ( 'redirectUrl' );
			$this->redirect ()->toUrl ( $redirectUrl );
			
		}
		
		/*
		 * if ($request->isXmlHttpRequest ()) {
		 * $this->layout ( "layout/inventory/ajax" );
		 * }
		 */
		$company_id = ( int ) $this->params ()->fromQuery ( 'company_id' );
		$company = $this->doctrineEM->find ( 'Application\Entity\NmtApplicationCompany', $company_id );
		$countries= $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCountry')->findAll();
		return new ViewModel ( array (
				'errors' => null,
				'identity'=>$u,
				'company'=>$company,
				'countries'=>$countries,
				'redirectUrl' => $redirectUrl
				
				
		) );
	}
	
	public function listAction() {
		$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryWarehouse' )->findBy(array(),array('whName'=>'ASC'));
		$total_records = count ( $list );
		return new ViewModel ( array (
				'list' => $list,
				'total_records' => $total_records,
				'paginator' => null
		) );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function list1Action() {
		
		$request = $this->getRequest ();
		
		// accepted only ajax request
		/* if (!$request->isXmlHttpRequest ()) {
			return $this->redirect ()->toRoute ( 'access_denied' );
		} */
		$this->layout ( "layout/user/ajax" );
		$target_id = $_GET['target_id'];
		$target_name = $_GET['target_name'];
		
		
		$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryWarehouse' )->findBy(array(),array('whName'=>'ASC'));
		$total_records = count ( $list );
		return new ViewModel ( array (
				'list' => $list,
				'total_records' => $total_records,
				'paginator' => null,
				'target_id' => $target_id,
				'target_name' => $target_name,
				
		) );
	}
	
	
	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function transferAction() {
		
		$request = $this->getRequest ();
		$redirectUrl = $request->getHeader ( 'Referer' )->getUri ();
		
		$item_id= ( int ) $this->params ()->fromQuery ( 'item_id' );
		
		$item = $this->doctrineEM->find('Application\Entity\NmtInventoryItem', $item_id);
		
		return new ViewModel ( array (
				"item"=>$item,
				"errors"=>null,
				'redirectUrl' => $redirectUrl,
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
