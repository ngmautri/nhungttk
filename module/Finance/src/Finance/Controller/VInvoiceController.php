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
use Application\Entity\FinVendorInvoice;

/**
 *
 * @author nmt
 *        
 */
class VInvoiceController extends AbstractActionController
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
        $criteria = array(
            'isActive' => 1
        );
        $sort_criteria = array(
            'currency' => 'ASC'
        );
        
        $currency_list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->findBy($criteria, $sort_criteria);
        
        
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            
            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');
            
            $vendor_id = (int) $request->getPost('vendor_id');
            $currency_id = (int) $request->getPost('currency_id');
            $warehouse_id = (int) $request->getPost('target_wh_id');
            
            $postingDate = $request->getPost('postingDate');
            $grDate = $request->getPost('grDate');
            $invoiceDate = $request->getPost('invoiceDate');
            $invoiceNo = $request->getPost('invoiceNo');
            $sapDoc = $request->getPost('sapDoc');
            $isActive = (int) $request->getPost('isActive');
            $remarks = $request->getPost('remarks');
            
            if ($isActive !== 1) {
                $isActive = 0;
            }
            
            $entity = new FinVendorInvoice();
            $entity->setIsActive($isActive);
            
            $vendor = null;
            if ($vendor_id > 0) {
                $vendor = $this->doctrineEM->getRepository('Application\Entity\NmtBpVendor')->find($vendor_id);
            }
            
            if ($vendor !== null) {
                $entity->setVendor($vendor);
            } else {
                $errors[] = 'Vendor can\'t be empty. Please select a vendor!';
            }
            
            if ($invoiceNo == null) {
                $errors[] = 'Please enter Invoice Number!';
            } else {
                $entity->setInvoiceNo($invoiceNo);
            }
            
            $entity->setSapDoc($sapDoc);
            
            $validator = new Date();
            
            if (! $validator->isValid($invoiceDate)) {
                $errors[] = 'Invoice Date is not correct or empty!';
            } else {
                $entity->setInvoiceDate(new \DateTime($invoiceDate));
            }
            
            if (! $validator->isValid($invoiceDate)) {
                $errors[] = 'Invoice Date is not correct or empty!';
            } else {
                $entity->setInvoiceDate(new \DateTime($invoiceDate));
            }
            
            $currency = null;
            if ($currency_id > 0) {
                $currency = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->find($currency_id);
            }
            
            if ($currency !== null) {
                $entity->setCurrency($currency);
            } else {
                $errors[] = 'Currency can\'t be empty. Please select a vendor!';
            }
            
            if (! $validator->isValid($postingDate)) {
                $errors[] = 'Posting Date is not correct or empty!';
            } else {
                
                $entity->setPostingDate(new \DateTime($postingDate));
                
                // check if posting period is close
                /** @var \Application\Repository\NmtFinPostingPeriodRepository $p */
                $p = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod');
                
                /** @var \Application\Entity\NmtFinPostingPeriod $postingPeriod */
                $postingPeriod = $p->getPostingPeriod(new \DateTime($postingDate));
                
                if ($postingPeriod->getPeriodStatus() == "C") {
                    $errors[] = 'Posting period "' . $postingPeriod->getPeriodName() . '" is closed or not created yet!';
                }
            }
            
            if (! $validator->isValid($grDate)) {
                $errors[] = 'Good receipt Date is not correct or empty!';
            } else {
                $entity->setGrDate(new \DateTime($grDate));
                  // check if posting period is close
                /** @var \Application\Repository\NmtFinPostingPeriodRepository $p */
                $p = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod');
                
                /** @var \Application\Entity\NmtFinPostingPeriod $postingPeriod */
                $postingPeriod = $p->getPostingPeriod(new \DateTime($grDate));
                
                if ($postingPeriod->getPeriodStatus() == "C") {
                    $errors[] = ' period "' . $postingPeriod->getPeriodName() . '" is closed or not created yet!';
                }
            }
            
            $warehouse = null;
            if ($warehouse_id > 0) {
                $warehouse = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->find($warehouse_id);
            }
            
            if ($warehouse !== null) {
                $entity->setWarehouse($warehouse);
            } else {
                $errors[] = 'Warehouse can\'t be empty. Please select a vendor!';
            }
            
            $entity->setRemarks($remarks);
            
            if (count($errors) > 0) {
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'currency_list' => $currency_list
                ));
            }
            
            // NO ERROR
            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                "email" => $this->identity()
            ));
            
            $entity->setCreatedBy($u);
            $entity->setCreatedOn(new \DateTime());
            $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
            
            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();
            
            $this->flashMessenger()->addMessage('Invoice Period "' . $invoiceNo . '" is created successfully!');
            
            $redirectUrl = "/finance/v-invoice/add1?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
            return $this->redirect()->toUrl($redirectUrl);
        }
        
        // NOT POST
        $redirectUrl = null;
        if ($request->getHeader('Referer') != null) {
            
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }
        
        $entity = new FinVendorInvoice();
        
        $entity->setIsActive(1);
        
        $default_wh = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->findOneBy(array(
            'isDefault' => 1
        ));
        
        if ($default_wh !== null) {
            $entity->setWarehouse($default_wh);
        }
        
        return new ViewModel(array(
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'currency_list' => $currency_list
        ));
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function add1Action()
    {
        $criteria = array(
            'isActive' => 1
        );
        $sort_criteria = array(
            'currency' => 'ASC'
        );
        
        $currency_list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->findBy($criteria, $sort_criteria);
        
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
        
        $query = 'SELECT e,v FROM Application\Entity\FinVendorInvoice e Join e.vendor v
            WHERE e.id=?1 AND e.token =?2';
        
        $entity = $this->doctrineEM->createQuery ( $query )->setParameters ( array (
            "1" => $id,
            "2" => $token,            
        ) )->getSingleResult();
        
        //var_dump($entity);
        
        
        //$entity = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findOneBy($criteria);
        if ($entity !== null) {
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'entity' => $entity,
                'errors' => null,
                'currency_list' => $currency_list
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
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
                $entity->setPeriodStatus("N");
                $entity->setLastChangeBy($u);
                $entity->setLastChangeOn(new \DateTime());
                
                $this->doctrineEM->persist($entity);
                $this->doctrineEM->flush();
            } elseif ($action == "close") {
                $entity->setPeriodStatus("C");
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
        
        $list = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findBy($criteria, $sort_criteria);
        $total_records = count($list);
        $paginator = null;
        
        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $list = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findBy($criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
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
