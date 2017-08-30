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
use Application\Entity\FinVendorInvoiceRow;

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

    /**
     * adding new vendor invoce
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
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
            
            $contractDate = $request->getPost('contractDate');
            $contractNo = $request->getPost('contractNo');
            $currentState = $request->getPost('currentState');
            
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
            
            if ($sapDoc == "") {
                $sapDoc = "N/A";
            }
            
            $entity = new FinVendorInvoice();
            $entity->setIsActive($isActive);
            
            $entity->setCurrentState($currentState);
            
            $vendor = null;
            if ($vendor_id > 0) {
                $vendor = $this->doctrineEM->getRepository('Application\Entity\NmtBpVendor')->find($vendor_id);
            }
            
            if ($vendor !== null) {
                $entity->setVendor($vendor);
            } else {
                $errors[] = 'Vendor can\'t be empty. Please select a vendor!';
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
            
            $validator = new Date();
            
            switch ($currentState) {
                case "contract":
                    // contract number not empty
                    
                    if ($contractNo == "") {
                        $errors[] = 'Contract is not correct or empty!';
                    } else {
                        $entity->setContractNo($contractNo);
                    }
                    
                    if (! $validator->isValid($contractDate)) {
                        $errors[] = 'Contract Date is not correct or empty!';
                    } else {
                        $entity->setContractDate(new \DateTime($contractDate));
                    }
                    
                    break;
                case "draftInvoice":
                    
                    /**
                     *
                     * @todo
                     */
                    
                    /*
                     * if ($invoiceNo == null) {
                     * $errors[] = 'Please enter Invoice Number!';
                     * } else {
                     * $entity->setInvoiceNo($invoiceNo);
                     * }
                     *
                     * if (! $validator->isValid($invoiceDate)) {
                     * $errors[] = 'Invoice Date is not correct or empty!';
                     * } else {
                     * $entity->setInvoiceDate(new \DateTime($invoiceDate));
                     * }
                     */
                    
                    break;
                
                case "finalInvoice":
                    
                    /**
                     *
                     * @todo
                     */
                    
                    if ($invoiceNo == null) {
                        $errors[] = 'Please enter Invoice Number!';
                    } else {
                        $entity->setInvoiceNo($invoiceNo);
                    }
                    
                    $entity->setSapDoc($sapDoc);
                    
                    if (! $validator->isValid($invoiceDate)) {
                        $errors[] = 'Invoice Date is not correct or empty!';
                    } else {
                        $entity->setInvoiceDate(new \DateTime($invoiceDate));
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
                    
                    break;
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
            
            $this->flashMessenger()->addMessage($invoiceNo . 'of ' . $entity->getVendor()
                ->getVendorName() . '" is created successfully!');
            
            $redirectUrl = "/finance/v-invoice/add1?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
            return $this->redirect()->toUrl($redirectUrl);
        }
        
        // NOT POST ================================
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
       
        /**@var \Application\Repository\FinVendorInvoiceRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice');
        $invoice = $res->getVendorInvoice($id,$token);
  
        if($invoice==null){
            return $this->redirect()->toRoute('access_denied');
        }
                
        $entity = null;
        if($invoice[0] instanceof FinVendorInvoice)        {
            $entity = $invoice[0];
        }
        
        if ($entity !== null) {
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'entity' => $entity,
                'errors' => null,
                'currency_list' => $currency_list,
                'total_row' => $invoice['total_row'],
                'max_row_number' => $invoice['total_row'],
                'net_amount' => $invoice['net_amount'],
                'tax_amount' => $invoice['tax_amount'],
                'gross_amount' => $invoice['gross_amount'],
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
        
        $criteria = array(
            'isActive' => 1
        );
        $sort_criteria = array(
            'currency' => 'ASC'
        );
        
        $currency_list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->findBy($criteria, $sort_criteria);
        
        if ($request->isPost()) {
            
            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');
            
            $entity_id = $request->getPost('entity_id');
            $token = $request->getPost('token');
            
            $criteria = array(
                'id' => $entity_id,
                'token' => $token
            );
            
            /**@var \Application\Entity\FinVendorInvoice $entity*/
            $entity = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findOneBy($criteria);
            
            if ($entity == null) {
                
                $errors[] = 'Entity object can\'t be empty. Or token key is not valid!';
                $this->flashMessenger()->addMessage('Something wrong!');
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => null,
                    'currency_list' => $currency_list
                ));
                
                // might need redirect
            } else {
                
                $errors = array();
                $redirectUrl = $request->getPost('redirectUrl');
                
                $contractDate = $request->getPost('contractDate');
                $contractNo = $request->getPost('contractNo');
                $currentState = $request->getPost('currentState');
                
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
                
                if ($sapDoc == "") {
                    $sapDoc = "N/A";
                }
                
                $entity->setIsActive($isActive);
                
                $entity->setCurrentState($currentState);
                
                $vendor = null;
                if ($vendor_id > 0) {
                    $vendor = $this->doctrineEM->getRepository('Application\Entity\NmtBpVendor')->find($vendor_id);
                }
                
                if ($vendor !== null) {
                    $entity->setVendor($vendor);
                } else {
                    $errors[] = 'Vendor can\'t be empty. Please select a vendor!';
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
                
                $validator = new Date();
                
                switch ($currentState) {
                    case "contract":
                        // contract number not empty
                        
                        if ($contractNo == "") {
                            $errors[] = 'Contract is not correct or empty!';
                        } else {
                            $entity->setContractNo($contractNo);
                        }
                        
                        if (! $validator->isValid($contractDate)) {
                            $errors[] = 'Contract Date is not correct or empty!';
                        } else {
                            $entity->setContractDate(new \DateTime($contractDate));
                        }
                        
                        break;
                    case "draftInvoice":
                        
                        /**
                         *
                         * @todo
                         */
                        
                        /*
                         * if ($invoiceNo == null) {
                         * $errors[] = 'Please enter Invoice Number!';
                         * } else {
                         * $entity->setInvoiceNo($invoiceNo);
                         * }
                         *
                         * if (! $validator->isValid($invoiceDate)) {
                         * $errors[] = 'Invoice Date is not correct or empty!';
                         * } else {
                         * $entity->setInvoiceDate(new \DateTime($invoiceDate));
                         * }
                         */
                        
                        break;
                    
                    case "finalInvoice":
                        
                        /**
                         *
                         * @todo
                         */
                        
                        if ($invoiceNo == null) {
                            $errors[] = 'Please enter Invoice Number!';
                        } else {
                            $entity->setInvoiceNo($invoiceNo);
                        }
                        
                        $entity->setSapDoc($sapDoc);
                        
                        if (! $validator->isValid($invoiceDate)) {
                            $errors[] = 'Invoice Date is not correct or empty!';
                        } else {
                            $entity->setInvoiceDate(new \DateTime($invoiceDate));
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
                        
                        break;
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
                
                // NO ERROR =====
                $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                    "email" => $this->identity()
                ));
                
                $entity->setLastchangeBy($u);
                $entity->setLastchangeOn(new \DateTime());
                
                $this->doctrineEM->persist($entity);
                $this->doctrineEM->flush();
                
                $this->flashMessenger()->addMessage('Doc. ' . $invoiceNo . 'of ' . $entity->getVendor()
                    ->getVendorName() . '" is updated successfully!');
                
                // update current state of row
                $query = $this->doctrineEM->createQuery('
UPDATE Application\Entity\FinVendorInvoiceRow r SET r.currentState = :new_state WHERE r.invoice =:invoice_id
                    ')->setParameters(array(
                        'new_state' => $entity->getCurrentState(),
                        'invoice_id' => $entity->getId(),
                    ));
                
                $query->getResult();
                
                $redirectUrl = "/finance/v-invoice/add1?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
                return $this->redirect()->toUrl($redirectUrl);
            }
        }
        
        // NO POST ====================
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
        
        /**@var \Application\Entity\FinVendorInvoice $entity*/
        $entity = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findOneBy($criteria);
        
        if ($entity !== null) {
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'errors' => null,
                'entity' => $entity,
                'currency_list' => $currency_list
            
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
        
        $is_active = (int) $this->params()->fromQuery('is_active');
        
        if ($is_active == null) {
            $is_active = 1;
        }
        
        /** @var \Application\Repository\NmtFinPostingPeriodRepository $p */
        // $p = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod');
        
        /** @var \Application\Entity\NmtFinPostingPeriod $postingPeriod */
        // $postingPeriod = $p->getPostingPeriodStatus(new \DateTime());
        // echo $postingPeriod->getPeriodName() . $postingPeriod->getPeriodStatus();
        // echo $postingPeriod;
        
        $criteria = array(
            'isActive' => $is_active
        );
        
        $sort_criteria = array(
            'createdOn' => 'DESC'
        );
        
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
            'paginator' => $paginator,
            'is_active' => $is_active
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
