<?php
namespace Procure\Controller;

use Zend\Math\Rand;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Date;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityManager;
use MLA\Paginator;
use Application\Entity\NmtProcurePo;
use Application\Entity\NmtProcurePoRow;
use Application\Entity\NmtProcureGr;
use Application\Entity\NmtProcureGrRow;
use Application\Entity\NmtInventoryTrx;

/**
 * Good Receipt Controller
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GrController extends AbstractActionController
{

    const CHAR_LIST = "_0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";

    protected $doctrineEM;

    /**
     * Make GR from PO
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function copyFromPoAction()
    {
        $this->layout("Procure/layout-fullscreen");
        
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $currency_list = $nmtPlugin->currencyList();
        
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            
            /*
             * 1. created GR header
             * 2. create GR line
             * 3. mark is Draft
             *
             */
            
            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');
            
            $id = (int) $request->getPost('source_id');
            $token = $request->getPost('source_token');
            
            /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
            $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
            $po = $res->getPo($id, $token);
            
            /**@var \Application\Entity\NmtProcurePo $source ;*/
            $source = null;
            
            if ($po !== null) {
                if ($po[0] instanceof NmtProcurePo) {
                    $source = $po[0];
                }
            }
            
            if (! $source instanceof \Application\Entity\NmtProcurePo) {
                $errors[] = 'PO can\'t be empty!';
                $this->flashMessenger()->addMessage('Something wrong!');
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => null,
                    'source' => null,
                    'currency_list' => $currency_list
                ));
            }
            
            $validator = new Date();
            
            $currentState = $request->getPost('currentState');
            $warehouse_id = (int) $request->getPost('target_wh_id');
            
            $grDate = $request->getPost('grDate');
            $isActive = (int) $request->getPost('isActive');
            $remarks = $request->getPost('remarks');
            
            if ($isActive !== 1) {
                $isActive = 0;
            }
            
            $entity = new NmtProcureGr();
            
            $entity->setIsActive($isActive);
            // $entity->setGr($source);
            $entity->setCurrentState($currentState);
            
            if ($source->getVendor() instanceof \Application\Entity\NmtBpVendor) {
                $entity->setVendor($source->getVendor());
                $entity->setVendorName($source->getVendor()
                    ->getVendorName());
            } else {
                $errors[] = 'Vendor can\'t be empty. Please select a vendor!';
            }
            
            if ($source->getCurrency() instanceof \Application\Entity\NmtApplicationCurrency) {
                $entity->setCurrency($source->getCurrency());
                $entity->setCurrencyIso3($source->getCurrency()
                    ->getCurrency());
            } else {
                $errors[] = 'Currency can\'t be empty. Please select a currency!';
            }
            
            if (! $validator->isValid($grDate)) {
                $errors[] = 'Goods receipt Date is not correct or empty!';
            } else {
                
                // check if posting period is close
                /** @var \Application\Repository\NmtFinPostingPeriodRepository $p */
                $res = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod');
                
                /** @var \Application\Entity\NmtFinPostingPeriod $postingPeriod */
                $postingPeriod = $res->getPostingPeriod(new \DateTime($grDate));
                
                if ($postingPeriod instanceof \Application\Entity\NmtFinPostingPeriod) {
                    if ($postingPeriod->getPeriodStatus() == \Application\Model\Constants::PERIOD_STATUS_CLOSED) {
                        $errors[] = sprintf('Period "%s" is closed', $postingPeriod->getPeriodName());
                    } else {
                        $entity->setGrDate(new \DateTime($grDate));
                    }
                } else {
                    $errors[] = sprintf('Period for GR Date "%s" is not created yet', $grDate);
                }
            }
            
            $warehouse = null;
            if ($warehouse_id > 0) {
                $warehouse = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->find($warehouse_id);
            }
            
            if ($warehouse instanceof \Application\Entity\NmtInventoryWarehouse) {
                $entity->setWarehouse($warehouse);
            } else {
                $errors[] = 'Warehouse can\'t be empty. Please select a Wahrhouse!';
            }
            
            $entity->setRemarks($remarks);
            
            if (count($errors) > 0) {
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'source' => $source,
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
            
            $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_DRAFT);
            $entity->setTransactionStatus(\Application\Model\Constants::TRANSACTION_TYPE_PURCHASED);
            
            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                "email" => $this->identity()
            ));
            
            $entity->setCreatedBy($u);
            $entity->setCreatedOn(new \DateTime());
            $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
            
            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();
            
            // COPY open PO Row to GR document
            
            /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
            $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
            $po_rows = $res->getOpenPoGr($id, $token);
            
            if (count($po_rows > 0)) {
                $n = 0;
                foreach ($po_rows as $l) {
                    /** @var \Application\Entity\NmtProcurePoRow $l ; */
                    $r = $l[0];
                    
                    $n ++;
                    $row_tmp = new NmtProcureGrRow();
                    $row_tmp->setDocStatus($entity->getDocStatus());
                    $row_tmp->setTransactionType($entity->getTransactionStatus());
                    
                    $row_tmp->setGr($entity);
                    $row_tmp->setIsDraft(1);
                    $row_tmp->setIsActive(0);
                    
                    $row_tmp->setRowNumber($n);
                    
                    // $row_tmp->setRowIndentifer($entity->getSysNumber() . "-$n");
                    $row_tmp->setCurrentState("DRAFT");
                    $row_tmp->setPoRow($r);
                    $row_tmp->setPrRow($r->getPrRow());
                    $row_tmp->setItem($r->getItem());
                    $row_tmp->setQuantity($l['open_gr']);
                    $row_tmp->setUnit($r->getUnit());
                    $row_tmp->setUnitPrice($r->getUnitPrice());
                    $row_tmp->setCreatedBy($u);
                    $row_tmp->setCreatedOn(new \DateTime());
                    $row_tmp->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
                    $row_tmp->setRemarks("Ref: PO #" . $r->getRowIdentifer());
                    
                    $this->doctrineEM->persist($row_tmp);
                }
                $this->doctrineEM->flush();
            }
            
            $m = sprintf("[OK] Goods Receipt #%s created from P/O #%s", $entity->getSysNumber(), $source->getSysNumber());
            $this->flashMessenger()->addMessage($m);
            
            $redirectUrl = "/procure/gr/copy-from-po1?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
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
        
        $id = (int) $this->params()->fromQuery('source_id');
        $token = $this->params()->fromQuery('token');
        
        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        
        /**@var \Application\Entity\NmtProcurePo $source ;*/
        $po = $res->getPo($id, $token);
        
        $source = null;
        if ($po[0] instanceof NmtProcurePo) {
            $source = $po[0];
        }
        
        if (! $source instanceof \Application\Entity\NmtProcurePo) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        $po_rows = $res->getOpenPoGr($id, $token);
        
        if ($po_rows == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        if (count($po_rows) > 0) {
            
            $errors = array();
            
            $total_open_gr = 0;
            foreach ($po_rows as $r) {
                $total_open_gr = $total_open_gr + $r['open_gr'];
            }
            
            if ($total_open_gr == 0) {
                
                $errors[] = "All items of PO received!";
                
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => null,
                    'source' => $source,
                    'currency_list' => $currency_list
                ));
            }
        }
        
        $entity = new NmtProcureGr();
        $entity->setContractNo($source->getContractNo());
        $entity->setContractDate($source->getContractDate());
        
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
            'source' => $source,
            'currency_list' => $currency_list
        ));
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function copyFromPo1Action()
    {
        $this->layout("Procure/layout-fullscreen");
        
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
        
        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        $gr = $res->getGr($id, $token);
        
        if ($gr == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        if ($gr[0] instanceof \Application\Entity\NmtProcureGr) {
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'entity' => $gr[0],
                'errors' => null,
                'currency_list' => $currency_list,
                'total_row' => $gr['total_row']
                // 'active_row' => $invoice['active_row'],
                // 'max_row_number' => $invoice['total_row'],
                // 'net_amount' => $invoice['net_amount'],
                // 'tax_amount' => $invoice['tax_amount'],
                // 'gross_amount' => $invoice['gross_amount']
            ));
        }
        // return $this->redirect()->toRoute('access_denied');
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function reviewAction()
    {
        $this->layout("Procure/layout-fullscreen");
        
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
        
        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        $gr = $res->getGr($id, $token);
        
        if ($gr == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        if ($gr[0] instanceof \Application\Entity\NmtProcureGr) {
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'entity' => $gr[0],
                'errors' => null,
                'currency_list' => $currency_list,
                'total_row' => $gr['total_row']
                // 'active_row' => $invoice['active_row'],
                // 'max_row_number' => $invoice['total_row'],
                // 'net_amount' => $invoice['net_amount'],
                // 'tax_amount' => $invoice['tax_amount'],
                // 'gross_amount' => $invoice['gross_amount']
            ));
        }
        // return $this->redirect()->toRoute('access_denied');
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function postGrAction()
    {
        $request = $this->getRequest();
        
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        } else {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }
        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');
        
        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        $gr = $res->getGr($id, $token);
        
        if ($gr == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        if ($gr[0] instanceof \Application\Entity\NmtProcureGr) {
            
            /**@var \Application\Entity\NmtProcureGr $entity ;*/
            $entity = $gr[0];
            
            // UPDATE status
            $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_POSTED);
            $entity->setIsDraft(0);
            
            /**@var \Application\Entity\MlaUsers $u ;*/
            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                "email" => $this->identity()
            ));
            
            $createdOn = new \Datetime();
            
            // $entity->setCreatedBy($u);
            // $entity->setCreatedOn($changeOn);
            
            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();
            
            $m = sprintf('[OK] GR #%s - %s posted', $entity->getId(), $entity->getSysNumber());
            $this->flashMessenger()->addMessage($m);
            
            // Trigger: finance.activity.log. AbtractController is EventManagerAware.
            $this->getEventManager()->trigger('procure.activity.log', __METHOD__, array(
                'priority' => \Zend\Log\Logger::INFO,
                'message' => $m,
                'createdBy' => $u,
                'createdOn' => $createdOn,
                'entity_id' => $entity->getId(),
                'entity_class' => get_class($entity),
                'entity_token' => $entity->getToken()
            ));
            
            
           // CREATE STOCK GR
           // get gr_row.
            $criteria = array(
                'isActive' => 1,
                'gr' => $entity
            );
            $gr_rows = $this->doctrineEM->getRepository('Application\Entity\NmtProcureGrRow')->findBy($criteria);
            
            if(count($gr_rows)>0)
            {
                foreach($gr_rows as $r){
                    
                    /** @var \Application\Entity\NmtProcureGrRow $r ; */
                    
                    // UPDATE status
                    $r->setIsPosted(1);
                    $r->setIsDraft(0);
                    $r->setDocStatus($entity->getDocStatus());
                    
                    /**
                     * create procure good receipt.
                     * only for item controlled inventory
                     * ===================
                     */
                    $stock_gr_entity = new NmtInventoryTrx();
                    
                    $stock_gr_entity->setGrRow($r);
                    $stock_gr_entity->setTransactionType($r->getTransactionType());
                    $stock_gr_entity->setCurrentState($entity->getCurrentState());
                    $stock_gr_entity->setDocStatus($r->getDocStatus());
                    $stock_gr_entity->setIsActive($r->getIsActive());
                    
                    $stock_gr_entity->setVendor($entity->getVendor());
                    $stock_gr_entity->setFlow('IN');
                    
                    $stock_gr_entity->setItem($r->getItem());
                    $stock_gr_entity->setPrRow($r->getPrRow());
                    $stock_gr_entity->setPoRow($r->getPoRow());
                    $stock_gr_entity->setQuantity($r->getQuantity());
                    $stock_gr_entity->setVendorItemCode($r->getVendorItemCode());
                    $stock_gr_entity->setVendorItemUnit($r->getUnit());
                    $stock_gr_entity->setVendorUnitPrice($r->getUnitPrice());
                    $stock_gr_entity->setTrxDate($entity->getGrDate());
                    $stock_gr_entity->setCurrency($entity->getCurrency());
                    $stock_gr_entity->setRemarks('PO-GR '.$r->getRowIdentifer());
                    $stock_gr_entity->setWh($entity->getWarehouse());
                    $stock_gr_entity->setCreatedBy($u);
                    $stock_gr_entity->setCreatedOn(new \DateTime());
                    $stock_gr_entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
                    $stock_gr_entity->setChecksum(Rand::getString(32, self::CHAR_LIST, true));
                    
                    $stock_gr_entity->setTaxRate($r->getTaxRate());
                      
                    $this->doctrineEM->persist($stock_gr_entity);
                }
                
                $this->doctrineEM->flush();
                
            }
            
            // update P/O
            $message = $res->updatePOofGR($entity->getId());
              
            $redirectUrl = "/procure/gr/list";
            return $this->redirect()->toUrl($redirectUrl);
        }
        return $this->redirect()->toRoute('access_denied');
    }

    /**
     * adding new vendor invoce
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function addAction()
    {
        $this->layout("Procure/layout-fullscreen");
        
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
            $isActive = (int) $request->getPost('isActive');
            $remarks = $request->getPost('remarks');
            
            if ($isActive !== 1) {
                $isActive = 0;
            }
            
            $entity = new NmtProcurePo();
            
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
                $errors[] = 'Currency can\'t be empty. Please select a currency!';
            }
            
            $validator = new Date();
            
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
            
            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();
            $m = sprintf("[OK] Contract /PO: %s created!", $currentDoc);
            $this->flashMessenger()->addMessage($m);
            
            $redirectUrl = "/procure/po/add1?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
            return $this->redirect()->toUrl($redirectUrl);
        }
        
        // NOT POST ================================
        $redirectUrl = null;
        if ($request->getHeader('Referer') != null) {
            
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }
        
        $entity = new NmtProcurePo();
        
        $entity->setIsActive(1);
        
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
        
        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        $po = $res->getPo($id, $token);
        
        if ($po == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        $entity = null;
        if ($po[0] instanceof NmtProcurePo) {
            $entity = $po[0];
        }
        
        if ($entity !== null) {
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'entity' => $entity,
                'errors' => null,
                'currency_list' => $currency_list,
                'total_row' => $po['total_row'],
                'active_row' => $po['active_row'],
                'max_row_number' => $po['total_row'],
                'net_amount' => $po['net_amount'],
                'tax_amount' => $po['tax_amount'],
                'gross_amount' => $po['gross_amount']
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function add2Action()
    {
        $request = $this->getRequest();
        
        // accepted only ajax request
        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        $this->layout("layout/user/ajax");
        
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
        
        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        $po = $res->getPo($id, $token);
        
        if ($po == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        $entity = null;
        if ($po[0] instanceof NmtProcurePo) {
            $entity = $po[0];
        }
        
        if ($entity !== null) {
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'entity' => $entity,
                'errors' => null,
                'currency_list' => $currency_list,
                'total_row' => $po['total_row'],
                'active_row' => $po['active_row'],
                'max_row_number' => $po['total_row'],
                'net_amount' => $po['net_amount'],
                'tax_amount' => $po['tax_amount'],
                'gross_amount' => $po['gross_amount']
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
            
            /**@var \Application\Entity\NmtProcurePo $entity*/
            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo')->findOneBy($criteria);
            
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
                
                $isActive = (int) $request->getPost('isActive');
                $remarks = $request->getPost('remarks');
                
                if ($isActive !== 1) {
                    $isActive = 0;
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
                
                $this->flashMessenger()->addMessage('Document ' . $entity->getSysNumber() . ' is updated successfully!');
                
                /**
                 *
                 * @todo
                 */
                // update current state of po row
                $query = $this->doctrineEM->createQuery('
UPDATE Application\Entity\NmtProcurePoRow r SET r.currentState = :new_state WHERE r.po =:po_id
                    ')->setParameters(array(
                    'new_state' => $entity->getCurrentState(),
                    'po_id' => $entity->getId()
                ));
                $query->getResult();
                
                $criteria = array(
                    'isActive' => 1,
                    'po' => $entity->getId()
                );
                $sort_criteria = array();
                
                $po_rows = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePoRow')->findBy($criteria, $sort_criteria);
                
                // update current state of stock row.
                if (count($po_rows) > 0) {
                    foreach ($po_rows as $r) {
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
                
                $redirectUrl = "/procure/po/add1?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
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
        
        /**@var \Application\Entity\NmtProcurePo $entity*/
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo')->findOneBy($criteria);
        
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
        
        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        $list = $res->getGrList($is_active, $currentState, null, $sort_by, $sort, 0, 0);
        $total_records = count($list);
        $paginator = null;
        
        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            // $list = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findBy($criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
            $list = $res->getGrList($is_active, $currentState, null, $sort_by, $sort, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
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
