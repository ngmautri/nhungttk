<?php

namespace Finance\Controller;

use Zend\Math\Rand;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Date;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityManager;
use MLA\Paginator;
use Application\Entity\FinVendorInvoice;
use Application\Entity\FinVendorInvoiceRow;
use Application\Entity\NmtInventoryTrx;
use Application\Entity\NmtProcurePo;
use Application\Entity\FinVendorInvoiceRowTmp;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class VInvoiceController extends AbstractActionController
{

    const CHAR_LIST = "__0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ__";

    protected $doctrineEM;

    /**
     * adding new vendor invoce
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function addAction()
    {
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $currency_list = $nmtPlugin->currencyList();
        
        /**@var \Application\Entity\MlaUsers $u ;*/
         $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));
        
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
                /** @var \Application\Entity\NmtBpVendor $vendor ; */
                $vendor = $this->doctrineEM->getRepository('Application\Entity\NmtBpVendor')->find($vendor_id);
            }
            
            if ($vendor !== null) {
                $entity->setVendor($vendor);
                $entity->setVendorName($vendor->getVendorName());
            } else {
                $errors[] = 'Vendor can\'t be empty. Please select a vendor!';
            }
            
            $currency = null;
            if ($currency_id > 0) {
                /** @var \Application\Entity\NmtApplicationCurrency  $currency ; */
                $currency = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->find($currency_id);
            }
            
            if ($currency !== null) {
                $entity->setCurrency($currency);
                $entity->setCurrencyIso3($currency->getCurrency());
            } else {
                $errors[] = 'Currency can\'t be empty. Please select a Currency!';
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
            // ++++++++++++++++++++++++++
            
            // generate document
            $criteria = array(
                'isActive' => 1,
                'subjectClass' => get_class($entity)
            );
            
            /** @var \Application\Entity\NmtApplicationDocNumber $docNumber ; */
            $docNumber = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationDocNumber')->findOneBy($criteria);
            if ($docNumber != null) {
                $maxLen = strlen($docNumber->getToNumber());
                $currentLen = 1;
                $currentDoc = $docNumber->getPrefix();
                $current_no = $docNumber->getCurrentNumber();
                
                if ($current_no == null) {
                    $current_no = $docNumber->getFromNumber();
                } else {
                    $current_no ++;
                    $currentLen = strlen($current_no);
                }
                
                $docNumber->setCurrentNumber($current_no);
                
                $tmp = "";
                for ($i = 0; $i < $maxLen - $currentLen; $i ++) {
                    
                    $tmp = $tmp . "0";
                }
                
                $currentDoc = $currentDoc . $tmp . $current_no;
                $entity->setSysNumber($currentDoc);
            }
            
             $createdOn = new \DateTime();
            
            $entity->setCreatedBy($u);
            $entity->setCreatedOn($createdOn);
            $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
            
            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();
            
            $m = sprintf('AP Invoice #%s - %s created. OK!', $entity->getId(), $entity->getSysNumber());
            $this->flashMessenger()->addMessage($m);
            
            // Trigger: finance.activity.log. AbtractController is EventManagerAware.
            $this->getEventManager()->trigger('finance.activity.log', __METHOD__, array(
                'priority' => \Zend\Log\Logger::INFO,
                'message' => $m,
                'createdBy' => $u,
                'createdOn' => $createdOn,
                'entity_id' => $entity->getId(),
                'entity_class' => get_class($entity),
                'entity_token' => $entity->getToken()
            ));
            
            $redirectUrl = "/finance/v-invoice/add1?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
            return $this->redirect()->toUrl($redirectUrl);
        }
        
        // NOT POST
        // ================================
        $redirectUrl = null;
        if ($request->getHeader('Referer') != null) {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }
        
        //get company
        
        $entity = new FinVendorInvoice();
        $entity->setIsActive(1);
        
        $default_cur=null;
        if($u->getCompany() instanceof  \Application\Entity\NmtApplicationCompany){
            $default_cur = $u->getCompany() ->getDefaultCurrency();
            $entity->setCurrency($default_cur);
        }
        
        $entity->setCurrentState('finalInvoice');
        
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
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $currency_list = $nmtPlugin->currencyList();
        
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
        $invoice = $res->getVendorInvoice($id, $token);
        
        if ($invoice == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        $entity = null;
        if ($invoice[0] instanceof FinVendorInvoice) {
            $entity = $invoice[0];
        }
        
        if ($entity instanceof FinVendorInvoice) {
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'entity' => $entity,
                'errors' => null,
                'currency_list' => $currency_list,
                'total_row' => $invoice['total_row'],
                'active_row' => $invoice['active_row'],
                'max_row_number' => $invoice['total_row'],
                'total_picture' => $invoice['total_picture'],
                'total_attachment' => $invoice['total_attachment'],
                'net_amount' => $invoice['net_amount'],
                'tax_amount' => $invoice['tax_amount'],
                'gross_amount' => $invoice['gross_amount']
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     * Make A/P Invoice from PO
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function copyFromPoAction()
    {
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $currency_list = $nmtPlugin->currencyList();
        
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            
            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');
            
            $id = (int) $request->getPost('target_id');
            $token = $request->getPost('target_token');
            
            /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
            $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
            $po = $res->getPo($id, $token);
            
            /**@var \Application\Entity\NmtProcurePo $target ;*/
            $target = null;
            
            if ($po !== null) {
                if ($po[0] instanceof NmtProcurePo) {
                    $target = $po[0];
                }
            }
            
            if ($target == null) {
                $errors[] = 'Contract /PO can\'t be empty!';
                $this->flashMessenger()->addMessage('Something wrong!');
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => null,
                    'target' => null,
                    'currency_list' => $currency_list
                ));
            }
            
            $validator = new Date();
            
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
            $entity->setPo($target);
            $entity->setCurrentState($currentState);
            
            if (! $validator->isValid($contractDate)) {
                $errors[] = 'Contract Date is not correct or empty!';
            } else {
                $entity->setContractDate(new \DateTime($contractDate));
            }
            
            $entity->setContractNo($contractNo);
            
            $vendor = null;
            if ($vendor_id > 0) {
                /** @var \Application\Entity\NmtBpVendor $vendor ; */
                $vendor = $this->doctrineEM->getRepository('Application\Entity\NmtBpVendor')->find($vendor_id);
            }
            
            if ($vendor !== null) {
                $entity->setVendor($vendor);
                $entity->setVendorName($vendor->getVendorName());
            } else {
                $errors[] = 'Vendor can\'t be empty. Please select a vendor!';
            }
            
            $currency = null;
            if ($currency_id > 0) {
                /** @var \Application\Entity\NmtApplicationCurrency  $currency ; */
                $currency = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->find($currency_id);
            }
            
            if ($currency !== null) {
                $entity->setCurrency($currency);
                $entity->setCurrencyIso3($currency->getCurrency());
            } else {
                $errors[] = 'Currency can\'t be empty. Please select a vendor!';
            }
            
            switch ($currentState) {
                case "draftInvoice":
                    
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
                    'target' => $target,
                    'currency_list' => $currency_list
                ));
            }
            // NO ERROR
            // ======================================================
            
            // Generate document BEGINN
            // =======================
            $criteria = array(
                'isActive' => 1,
                'subjectClass' => get_class($entity)
            );
            
            /** @var \Application\Entity\NmtApplicationDocNumber $docNumber ; */
            $docNumber = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationDocNumber')->findOneBy($criteria);
            if ($docNumber != null) {
                $maxLen = strlen($docNumber->getToNumber());
                $currentLen = 1;
                $currentDoc = $docNumber->getPrefix();
                $current_no = $docNumber->getCurrentNumber();
                
                if ($current_no == null) {
                    $current_no = $docNumber->getFromNumber();
                } else {
                    $current_no ++;
                    $currentLen = strlen($current_no);
                }
                
                $docNumber->setCurrentNumber($current_no);
                
                $tmp = "";
                for ($i = 0; $i < $maxLen - $currentLen; $i ++) {
                    
                    $tmp = $tmp . "0";
                }
                
                $currentDoc = $currentDoc . $tmp . $current_no;
                $entity->setSysNumber($currentDoc);
            }
            // Generate document END
            // =======================
            
            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                "email" => $this->identity()
            ));
            
            $entity->setCreatedBy($u);
            $entity->setCreatedOn(new \DateTime());
            $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
            
            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();
            
            // COPY PO Row to AP INvoice Row TMP
            $criteria = array(
                'isActive' => 1,
                'po' => $target
            );
            
            $sort_criteria = array(
                'rowNumber' => 'ASC'
            );
            
            $po_rows = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePoRow')->findBy($criteria, $sort_criteria);
            
            if (count($po_rows > 0)) {
                $n = 0;
                foreach ($po_rows as $r) {
                    /** @var \Application\Entity\NmtProcurePoRow $r ; */
                    $n ++;
                    $ap_row_tmp = new FinVendorInvoiceRowTmp();
                    $ap_row_tmp->setIsActive(1);
                    $ap_row_tmp->setRowNumber($n);
                    $ap_row_tmp->setRowIndentifer($entity->getSysNumber() . "-$n");
                    
                    $ap_row_tmp->setCurrentState("DRAFT");
                    $ap_row_tmp->setInvoice($entity);
                    $ap_row_tmp->setPoRow($r);
                    $ap_row_tmp->setPrRow($r->getPrRow());
                    $ap_row_tmp->setItem($r->getItem());
                    $ap_row_tmp->setQuantity($r->getQuantity());
                    $ap_row_tmp->setUnit($r->getUnit());
                    $ap_row_tmp->setUnitPrice($r->getUnitPrice());
                    $ap_row_tmp->setCreatedBy($u);
                    $ap_row_tmp->setCreatedOn(new \DateTime());
                    $ap_row_tmp->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
                    $ap_row_tmp->setRemarks("Ref: Contract/PO " . $r->getRowIdentifer());
                    
                    $this->doctrineEM->persist($ap_row_tmp);
                }
                $this->doctrineEM->flush();
            }
            
            $this->flashMessenger()->addMessage('A/P Invoice ' . $entity->getSysNumber() . ' is created from Contract /PO (' . $target->getSysNumber() . ')successfully!');
            
            $redirectUrl = "/finance/v-invoice/copy-from-po1?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
            return $this->redirect()->toUrl($redirectUrl);
        }
        
        // NOT POST ================================
        
        $redirectUrl = null;
        if ($request->getHeader('Referer') != null) {
            
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }
        
        $id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');
        
        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        $po = $res->getPo($id, $token);
        
        if ($po == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        /**@var \Application\Entity\NmtProcurePo $target ;*/
        
        $target = null;
        if ($po[0] instanceof NmtProcurePo) {
            $target = $po[0];
        }
        
        if ($target == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        $entity = new FinVendorInvoice();
        $entity->setContractNo($target->getContractNo());
        $entity->setContractDate($target->getContractDate());
        
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
            'target' => $target,
            'currency_list' => $currency_list
        ));
    }

    /**
     * Make A/P Invoice from PO
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function acceptFromPoAction()
    {
        $request = $this->getRequest();
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        $id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');
        
        /**@var \Application\Repository\FinVendorInvoiceRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice');
        $rows_tmp = $res->getAPInvoiceTmp($id, $token);
        
        if ($rows_tmp !== null) {
            
            /**@var \Application\Entity\FinVendorInvoice $target ;*/
            $target = null;
            
            if (count($rows_tmp) > 0) {
                $row_1 = $rows_tmp[0];
                if ($row_1 instanceof FinVendorInvoiceRowTmp) {
                    $target = $row_1->getInvoice();
                }
                
                if ($target == null) {
                    return $this->redirect()->toRoute('access_denied');
                }
                
                $n = 0;
                foreach ($rows_tmp as $r) {
                    
                    /**@var \Application\Entity\FinVendorInvoiceRowTmp $r ;*/
                    
                    $n ++;
                    $ap_row = new FinVendorInvoiceRow();
                    $ap_row->setIsActive(1);
                    $ap_row->setRowNumber($n);
                    $ap_row->setRowIndentifer($target->getSysNumber() . "-$n");
                    
                    $ap_row->setCurrentState($target->getCurrentState());
                    $ap_row->setInvoice($target);
                    $ap_row->setPoRow($r->getPoRow());
                    $ap_row->setPrRow($r->getPrRow());
                    $ap_row->setItem($r->getItem());
                    
                    $ap_row->setQuantity($r->getQuantity());
                    $ap_row->setUnit($r->getUnit());
                    $ap_row->setUnitPrice($r->getUnitPrice());
                    
                    $netAmount = $r->getQuantity() * $r->getUnitPrice();
                    
                    $taxRate = (int) $r->getTaxRate();
                    $ap_row->setTaxRate($taxRate);
                    
                    $taxAmount = $netAmount * $taxRate;
                    $grossAmount = $netAmount + $taxAmount;
                    
                    $ap_row->setNetAmount($netAmount);
                    $ap_row->setGrossAmount($grossAmount);
                    
                    $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                        "email" => $this->identity()
                    ));
                    
                    $ap_row->setCreatedBy($u);
                    $ap_row->setCreatedOn(new \DateTime());
                    $ap_row->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
                    $ap_row->setRemarks($r->getRemarks());
                    
                    $this->doctrineEM->persist($ap_row);
                    
                    $r->setCurrentState("ACCEPTED");
                    $r->setIsActive(0);
                    $this->doctrineEM->persist($r);
                    
                    $gr_entity = new NmtInventoryTrx();
                    $gr_entity->setVendor($target->getVendor());
                    $gr_entity->setFlow('IN');
                    $gr_entity->setInvoiceRow($ap_row);
                    $gr_entity->setItem($r->getItem());
                    $gr_entity->setPrRow($r->getPrRow());
                    $gr_entity->setQuantity($r->getQuantity());
                    $gr_entity->setVendorItemCode($r->getVendorItemCode());
                    $gr_entity->setVendorItemUnit($r->getUnit());
                    $gr_entity->setVendorUnitPrice($r->getUnitPrice());
                    $gr_entity->setTrxDate($target->getGrDate());
                    $gr_entity->setCurrency($target->getCurrency());
                    $gr_entity->setRemarks("GR of Invoice " . $target->getInvoiceNo());
                    $gr_entity->setWh($target->getWarehouse());
                    $gr_entity->setCreatedBy($u);
                    $gr_entity->setCreatedOn(new \DateTime());
                    $gr_entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
                    $gr_entity->setChecksum(Rand::getString(32, self::CHAR_LIST, true));
                    
                    $gr_entity->setTaxRate($r->getTaxRate());
                    
                    $gr_entity->setCurrentState($target->getCurrentState());
                    
                    if ($target->getCurrentState() == "finalInvoice") {
                        $gr_entity->setIsActive(1);
                    } else {
                        $gr_entity->setIsActive(0);
                    }
                    
                    $this->doctrineEM->persist($gr_entity);
                    $this->doctrineEM->flush();
                }
            }
            
            $this->doctrineEM->flush();
            
            /*
             * $redirectUrl = "/finance/v-invoice/copy-from-po1?token=" . $target>getToken() . "&entity_id=" . $entity->getId();
             */
            $redirectUrl = "/finance/v-invoice/list";
            return $this->redirect()->toUrl($redirectUrl);
            
            /*
             * return new ViewModel(array(
             * 'target' => $target,
             * ));
             */
        }
        return $this->redirect()->toRoute('access_denied');
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function copyFromPo1Action()
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
        $invoice = $res->getVendorInvoiceTmp($id, $token);
        
        if ($invoice == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        $entity = null;
        if ($invoice[0] instanceof FinVendorInvoice) {
            $entity = $invoice[0];
        }
        
        if ($entity !== null) {
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'entity' => $entity,
                'errors' => null,
                'currency_list' => $currency_list,
                'total_row' => $invoice['total_row'],
                'active_row' => $invoice['active_row'],
                'max_row_number' => $invoice['total_row'],
                'net_amount' => $invoice['net_amount'],
                'tax_amount' => $invoice['tax_amount'],
                'gross_amount' => $invoice['gross_amount']
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
     * Edit Invoice Header
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function editAction()
    {
        $request = $this->getRequest();
        
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $currency_list = $nmtPlugin->currencyList();
        
        if ($request->isPost()) {
            
            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');
            
            $entity_id = $request->getPost('entity_id');
            $token = $request->getPost('token');
            $nTry = $request->getPost('n');
            
            $criteria = array(
                'id' => $entity_id,
                'token' => $token
            );
            
            /**@var \Application\Entity\FinVendorInvoice $entity*/
            $entity = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findOneBy($criteria);
            
            if (! $entity instanceof \Application\Entity\FinVendorInvoice) {
                
                $errors[] = 'Entity object can\'t be empty. Or token key is not valid!';
                $this->flashMessenger()->addMessage('Something wrong!');
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => null,
                    'currency_list' => $currency_list,
                    'n' => $nTry
                
                ));
                
                // might need redirect
            } else {
                $oldEntity = clone ($entity);
                
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
                
                /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
                $nmtPlugin = $this->Nmtplugin();
                $changeArray = $nmtPlugin->objectsAreIdentical($oldEntity, $entity);
                
                if (count($changeArray) == 0) {
                    $nTry ++;
                    $errors[] = sprintf('Nothing changed! n = %s', $nTry);
                }
                
                if ($nTry >= 3) {
                    $errors[] = sprintf('Do you really want to edit "AP Inv. %s (%s)"?', $entity->getInvoiceNo(), $entity->getSysNumber());
                }
                
                if ($nTry == 5) {
                    $m = sprintf('You might be not ready to edit "AP Inv. %s (%s)". Please try later!', $entity->getInvoiceNo(), $entity->getSysNumber());
                    $this->flashMessenger()->addMessage($m);
                    return $this->redirect()->toUrl($redirectUrl);
                }
                
                if (count($errors) > 0) {
                    return new ViewModel(array(
                        'redirectUrl' => $redirectUrl,
                        'errors' => $errors,
                        'entity' => $entity,
                        'currency_list' => $currency_list,
                        'n' => $nTry
                    
                    ));
                }
                
                // NO ERROR
                // ===================
                
                $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                    "email" => $this->identity()
                ));
                
                $changeOn = new \DateTime();
                
                $entity->setRevisionNo($entity->getRevisionNo() + 1);
                $entity->setLastchangeBy($u);
                $entity->setLastchangeOn($changeOn);
                
                $this->doctrineEM->persist($entity);
                $this->doctrineEM->flush();
                
                $m = sprintf('AP Invoice #%s - %s updated. No. of change: %s. OK!', $entity->getId(), $entity->getSysNumber(), count($changeArray));
                
                // Trigger Change Log. AbtractController is EventManagerAware.
                $this->getEventManager()->trigger('finance.change.log', __METHOD__, array(
                    'priority' => 7,
                    'message' => $m,
                    'objectId' => $entity->getId(),
                    'objectToken' => $entity->getToken(),
                    'changeArray' => $changeArray,
                    'changeBy' => $u,
                    'changeOn' => $changeOn,
                    'revisionNumber' => $entity->getRevisionNo(),
                    'changeDate' => $changeOn,
                    'changeValidFrom' => $changeOn
                ));
                
                // Trigger: finance.activity.log. AbtractController is EventManagerAware.
                $this->getEventManager()->trigger('finance.activity.log', __METHOD__, array(
                    'priority' => \Zend\Log\Logger::INFO,
                    'message' => $m,
                    'createdBy' => $u,
                    'createdOn' => $changeOn,
                    'entity_id' => $entity->getId(),
                    'entity_class' => get_class($entity),
                    'entity_token' => $entity->getToken()
                ));
                
                $this->flashMessenger()->addMessage($m);
                
                // Update current state of row invoice row
                
                $query = $this->doctrineEM->createQuery('
UPDATE Application\Entity\FinVendorInvoiceRow r SET r.currentState = :new_state WHERE r.invoice =:invoice_id
                    ')->setParameters(array(
                    'new_state' => $entity->getCurrentState(),
                    'invoice_id' => $entity->getId()
                ));
                $query->getResult();
                
                $criteria = array(
                    'isActive' => 1,
                    'invoice' => $entity->getId()
                );
                $sort_criteria = array();
                $invoice_rows = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoiceRow')->findBy($criteria, $sort_criteria);
                
                // update current state of stock row.
                if (count($invoice_rows) > 0) {
                    foreach ($invoice_rows as $r) {
                        $query = $this->doctrineEM->createQuery('
UPDATE Application\Entity\NmtInventoryTrx t SET t.currentState = :new_state, t.isActive=:is_active WHERE t.invoiceRow =:invoice_row_id
                    ')->setParameters(array(
                            'new_state' => $r->getCurrentState(),
                            'is_active' => 1,
                            'invoice_row_id' => $r->getId()
                        
                        ));
                        $query->getResult();
                    }
                }
                
                $redirectUrl = "/finance/v-invoice/add1?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
                return $this->redirect()->toUrl($redirectUrl);
            }
        }
        
        // NO POST
        // ====================
        
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
        
        if ($entity instanceof \Application\Entity\FinVendorInvoice) {
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'errors' => null,
                'entity' => $entity,
                'currency_list' => $currency_list,
                'n' => 0
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\Http\Response
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
        $is_active = (int) $this->params()->fromQuery('is_active');
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        $currentState = $this->params()->fromQuery('currentState');
        
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
        
        if ($sort_by == null) :
            $sort_by = "createdOn";
        endif;
        
        if ($sort == null) :
            $sort = "DESC";
        endif;
        
        /** @var \Application\Repository\NmtFinPostingPeriodRepository $p */
            // $p = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod');
        
        /** @var \Application\Entity\NmtFinPostingPeriod $postingPeriod */
            // $postingPeriod = $p->getPostingPeriodStatus(new \DateTime());
            // echo $postingPeriod->getPeriodName() . $postingPeriod->getPeriodStatus();
            // echo $postingPeriod;
        
        /**@var \Application\Repository\FinVendorInvoiceRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice');
        $list = $res->getVendorInvoiceList($is_active, $currentState, null, $sort_by, $sort, 0, 0);
        $total_records = count($list);
        $paginator = null;
        
        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            // $list = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findBy($criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
            $list = $res->getVendorInvoiceList($is_active, $currentState, null, $sort_by, $sort, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
        }
        
        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator,
            'is_active' => $is_active,
            'sort_by' => $sort_by,
            'sort' => $sort,
            'per_pape' => $resultsPerPage,
            'currentState' => $currentState
        
        ));
    }

    /**
     *
     * @return \Zend\View\Helper\ViewModel
     */
    public function vendorAction()
    {
        $request = $this->getRequest();
        
        // accepted only ajax request
        
        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        $this->layout("layout/user/ajax");
        
        $vendor_id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');
        
        $is_active = (int) $this->params()->fromQuery('is_active');
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        $currentState = $this->params()->fromQuery('currentState');
        
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
        
        if ($sort_by == null) :
            $sort_by = "createdOn";
        endif;
        
        if ($sort == null) :
            $sort = "DESC";
        endif;
        
        /**@var \Application\Repository\FinVendorInvoiceRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice');
        $list = $res->getInvoicesOf($vendor_id, $is_active, $currentState, null, $sort_by, $sort, 0, 0);
        $total_records = count($list);
        $paginator = null;
        
        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            // $list = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findBy($criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
            $list = $res->getInvoicesOf($vendor_id, $is_active, $currentState, null, $sort_by, $sort, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
        }
        
        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator,
            'is_active' => $is_active,
            'sort_by' => $sort_by,
            'sort' => $sort,
            'per_pape' => $resultsPerPage,
            'currentState' => $currentState
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
        
        $list = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findBy($criteria, $sort_criteria);
        
        if (count($list) > 0) {
            foreach ($list as $entity) {
                
                /**@var \Application\Entity\FinVendorInvoice $entity ;*/
                
                if ($entity->getVendor() !== null) {
                    $entity->setVendorName($entity->getVendor()
                        ->getVendorName());
                }
                
                if ($entity->getCurrency() !== null) {
                    $entity->setCurrencyIso3($entity->getCurrency()
                        ->getCurrency());
                }
                
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
