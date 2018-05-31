<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Finance\Controller;

use Zend\Math\Rand;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Date;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityManager;
use MLA\Paginator;
use Application\Entity\NmtFinPostingPeriod;

/**
 *
 * @author nmt
 *        
 */
class PostingPeriodController extends AbstractActionController
{

    const CHAR_LIST = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";

    protected $doctrineEM;

    /*
     * Defaul Action
     */
    public function indexAction()
    {}

    public function addAction()
    {
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            
            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');
            
            $periodName = $request->getPost('periodName');
            $periodCode = $request->getPost('periodCode');
            $postingFromDate = $request->getPost('postingFromDate');
            $postingToDate = $request->getPost('postingToDate');
            
            $periodStatus = $request->getPost('periodStatus');
            
            $actualWorkdingDays = $request->getPost('actualWorkdingDays');
            $planWorkingDays = $request->getPost('planWorkingDays');
            $nationalHolidays = $request->getPost('nationalHolidays');
            $cooperateLeave = $request->getPost('cooperateLeave');
            
            $entity = new NmtFinPostingPeriod();
            
            $entity->setPeriodStatus($periodStatus);
            
            if ($periodName == null) {
                $errors[] = 'Please enter Period Name!';
            } else {
                $entity->setPeriodName($periodName);
            }
            
            if ($periodCode == null) {
                $errors[] = 'Please enter Period Code!';
            } else {
                $entity->setPeriodCode($periodCode);
            }
            
            if ($planWorkingDays == null) {
                $errors[] = 'Please  enter Plan Working Days!';
            } else {
                
                if (! is_numeric($planWorkingDays)) {
                    $errors[] = 'Plan Working Days must be a number.';
                } else {
                    if ($planWorkingDays <= 0) {
                        $errors[] = 'Plan Working Days must be greater than 0!';
                    }
                    $entity->setPlanWorkingDays($planWorkingDays);
                }
            }
            
            if ($actualWorkdingDays == null) {
                $errors[] = 'Please  enter $actualWorkdingDays!';
            } else {
                
                if (! is_numeric($actualWorkdingDays)) {
                    $errors[] = '$actualWorkdingDaysmust be a number.';
                } else {
                    if ($actualWorkdingDays <= 0) {
                        $errors[] = '$actualWorkdingDays must be greater than 0!';
                    }
                    $entity->setActualWorkdingDays($actualWorkdingDays);
                }
            }
            
            if ($nationalHolidays == null) {
                // $errors [] = 'Please enter $actualWorkdingDays!';
            } else {
                
                if (! is_numeric($nationalHolidays)) {
                    $errors[] = 'nationalHolidays be a number.';
                } else {
                    if ($nationalHolidays <= 0) {
                        $errors[] = 'nationalHolidays must be greater than 0!';
                    }
                    $entity->setNationalHolidays($nationalHolidays);
                }
            }
            
            if ($cooperateLeave == null) {
                // $errors [] = 'Please enter $actualWorkdingDays!';
            } else {
                
                if (! is_numeric($cooperateLeave)) {
                    $errors[] = 'CooperateLeave be a number.';
                } else {
                    if ($cooperateLeave <= 0) {
                        $errors[] = 'CooperateLeave must be greater than 0!';
                    }
                    $entity->setCooperateLeave($cooperateLeave);
                }
            }
            
            $validator = new Date();
            
            if (! $validator->isValid($postingFromDate)) {
                $errors[] = 'Start Date is not correct or empty!';
            } else {
                $entity->setPostingFromDate(new \DateTime($postingFromDate));
            }
            if (! $validator->isValid($postingToDate)) {
                $errors[] = 'End Date is not correct or empty!';
            } else {
                $entity->setPostingToDate(new \DateTime($postingToDate));
            }
            
            if (count($errors) > 0) {
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity
                ));
            }
            
            // NO ERROR
            //=====================
            
            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                "email" => $this->identity()
            ));
            
            $entity->setCreatedBy($u);
            $entity->setCreatedOn(new \DateTime());
            $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
            
            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();
            
            $this->flashMessenger()->addMessage('Posting Period "' . $periodName . '" is created successfully!');
            
            // $redirectUrl = "/procure/pr/show?token=" . $entity->getToken() . "&entity_id=" . $entity->getId() . "&checksum=" . $entity->getChecksum();
            return $this->redirect()->toUrl($redirectUrl);
        }
        
        // NOT POST
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();
        
        return new ViewModel(array(
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => null
        ));
    }

    /**
     */
    public function showAction()
    {
        $request = $this->getRequest();
        
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();
        
        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $id,
            'token' => $token
        );
        
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod')->findOneBy($criteria);
        if ($entity !== null) {
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'entity' => $entity,
                'errors' => null
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function editAction()
    {
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            
            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');
            
            $entity_id = $request->getPost('entity_id');
            $token = $request->getPost('token');
            
            $criteria = array(
                'id' => $entity_id,
                'token' => $token
            );
            
            /**@var \Application\Entity\NmtFinPostingPeriod $entity*/
            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod')->findOneBy($criteria);
            
            if ($entity == null) {
                
                $errors[] = 'Entity object can\'t be empty. Or token key is not valid!';
                $this->flashMessenger()->addMessage('Something wrong!');
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => null
                ));
                
                // might need redirect
            } else {
                
                $periodName = $request->getPost('periodName');
                $periodCode = $request->getPost('periodCode');
                $postingFromDate = $request->getPost('postingFromDate');
                $postingToDate = $request->getPost('postingToDate');
                
                $periodStatus = $request->getPost('periodStatus');
                
                $actualWorkdingDays = $request->getPost('actualWorkdingDays');
                $planWorkingDays = $request->getPost('planWorkingDays');
                $nationalHolidays = $request->getPost('nationalHolidays');
                $cooperateLeave = $request->getPost('cooperateLeave');
                
                $entity->setPeriodStatus($periodStatus);
                
                if ($periodName == null) {
                    $errors[] = 'Please enter Period Name!';
                } else {
                    $entity->setPeriodName($periodName);
                }
                
                if ($periodCode == null) {
                    $errors[] = 'Please enter Period Code!';
                } else {
                    $entity->setPeriodCode($periodCode);
                }
                
                if ($planWorkingDays == null) {
                    $errors[] = 'Please  enter Plan Working Days!';
                } else {
                    
                    if (! is_numeric($planWorkingDays)) {
                        $errors[] = 'Plan Working Days must be a number.';
                    } else {
                        if ($planWorkingDays <= 0) {
                            $errors[] = 'Plan Working Days must be greater than 0!';
                        }
                        $entity->setPlanWorkingDays($planWorkingDays);
                    }
                }
                
                if ($actualWorkdingDays == null) {
                    $errors[] = 'Please  enter $actualWorkdingDays!';
                } else {
                    
                    if (! is_numeric($actualWorkdingDays)) {
                        $errors[] = '$actualWorkdingDaysmust be a number.';
                    } else {
                        if ($actualWorkdingDays <= 0) {
                            $errors[] = '$actualWorkdingDays must be greater than 0!';
                        }
                        $entity->setActualWorkdingDays($actualWorkdingDays);
                    }
                }
                
                if ($nationalHolidays == null) {
                    // $errors [] = 'Please enter $actualWorkdingDays!';
                } else {
                    
                    if (! is_numeric($nationalHolidays)) {
                        $errors[] = '$nationalHolidays be a number.';
                    } else {
                        if ($nationalHolidays <= 0) {
                            $errors[] = '$$nationalHolidays must be greater than 0!';
                        }
                        $entity->setNationalHolidays($nationalHolidays);
                    }
                }
                
                if ($cooperateLeave == null) {
                    // $errors [] = 'Please enter $actualWorkdingDays!';
                } else {
                    
                    if (! is_numeric($cooperateLeave)) {
                        $errors[] = '$cooperateLeave be a number.';
                    } else {
                        if ($cooperateLeave <= 0) {
                            $errors[] = '$cooperateLeave must be greater than 0!';
                        }
                        $entity->setCooperateLeave($cooperateLeave);
                    }
                }
                
                $validator = new Date();
                
                if (! $validator->isValid($postingFromDate)) {
                    $errors[] = 'Start Date is not correct or empty!';
                } else {
                    $entity->setPostingFromDate(new \DateTime($postingFromDate));
                }
                if (! $validator->isValid($postingToDate)) {
                    $errors[] = 'End Date is not correct or empty!';
                } else {
                    $entity->setPostingToDate(new \DateTime($postingToDate));
                }
                
                if (count($errors) > 0) {
                    return new ViewModel(array(
                        'redirectUrl' => $redirectUrl,
                        'errors' => $errors,
                        'entity' => $entity
                    ));
                }
                
                // NO ERROR
                $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                    "email" => $this->identity()
                ));
                
                $entity->setLastChangeBy($u);
                $entity->setLastChangeOn(new \DateTime());
                  $this->doctrineEM->persist($entity);
                $this->doctrineEM->flush();
                
                $this->flashMessenger()->addMessage('Posting Period "' . $periodName . '" is updated successfully!');
                return $this->redirect()->toUrl("/finance/posting-period/list");
            }
        }
        
        // NO Post
        $redirectUrl = null;
        if ($this->getRequest()->getHeader('Referer') !== null) {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }
        
        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $id,
            'token' => $token
        );
        
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod')->findOneBy($criteria);
        
        if ($entity !== null) {
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
     */
    public function alterAction()
    {
        $action = $this->params()->fromQuery('action');
        $entity_id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');
        
        $criteria = array(
            'id' => $entity_id,
            'token' => $token
        );
        
        /** @var \Application\Entity\NmtFinPostingPeriod $entity */
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod')->findOneBy($criteria);
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));
        if ($entity !== null) {
            
            if ($action == "open") {
                $entity->setPeriodStatus(\Application\Model\Constants::PERIOD_STATUS_OPEN);
                $entity->setLastChangeBy($u);
                $entity->setLastChangeOn(new \DateTime());
                
                $this->doctrineEM->persist($entity);
                $this->doctrineEM->flush();
            } elseif ($action == "close") {
                $entity->setPeriodStatus(\Application\Model\Constants::PERIOD_STATUS_CLOSED);
                $entity->setLastChangeBy($u);
                $entity->setLastChangeOn(new \DateTime());
                
                $this->doctrineEM->persist($entity);
                $this->doctrineEM->flush();
            }
        }
        return $this->redirect()->toUrl('/finance/posting-period/list');
    }

    /**
     *
     * @return \Zend\View\Helper\ViewModel
     */
    public function listAction()
    {
        $criteria = array();
        
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
        
        /** @var \Application\Repository\NmtFinPostingPeriodRepository $p */
        // $p = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod');
        
        /** @var \Application\Entity\NmtFinPostingPeriod $postingPeriod */
        // $postingPeriod = $p->getPostingPeriodStatus(new \DateTime());
        // echo $postingPeriod->getPeriodName() . $postingPeriod->getPeriodStatus();
        // echo $postingPeriod;
        
        $list = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod')->findBy($criteria, $sort_criteria);
        $total_records = count($list);
        $paginator = null;
        
        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $list = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod')->findBy($criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
        }
        
        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function updateTokenAction()
    {
        $criteria = array();
        $sort_criteria = array();
        
        $list = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod')->findBy($criteria, $sort_criteria);
        
        if (count($list) > 0) {
            foreach ($list as $entity) {
                $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
            }
        }
        
        $this->doctrineEM->flush();
        
        return new ViewModel(array(
            'list' => $list
        ));
    }

    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @return \PM\Controller\IndexController
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }
}
