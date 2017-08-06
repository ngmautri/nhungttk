<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Finance\Controller;

use Zend\Escaper\Escaper;
use Zend\Math\Rand;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Date;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityManager;
use MLA\Paginator;
use Application\Entity\FinVendorInvoice;
use Application\Entity\FinVendorInvoiceRow;
use Application\Entity\NmtInventoryTrx;

/**
 *
 * @author nmt
 *        
 */
class VInvoiceRowController extends AbstractActionController
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
            $invoice_id = $request->getPost('invoice_id');
            $invoice_token = $request->getPost('invoice_token');
            
              
            $criteria = array(
                'id' => $invoice_id,
                'token' => $invoice_token
            );
            
            /** @var \Application\Entity\FinVendorInvoice $target ;*/
            $target = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findOneBy($criteria);
            
            if ($target == null) {
                
                $errors[] = 'Invoice object can\'t be empty. Or token key is not valid!';
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'target' => null,
                    'entity' => null,
                    'currency_list' =>$currency_list
                ));
                // might need redirect
            } else {
                
                $item_id = $request->getPost('item_id');
                $pr_row_id = $request->getPost('pr_row_id');
                $isActive = $request->getPost('isActive');
                
                
                $vendorItemCode = $request->getPost('vendorItemCode');
                $unit = $request->getPost('unit');
                $conversionFactor = $request->getPost('conversionFactor');
                $converstionText = $request->getPost('converstionText');
                
                $quantity = $request->getPost('quantity');
                $unitPrice = $request->getPost('unitPrice');
                $taxRate = $request->getPost('taxRate');
                $traceStock = $request->getPost('traceStock');
                
                $remarks = $request->getPost('remarks');
                
                   
                if ($isActive !== 1) {
                    $isActive = 0;
                }
                
                // Inventory Transaction:
                
                $entity = new FinVendorInvoiceRow();
                $entity->setIsActive($isActive);
                
                $entity->setInvoice($target);
                
                $pr_row = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->find($pr_row_id);
                $item = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->find($item_id);
                
                
                if($pr_row == null){
                    //$errors[] = 'Item can\'t be empty!';
                }else{
                    $entity->setPrRow($pr_row);
                    
                }
                 
                if($item== null){
                    $errors[] = 'Item can\'t be empty!';
                }else{
                    $entity->setItem($item);
                }
                
                $entity->setVendorItemCode($vendorItemCode);
                $entity->setUnit($unit);
                $entity->setConversionFactor($conversionFactor);
                $entity->setConverstionText($converstionText);
                
                $n_validated= 0;
                if ($quantity == null) {
                    $errors[] = 'Please  enter quantity!';
                } else {
                    
                    if (! is_numeric($quantity)) {
                        $errors[] = 'Quantity must be a number.';
                    } else {
                        if ($quantity <= 0) {
                            $errors[] = 'Quantity must be greater than 0!';
                        }
                        $entity->setQuantity($quantity);
                        $n_validated++;
                    }
                }
                 
                if ($unitPrice !== null) {
                    if (! is_numeric($unitPrice)) {
                         $errors [] = 'Price is not valid. It must be a number.';
                    } else {
                        if ($unitPrice <= 0) {
                            $errors[] = 'Price must be greate than 0!';
                        }
                        $entity->setUnitPrice($unitPrice);
                        $n_validated++;
                    }
                }                
                
                if($n_validated == 2){
                    $netAmount = $entity->getQuantity()*$entity->getUnitPrice();
                    $entity->setNetAmount($netAmount);
                }
                
                if ($taxRate !== null) {
                    if (! is_numeric($taxRate)) {
                        $errors [] = '$taxRate is not valid. It must be a number.';
                    } else {
                        if ($taxRate < 0) {
                            $errors[] = '$taxRate must be greate than 0!';
                        }
                        $entity->setTaxRate($taxRate);
                        $n_validated++;
                    }
                }    
                
                if($n_validated==3){
                    $taxAmount = $entity->getNetAmount()*$entity->getTaxRate()/100;
                    $grossAmount = $entity->getNetAmount() + $taxAmount;
                    $entity->setTaxAmount($taxAmount);
                    $entity->setGrossAmount($grossAmount);
                }
                
                //$entity->setTraceStock($traceStock);
                $entity->setRemarks($remarks);
                 
                if (count($errors) > 0) {
                    
                    return new ViewModel(array(
                        'redirectUrl' => $redirectUrl,
                        'errors' => $errors,
                        'target' => $target,
                        'entity' => $entity,
                        'currency_list' =>$currency_list
                        
                    ));
                }
                ;
                // OK now
                
                $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                    'email' => $this->identity()
                ));
                
                $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
                
                $entity->setCreatedBy($u);
                $entity->setCreatedOn(new \DateTime());
                $this->doctrineEM->persist($entity);
                $this->doctrineEM->flush();
                
                $gr_entity = new NmtInventoryTrx();
                $gr_entity->setVendor($target->getVendor());
                $gr_entity->setFlow('IN');
                $gr_entity->setInvoiceRow($entity);
                $gr_entity->setItem($entity->getItem());
                $gr_entity->setPrRow($entity->getPrRow());
                $gr_entity->setQuantity($quantity);
                $gr_entity->setVendorItemCode($entity->getVendorItemCode());
                $gr_entity->setVendorItemUnit($entity->getUnit());
                $gr_entity->setVendorUnitPrice($entity->getUnitPrice());
                $gr_entity->setIsActive(1);
                $gr_entity->setTrxDate($target->getGrDate());
                $gr_entity->setCurrency($target->getCurrency());
                $gr_entity->setRemarks("Auto:");
                $gr_entity->setWh($target->getWarehouse());
                $gr_entity->setCreatedBy($u);
                $gr_entity->setCreatedOn(new \DateTime());
                $gr_entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
                $gr_entity->setChecksum(Rand::getString(32, self::CHAR_LIST, true));
                
                $gr_entity->setTaxRate($entity->getTaxRate());
                
                $this->doctrineEM->persist($gr_entity);
                $this->doctrineEM->flush();                
                
                $redirectUrl = "/finance/v-invoice-row/add?token=".$target->getToken()."&target_id=".$target->getId();
                $this->flashMessenger()->addMessage('Invoice Item has been created successfully!');
                return $this->redirect()->toUrl($redirectUrl);
            }
        }
        
        // NO POST
        $redirectUrl = Null;
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        $redirectUrl = $this->getRequest()
        ->getHeader('Referer')
        ->getUri();
        
        $target_id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');
        
        $criteria = array(
            'id' => $target_id,
            'token' => $token
        );
        
        $target = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findOneBy($criteria);
        
        
        $entity = new FinVendorInvoiceRow();
  
        // set null
        $entity->setIsActive(1);
        
        return new ViewModel(array(
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'target' => $target,
            'currency_list' => $currency_list
            
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
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function processAction()
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
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function girdAction() {
        $request = $this->getRequest ();
        
        //$pq_curPage = $_GET ["pq_curpage"];
        //$pq_rPP = $_GET ["pq_rpp"];
        
        $target_id = ( int ) $this->params ()->fromQuery ( 'target_id' );
        $token = $this->params ()->fromQuery ( 'token' );
        $criteria = array (
            'id' => $target_id,
             'token' => $token
        );
        
        /**
         *
         * @todo : Change Target
         */
        $target = $this->doctrineEM->getRepository ( 'Application\Entity\FinVendorInvoice' )->findOneBy ( $criteria );
        
        $a_json_final = array ();
        $a_json = array ();
        $a_json_row = array ();
        $escaper = new Escaper ();
        
        if ($target !== null) {
            
            $criteria = array (
                'invoice' => $target_id,
                'isActive' => 1,
            );
            
            $query = 'SELECT e FROM Application\Entity\FinVendorInvoiceRow e 
            WHERE e.invoice=?1 AND e.isActive =?2';
            
            $list = $this->doctrineEM->createQuery ( $query )->setParameters ( array (
                "1" => $target,
                "2" => 1,
             
            ) )->getResult ();
            
            $total_records = 0;
            if (count ( $list ) > 0) {
                
                $total_records = count ( $list );
                foreach ( $list as $a ) {
                    
                   /** @var \Application\Entity\FinVendorInvoiceRow $a ;*/
                    
                    $a_json_row ["row_id"] = $a->getId();
                    $a_json_row ["row_token"] = $a->getToken();
                    $a_json_row ["row_unit"] = $a->getUnit();
                    $a_json_row ["row_quantity"] = $a->getQuantity();
                    $a_json_row ["row_unit_price"] =number_format($a->getUnitPrice(),2);
                    $a_json_row ["row_net"] = number_format($a->getNetAmount(),2);
                    $a_json_row ["row_tax_rate"] = $a->getTaxRate();
                    $a_json_row ["row_gross"] = number_format($a->getGrossAmount(),2);
                
                    $a_json_row ["pr_number"] = $a->getPrRow()->getPr()->getPrNumber();
                    
                    $a_json_row ["item_name"] = $a->getItem()->getItemName();
                    $a_json_row ["item_sku"] = $a->getItem()->getItemSku();
                    $a_json_row ["item_token"] = $a->getItem()->getToken();
                    $a_json_row ["item_checksum"] = $a->getItem()->getChecksum();
                    
                    $a_json [] = $a_json_row;
                }
            }
            
            $a_json_final ['data'] = $a_json;
            $a_json_final ['totalRecords'] = $total_records;
            //$a_json_final ['curPage'] = $pq_curPage;
        }
        
        $response = $this->getResponse ();
        $response->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/json' );
        $response->setContent ( json_encode ( $a_json_final ) );
        return $response;
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
