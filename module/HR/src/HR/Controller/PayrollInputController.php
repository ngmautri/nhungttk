<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace HR\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use Zend\View\Model\ViewModel;
use MLA\Paginator;
use Application\Entity\NmtApplicationAttachment;
use Zend\Http\Headers;
use Zend\Validator\Date;
use Zend\Math\Rand;
use Application\Entity\NmtHrPayrollInput;

/**
 *
 * @author nmt
 *        
 */
class PayrollInputController extends AbstractActionController
{

    const CHAR_LIST = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";

    protected $doctrineEM;

    /*
     * Defaul Action
     */
    public function indexAction()
    {}

    /**
     * 
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function  reviseAction()
    {
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            
            $errors = array();
            
            $target_id = $request->getPost('target_id');
            $token = $request->getPost('token');
            $period_id = $request->getPost('period_id');
            $period_token = $request->getPost('period_token');
            $redirectUrl = $request->getPost('redirectUrl');
            
            $criteria = array(
                'id' => $target_id,
                'token' => $token
            );
            
            /**@var \Application\Entity\NmtHrEmployee $target ; */
            $target = $this->doctrineEM->getRepository('Application\Entity\NmtHrEmployee')->findOneBy($criteria);
            
            $criteria = array(
                'id' => $period_id,
                'token' => $period_token
            );
            /**@var \Application\Entity\NmtFinPostingPeriod $period ; */
            $period = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod')->findOneBy($criteria);
            
            $validated_criteria = 0;
            if($target==null){
                $errors[]="Employee Object is not valid";
            }else{
                $validated_criteria++;
            }
            
            if($period==null){
                $errors[]="Period Object is not valid";
            }else{
                $validated_criteria++;
            }
            
            if($validated_criteria<2){
                
                $this->flashMessenger()->addMessage('Something wrong!');
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'target' => $target,
                    'entity' => null,
                    'period' => $period,
                ));
            }
            
            // ************** Both employee and period are valid *********
            
            $annualLeave = $request->getPost('$annualLeave');
            $approvedLeave = $request->getPost('$approvedLeave');
            $maternityLeave = $request->getPost('$maternityLeave');
            $otherLeave1 = $request->getPost('$otherLeave1');
            $otherLeave2 = $request->getPost('$otherLeave2');
           
            $otherLeave3 = $request->getPost('$$otherLeave3');
            $approvedLeave = $request->getPost('$approvedLeave');
            $outOfOfficeDay = $request->getPost('$$outOfOfficeDay');
            $personalPaidLeave = $request->getPost('$$personalPaidLeave');
            $presentDay = $request->getPost('$$presentDay');
            
            $sickLeave = $request->getPost('sickLeave');
            $unapprovedLeave = $request->getPost('$$unapprovedLeave');
            
            $entity = new NmtHrPayrollInput();
            $entity->setAnnualLeave($annualLeave);
            $entity->setApprovedLeave($approvedLeave);
            $entity->setCurrentState("ENABLED");
            $entity->setEmployee($target);
            $entity->setEmployeeName($target->getEmployeeName());
            $entity->setMaternityLeave($maternityLeave);
            $entity->setOtherLeave1($otherLeave1);
            $entity->setOtherLeave2($otherLeave2);
            
            $entity->setOtherLeave3($otherLeave3);
            $entity->setOutOfOfficeDay($outOfOfficeDay);
            $entity->setPeriod($period);
            $entity->setPeriodName($period->getPeriodName());
            $entity->setPersonalPaidLeave($personalPaidLeave);
            $entity->setPresentDay($presentDay);
            $entity->setSickLeave($sickLeave);
            $entity->setUnapprovedLeave($unapprovedLeave);
            
            if (count($errors) > 0) {
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'target' => $target,
                    'entity' => $entity,
                    'period' => $period,
                ));
            }
            
            //*********** NO ERRORS **********//
           
            // get Last Revision
            
            $LastRevisionNo=0;
            $entity->setRevisionNumber($LastRevisionNo+1);
            
            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                "email" => $this->identity()
            ));
            
            $entity->setCreatedBy($u);
            $entity->setCreatedOn(new \DateTime());
            $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
            
            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();
            
            $this->flashMessenger()->addMessage("Revision is created successfully!");
            
            $redirectUrl = "/hr/employee/show?token=" . $target->getToken() . "&entity_id=" . $target->getId();
            return $this->redirect()->toUrl($redirectUrl);
            
        }    
        
        // ******************* NO POST **********************/
        $redirectUrl = null;
        $target_id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');
        $period_id = (int) $this->params()->fromQuery('period_id');
        $period_token = $this->params()->fromQuery('period_token');
        
        $criteria = array(
            'id' => $target_id,
            'token' => $token
        );
        
        /**@var \Application\Entity\NmtHrEmployee $target ; */
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtHrEmployee')->findOneBy($criteria);
        
        
        $criteria = array(
            'id' => $period_id,
            'token' => $period_token
        );
        /**@var \Application\Entity\NmtFinPostingPeriod $period ; */
        $period = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod')->findOneBy($criteria);
        
        /**@var \Application\Repository\NmtHrPayrollInputRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtHrPayrollInput');
        $lastRevision = $res->getLastRevisionInput($target_id,$period_id);
        
      
        if ($target !== null && $period!==null) {
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'errors' => null,
                'target' => $target,
                'entity' => null,
                'period' => $period,
                'lastRevision' => $lastRevision,
                
            ));
        }
        
        $this->flashMessenger()->addMessage('Something wrong!');
        return $this->redirect()->toRoute('access_denied');
    }
    
    /**
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function consolidateAction()
    {
        $redirectUrl = null;
        $target = null;
        $entity = null;
        
        $id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $id,
            'token' => $token
        );
        
        // Target: Employee
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtHrEmployee')->findOneBy($criteria);
        
        if ($target !== null) {
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'errors' => null,
                'target' => $target,
                'entity' => $entity
            ));
        }
        return $this->redirect()->toRoute('access_denied');
    }
    

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function showAction()
    {
        $request = $this->getRequest();
        $redirectUrl = null;
        
        if ($request->getHeader('Referer') == null) {
            // return $this->redirect ()->toRoute ( 'access_denied' );
        } else {
            $redirectUrl = $request->getHeader('Referer')->getUri();
        }
        
        $entity_id = (int) $this->params()->fromQuery('entity_id');
        $checksum = $this->params()->fromQuery('checksum');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $entity_id,
            'checksum' => $checksum,
            'token' => $token
        );
        
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAttachment')->findOneBy($criteria);
        
        if (! $entity == null) {
            
            /**
             *
             * @todo Update Target
             */
            $target = $entity->getEmployee();
            
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'errors' => null,
                'target' => $target,
                'entity' => $entity
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }
    
    

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function listAction()
    {}

    /**
     * Return attachment of a target
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function list1Action()
    {
        $request = $this->getRequest();
        $period_id = $this->params ()->fromQuery ( 'period_id' );
        
        
        // Jquery
        $context ="J";
        
        
        // accepted only ajax request
       /*  if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }
        ; */
        
        $this->layout("layout/user/ajax");
        
        $target_id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $target_id,
            'token' => $token,
        );
        
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtHrEmployee')->findOneBy($criteria);
    
        
        
        
        if ($target !== null) {
            
            $criteria = array(
                'employee' => $target_id,
                'period' =>$period_id,
            );
            
            /**@var \Application\Entity\NmtHrPayrollInput */
            $list = $this->doctrineEM->getRepository('Application\Entity\NmtHrPayrollInput')->findBy($criteria);
            $total_records = count($list);
            
            $criteria = array(
                'id' => $period_id,
            );
            
            /**@var \Application\Entity\NmtFinPostingPeriod $period*/
            $period = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod')->findOneBy($criteria);
            
            /**@var \Application\Entity\NmtFinPostingPeriod */
            $periods = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod')->findBy(array());
            
            /**@var \Application\Repository\NmtHrPayrollInputRepository $res ;*/
            $res = $this->doctrineEM->getRepository('Application\Entity\NmtHrPayrollInput');
            $lastRevision = $res->getLastRevisionInput($target_id,$period_id);
            
            
            return new ViewModel(array(
                'list' => $list,
                'total_records' => $total_records,
                'target' => $target,
                'periods'=>$periods,
                'period'=>$period,                
                'context' => $context,
                'lastRevision' => $lastRevision,
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function updateTokenAction()
    {
        
        /**
         *
         * @todo : update target
         */
        $query = 'SELECT e FROM Application\Entity\NmtApplicationAttachment e WHERE e.employee > :n';
        
        $list = $this->doctrineEM->createQuery($query)
            ->setParameter('n', 0)
            ->getResult();
        
        if (count($list) > 0) {
            foreach ($list as $entity) {
                /**
                 *
                 * @todo Update Targnet
                 */
                $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
            }
        }
        
        $this->doctrineEM->flush();
        
        $total_records = count($list);
        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records
        ));
    }
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	
	/**
	 *
	 * @return \Zend\Stdlib\ResponseInterface
	 */
	public function getDoctrineEM() {
		return $this->doctrineEM;
	}
	public function setDoctrineEM(EntityManager $doctrineEM) {
		$this->doctrineEM = $doctrineEM;
		return $this;
	}
}
