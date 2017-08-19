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
use Zend\Http\Headers;
use Zend\Validator\Date;
use Zend\Math\Rand;
use Application\Entity\NmtHrLeaveReason;

/**
 *
 * @author nmt
 *        
 */
class FingerscanController extends AbstractActionController
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
    public function addAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            
            $errors = array();
            // $redirectUrl = $request->getPost('redirectUrl');
            
            $leaveReason = $request->getPost('leaveReason');
            $leaveReasonLocal = $request->getPost('leaveReasonLocal');
            $legalReference = $request->getPost('legalReference');
            $description = $request->getPost('description');
            // $condition = $request->getPost('condition');
            $isActive = (int) $request->getPost('isActive');
            
            if($isActive!==1){
                $isActive =0;
            }
            
            if ($leaveReason == null) {
                $errors[] = 'Please enter leave reason!';
            }
            
            if ($leaveReasonLocal == null) {
                $errors[] = 'Please enter leave reason in local language!';
            }
            
            $entity = new NmtHrLeaveReason();
            $entity->setLeaveReasonLocal($leaveReasonLocal);
            $entity->setLeaveReason($leaveReason);
            $entity->setLegalReference($legalReference);
            $entity->setDescription($description);
            // $entity->setCondition($condition);
            $entity->setIsActive($isActive);
            
            if (count($errors) > 0) {
                
                return new ViewModel(array(
                    'redirectUrl' => null,
                    'errors' => $errors,
                    'entity' => $entity
                ));
            }
            
            // NO ERROR
            
            $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
            
            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                "email" => $this->identity()
            ));
            
            $entity->setCreatedBy($u);
            $entity->setCreatedOn(new \DateTime());
            
            $redirectUrl = "/hr/leave-reason/list";
            
            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();
            $this->flashMessenger()->addMessage("Leave Reason '" . $entity->getLeaveReason() . "' has been created!");
            return $this->redirect()->toUrl($redirectUrl);
        }
        
        // NO POST
        $redirectUrl = null;
        $entity = new NmtHrLeaveReason();
        $entity->setIsActive(1);
        
        return new ViewModel(array(
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity
        ));
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
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $entity_id,
            'token' => $token
        );
        
        /**@var \Application\Entity\NmtHrLeaveReason $entity ; */
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtHrLeaveReason')->findOneBy($criteria);
        
        if (! $entity == null) {
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'errors' => null,
                'entity' => $entity
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            
            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                "email" => $this->identity()
            ));
            
            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');
            $entity_id = (int) $request->getPost('entity_id');
            $token = $request->getPost('token');
            
            $criteria = array(
                'id' => $entity_id,
                'token' => $token
            );
            
            /**@var \Application\Entity\NmtHrLeaveReason $entity ; */
            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtHrLeaveReason')->findOneBy($criteria);
            
            if ($entity == null) {
                
                $errors[] = 'Entity object can\'t be empty!';
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'target' => null,
                    'entity' => null
                ));
                
                // might need redirect
            } else {
                
                $errors = array();
                // $redirectUrl = $request->getPost('redirectUrl');
                
                $leaveReason = $request->getPost('leaveReason');
                $leaveReasonLocal = $request->getPost('leaveReasonLocal');
                $legalReference = $request->getPost('legalReference');
                $description = $request->getPost('description');
                // $condition = $request->getPost('condition');
                $isActive = (int) $request->getPost('isActive');
                
                if($isActive!==1){
                    $isActive =0;
                }
                
                if ($leaveReason == null) {
                    $errors[] = 'Please enter leave reason!';
                }
                
                if ($leaveReasonLocal == null) {
                    $errors[] = 'Please enter leave reason in local language!';
                }
                
                if($isActive!==1){
                    $isActive =0;
                }
                
                $entity->setLeaveReasonLocal($leaveReasonLocal);
                $entity->setLeaveReason($leaveReason);
                $entity->setLegalReference($legalReference);
                $entity->setDescription($description);
                // $entity->setCondition($condition);
                $entity->setIsActive($isActive);
                
                if (count($errors) > 0) {
                    
                    return new ViewModel(array(
                        'redirectUrl' => null,
                        'errors' => $errors,
                        'entity' => $entity
                    ));
                }
                
                // NO ERROR
                
                $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
                
                $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                    "email" => $this->identity()
                ));
                
                $entity->setLastchangeBy($u);
                $entity->setLastchangeOn(new \DateTime());
                
                $redirectUrl = "/hr/leave-reason/list";
                
                $this->doctrineEM->persist($entity);
                $this->doctrineEM->flush();
                $this->flashMessenger()->addMessage("Leave Reason '" . $entity->getLeaveReason() . "' has been updated!");
                return $this->redirect()->toUrl($redirectUrl);
                
            }
        }
        
        $redirectUrl = null;
        
        if ($request->getHeader('Referer') == null) {
            // return $this->redirect ()->toRoute ( 'access_denied' );
        } else {
            $redirectUrl = $request->getHeader('Referer')->getUri();
        }
            
        $entity_id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $entity_id,
            'token' => $token
        );
        
        /**@var \Application\Entity\NmtHrLeaveReason $entity ; */
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtHrLeaveReason')->findOneBy($criteria);
        
        if (! $entity == null) {
            
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'errors' => null,
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
    {
        $criteria = array();
        
        // var_dump($criteria);
        
        $sort_criteria = array();
        
        if (is_null($this->params()->fromQuery('perPage'))) {
            $resultsPerPage = 15;
        } else {
            $resultsPerPage = $this->params()->fromQuery('perPage');
        }
        ;
        
        if (is_null($this->params()->fromQuery('page'))) {
            $page = 1;
        } else {
            $page = $this->params()->fromQuery('page');
        }
        ;
        
        /**@var \Application\Repository\NmtHrFingerscanRepository $res ;  */
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtHrFingerscan');
        $list = $res->getFingerscan(null, null, 7, 2017);
        //var_dump($list);
        $total_records = count($list);
        $paginator = null;
        
        /* if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $list = $this->doctrineEM->getRepository('Application\Entity\NmtHrLeaveReason')->findBy($criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
        } */
        
        // $all = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItem' )->getAllItem();
        // var_dump (count($all));
        
        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator
        ));
    }

    /**
     * Return attachment of a target
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function list1Action()
    {
        $request = $this->getRequest();
        
        // accepted only ajax request
        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }
        ;
        
        $this->layout("layout/user/ajax");
        
        $target_id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $target_id,
            'token' => $token
        );
        
        /**
         *
         * @todo : Change Target
         */
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtHrEmployee')->findOneBy($criteria);
        
        if ($target !== null) {
            
            /**
             *
             * @todo : Change Target
             */
            $criteria = array(
                'employee' => $target_id,
                'isActive' => 1,
                'markedForDeletion' => 0
            );
            
            $list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAttachment')->findBy($criteria);
            $total_records = count($list);
            $paginator = null;
            
            return new ViewModel(array(
                'list' => $list,
                'total_records' => $total_records,
                'paginator' => $paginator,
                'target' => $target
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
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }
}
