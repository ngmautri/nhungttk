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
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function showAction()
    {
         
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $currency_list = $nmtPlugin->currencyList();
        
        $request = $this->getRequest();
        
        if ($request->getHeader('Referer') == null) {
            // return $this->redirect()->toRoute('access_denied');
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
        
        $entity = null;
        if ($gr[0] instanceof NmtProcureGr) {
            $entity = $gr[0];
        }
        
        if (! $entity == null) {
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'entity' => $entity,
                'errors' => null,
                'currency_list' => $currency_list,
                'total_row' => $gr['total_row'],
                'active_row' => $gr['active_row'],
                'max_row_number' => $gr['total_row']
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     * Make GR from PO
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function copyFromPoAction()
    {
        $request = $this->getRequest();
        $this->layout("Procure/layout-fullscreen");
        
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $currency_list = $nmtPlugin->currencyList();
        
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));
        
        // Is Posting .................
        // ============================
        if ($request->isPost()) {
            
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
            
            $currentState = $request->getPost('currentState');
            $warehouse_id = (int) $request->getPost('target_wh_id');
            
            $grDate = $request->getPost('grDate');
            $isActive = (int) $request->getPost('isActive');
            $remarks = $request->getPost('remarks');
            
            if ($isActive !== 1) {
                $isActive = 0;
            }
            
            $entity = new NmtProcureGr();
            
            //unchangeable.
            //$entity->setDocType(\Application\Model\Constants::PROCURE_DOC_TYPE_GR);
            
            $entity->setIsActive($isActive);
            $entity->setCurrentState($currentState);
            
            if ($source->getVendor() instanceof \Application\Entity\NmtBpVendor) {
                $entity->setVendor($source->getVendor());
                $entity->setVendorName($source->getVendor()
                    ->getVendorName());
            } else {
                $errors[] = $nmtPlugin->translate('Vendor can\'t be empty. Please select a vendor!');
            }
            
            if ($source->getCurrency() instanceof \Application\Entity\NmtApplicationCurrency) {
                $entity->setCurrency($source->getCurrency());
                $entity->setCurrencyIso3($source->getCurrency()
                    ->getCurrency());
            } else {
                $errors[] = $nmtPlugin->translate('Currency can\'t be empty. Please select a currency!');
            }
            
            $validator = new Date();
            
            // check one more time when posted.
            if ($grDate !== null) {
                if (! $validator->isValid($grDate)) {
                    $errors[] = $nmtPlugin->translate('Goods receipt Date is not correct or empty!');
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
            // Saving into Database..........
            // ++++++++++++++++++++++++++++++
            
            // $entity->setSysNumber($nmtPlugin->getDocNumber($entity));
            $entity->setSysNumber(\Application\Model\Constants::SYS_NUMBER_UNASSIGNED);
            
            $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_DRAFT);
            //$entity->setTransactionStatus(\Application\Model\Constants::TRANSACTION_TYPE_PURCHASED);
            $entity->setIsDraft(1);
            $entity->setIsPosted(0);
            
            $createdOn = new \DateTime();
            
            $entity->setCreatedBy($u);
            $entity->setCreatedOn($createdOn);
            $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
            
            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();
            
            // COPY open PO Row to GR Row
            
            /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
            $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
            $po_rows = $res->getPOStatus($id, $token);
            
            if (count($po_rows > 0)) {
                $n = 0;
                foreach ($po_rows as $l) {
                    /** @var \Application\Entity\NmtProcurePoRow $l ; */
                    $r = $l[0];
                    
                    $n ++;
                    $row_tmp = new NmtProcureGrRow();
                    $row_tmp->setDocStatus($entity->getDocStatus());
                    
                    // Goods receipt, Invoice Not receipt
                    $row_tmp->setTransactionType(\Application\Model\Constants::PROCURE_TRANSACTION_TYPE_GRNI);
                    $row_tmp->setTransactionStatus(\Application\Model\Constants::PROCURE_TRANSACTION_STATUS_PENDING);
                    
                    $row_tmp->setGr($entity);
                    $row_tmp->setIsDraft(1);
                    $row_tmp->setIsPosted(0);
                    $row_tmp->setIsActive(1);
                    $row_tmp->setCurrentState("DRAFT");
                    
                    $row_tmp->setPoRow($r);
                    $row_tmp->setPrRow($r->getPrRow());
                    $row_tmp->setItem($r->getItem());
                    
                    $row_tmp->setQuantity($l['open_gr_qty']);
                    
                    $row_tmp->setUnit($r->getUnit());
                    $row_tmp->setUnitPrice($r->getUnitPrice());
                    $row_tmp->setTaxRate($r->getTaxRate());
                    
                    $netAmount = $row_tmp->getQuantity() * $row_tmp->getUnitPrice();
                    $taxAmount = $netAmount * $row_tmp->getTaxRate() / 100;
                    $grossAmount = $netAmount + $taxAmount;
                    
                    $row_tmp->setNetAmount($netAmount);
                    $row_tmp->setTaxAmount($taxAmount);
                    $row_tmp->setGrossAmount($grossAmount);
                    
                    $row_tmp->setCreatedBy($u);
                    $row_tmp->setCreatedOn($createdOn);
                    
                    $row_tmp->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
                    $row_tmp->setRemarks("Ref: PO #" . $r->getRowIdentifer());
                    
                    $this->doctrineEM->persist($row_tmp);
                }
                $this->doctrineEM->flush();
            }
            
            $m = sprintf("[OK] Goods Receipt #%s created from P/O #%s", $entity->getSysNumber(), $source->getSysNumber());
            $this->flashMessenger()->addMessage($m);
            
            $redirectUrl = "/procure/gr/review?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
            return $this->redirect()->toUrl($redirectUrl);
        }
        
        // NO POST
        // Initiate.....................
        // ==============================
        
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
        
        $po_rows = $res->getPOStatus($id, $token);
        
        if ($po_rows == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        if (count($po_rows) > 0) {
            
            $errors = array();
            $total_open_gr = 0;
            $total_draft_gr = 0;
            
            foreach ($po_rows as $r) {
                $total_open_gr = $total_open_gr + $r['open_gr_qty'];
                $total_draft_gr = $total_draft_gr + $r['draft_gr_qty'];
            }
            
            if ($total_draft_gr > 0) {
                
                $redirectUrl = '/procure/gr/list';
                $m = sprintf("[INFO] There is draft GR for PO #. Pls review it!", $source->getSysNumber());
                $this->flashMessenger()->addMessage($m);
                
                return $this->redirect()->toUrl($redirectUrl);
            }
            
            if ($total_open_gr == 0) {
                
                $redirectUrl = sprintf('/procure/po/add1?token=%s&entity_id=%s', $source->getToken(), $source->getId());
                $m = sprintf("[INFO] Items of PO # received fully!", $source->getSysNumber());
                $this->flashMessenger()->addMessage($m);
                
                return $this->redirect()->toUrl($redirectUrl);
            }
        }
        
        $entity = new NmtProcureGr();
        $entity->setContractNo($source->getContractNo());
        $entity->setContractDate($source->getContractDate());
        $entity->setIsActive(1);
        
        // $entity->setSysNumber($nmtPlugin->getDocNumber($entity));
        $entity->setSysNumber(\Application\Model\Constants::SYS_NUMBER_UNASSIGNED);
        
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
     * @deprecated
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
        $request = $this->getRequest();
        
        $this->layout("Procure/layout-fullscreen");
        
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $currency_list = $nmtPlugin->currencyList();
        
        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));
        
        $default_cur = null;
        if ($u->getCompany() instanceof \Application\Entity\NmtApplicationCompany) {
            $default_cur = $u->getCompany()->getDefaultCurrency();
        }
        
        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        
        // Do Posting .................
        // ============================
        if ($request->isPost()) {
            
            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');
            $id = (int) $request->getPost('entity_id');
            $token = $request->getPost('token');
            
            $gr = $res->getGr($id, $token);
            
            if ($gr == null) {
                return $this->redirect()->toRoute('access_denied');
            }
            
            if ($gr[0] instanceof \Application\Entity\NmtProcureGr) {
                
                /**@var \Application\Entity\NmtProcureGr $entity ;*/
                $entity = $gr[0];
                $oldEntity = clone ($entity);
                
                $grDate = $request->getPost('grDate');
                $currentState = $request->getPost('currentState');
                $vendor_id = (int) $request->getPost('vendor_id');
                $currency_id = (int) $request->getPost('currency_id');
                
                $warehouse_id = (int) $request->getPost('target_wh_id');
                $exchangeRate = (double) $request->getPost('exchangeRate');
                
                $isActive = (int) $request->getPost('isActive');
                $remarks = $request->getPost('remarks');
                
                if ($isActive != 1) {
                    $isActive = 0;
                }
                
                $entity->setIsActive($isActive);
                $entity->setCurrentState($currentState);
                
                $vendor = null;
                if ($vendor_id > 0) {
                    /** @var \Application\Entity\NmtBpVendor $vendor ; */
                    $vendor = $this->doctrineEM->getRepository('Application\Entity\NmtBpVendor')->find($vendor_id);
                }
                
                if ($vendor instanceof \Application\Entity\NmtBpVendor) {
                    $entity->setVendor($vendor);
                    $entity->setVendorName($vendor->getVendorName());
                } else {
                    $errors[] = $nmtPlugin->translate('Vendor can\'t be empty. Please select a vendor!');
                }
                
                /**
                 * Check default currency
                 */
                if (! $default_cur instanceof \Application\Entity\NmtApplicationCurrency) {
                    $errors[] = 'Company currency can\'t be defined!';
                }
                
                $currency = null;
                if ($currency_id > 0) {
                    /** @var \Application\Entity\NmtApplicationCurrency  $currency ; */
                    $currency = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->find($currency_id);
                }
                
                // check if posting period is close
                /** @var \Application\Repository\NmtFinPostingPeriodRepository $p */
                $p = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod');
                
                if ($currency instanceof \Application\Entity\NmtApplicationCurrency) {
                    $entity->setCurrency($currency);
                    $entity->setCurrencyIso3($currency->getCurrency());
                    
                    if ($currency == $default_cur) {
                        $entity->setExchangeRate(1);
                    } else {
                        
                        if ($exchangeRate != 0) {
                            if (! is_numeric($exchangeRate)) {
                                $errors[] = $nmtPlugin->translate('FX rate is not valid. It must be a number.');
                            } else {
                                if ($exchangeRate <= 0) {
                                    $errors[] = $nmtPlugin->translate('FX rate must be greate than 0!');
                                }
                                $entity->setExchangeRate($exchangeRate);
                            }
                        } else {
                            // get default exchange rate.
                            /** @var \Application\Entity\FinFx $lastest_fx */
                            $lastest_fx = $p->getLatestFX($currency_id, $default_cur->getId());
                            if ($lastest_fx instanceof \Application\Entity\FinFx) {
                                $entity->setExchangeRate($lastest_fx->getFxRate());
                            } else {
                                $errors[] = sprintf('FX rate for %s not definded yet!', $currency->getCurrency());
                            }
                        }
                    }
                } else {
                    $errors[] = $nmtPlugin->translate('Currency can\'t be empty. Please select a Currency!');
                }
                
                $validator = new Date();
                
                if (! $validator->isValid($grDate)) {
                    $errors[] = $nmtPlugin->translate('Goods receipt Date is not correct or empty!');
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
                
                // No ERROR
                // Saving into Database..........
                // ++++++++++++++++++++++++++++++
                
                $changeOn = new \DateTime();
                $oldEntity = clone ($entity);
                
                // Assign doc number
                if ($entity->getSysNumber() == \Application\Model\Constants::SYS_NUMBER_UNASSIGNED) {
                    $entity->setSysNumber($nmtPlugin->getDocNumber($entity));
                }
                
                //set posted
                $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_POSTED);
                
                $entity->setRevisionNo($entity->getRevisionNo() + 1);
                $entity->setLastchangeBy($u);
                $entity->setLastchangeOn($changeOn);
                $this->doctrineEM->persist($entity);
                $this->doctrineEM->flush();
                
                $criteria = array(
                    'isActive' => 1,
                    'gr' => $entity
                );
                $ap_rows = $this->doctrineEM->getRepository('Application\Entity\NmtProcureGrRow')->findBy($criteria);
                
                $n = 0;
                foreach ($ap_rows as $r) {
                    
                    /** @var \Application\Entity\NmtProcureGrRow $r ; */
                    
                    // ignore row with 0 quantity
                    if ($r->getQuantity() == 0) {
                        $r->setIsActive(0);
                        continue;
                    }
                    
                    $createdOn = new \DateTime();
                    
                    $netAmount = $r->getQuantity() * $r->getUnitPrice();
                    $taxAmount = $netAmount * $r->getTaxRate() / 100;
                    $grossAmount = $netAmount + $taxAmount;
                    
                    // UPDATE status
                    $n ++;
                    $r->setIsPosted(1);
                    $r->setIsDraft(0);
                    
                    $r->setNetAmount($netAmount);
                    $r->setTaxAmount($taxAmount);
                    $r->setGrossAmount($grossAmount);
                    
                    // transaction type is not changeable.
                    //$r->setTransactionType(\Application\Model\Constants::PROCURE_TRANSACTION_TYPE_GRNI);
                    //$r->setTransactionType($entity->getTransactionType());
                    
                    $r->setDocStatus($entity->getDocStatus());
                    $r->setRowIdentifer($entity->getSysNumber() . '-' . $n);
                    $r->setRowNumber($n);
                    $this->doctrineEM->persist($r);
                    
                    /**
                     * create procure good receipt.
                     * only for item controlled inventory
                     * ===================
                     */
                    
                    /**
                     *
                     * @todo: only for item with stock control.
                     */
                    if ($r->getItem()->getIsStocked() == 0) {
                        // continue;
                    }
                    
                    $criteria = array(
                        'isActive' => 1,
                        'grRow' => $r
                    );
                    $stock_gr_entity_ck = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryTrx')->findOneBy($criteria);
                    
                    if ($stock_gr_entity_ck instanceof \Application\Entity\NmtInventoryTrx) {
                        $stock_gr_entity = $stock_gr_entity_ck;
                    } else {
                        $stock_gr_entity = new NmtInventoryTrx();
                    }
                    
                    $stock_gr_entity->setIsActive(1);
                    $stock_gr_entity->setTrxDate($entity->getGrDate());
                    
                    $stock_gr_entity->setGr($entity);
                    $stock_gr_entity->setGrRow($r);
                    
                    $stock_gr_entity->setItem($r->getItem());
                    
                    $stock_gr_entity->setPrRow($r->getPrRow());
                    $stock_gr_entity->setPoRow($r->getPoRow());
                    
                    $stock_gr_entity->setIsDraft($r->getIsDraft());
                    $stock_gr_entity->setIsPosted($r->getIsPosted());
                    $stock_gr_entity->setDocStatus($r->getDocStatus());
                    
                    //get from gr-row.
                    $stock_gr_entity->setTransactionType($r->getTransactionType());
                    
                    $stock_gr_entity->setSourceClass(get_class($r));
                    $stock_gr_entity->setSourceId($r->getId());
                    
                    $stock_gr_entity->setTransactionType($r->getTransactionType());
                    $stock_gr_entity->setCurrentState($entity->getCurrentState());
                    
                    $stock_gr_entity->setVendor($entity->getVendor());
                    $stock_gr_entity->setFlow(\Application\Model\Constants::WH_TRANSACTION_IN);
                    
                    $stock_gr_entity->setQuantity($r->getQuantity());
                    $stock_gr_entity->setVendorItemCode($r->getVendorItemCode());
                    $stock_gr_entity->setVendorItemUnit($r->getUnit());
                    $stock_gr_entity->setVendorUnitPrice($r->getUnitPrice());
                    $stock_gr_entity->setTrxDate($entity->getGrDate());
                    $stock_gr_entity->setCurrency($entity->getCurrency());
                    $stock_gr_entity->setTaxRate($r->getTaxRate());
                    
                    $stock_gr_entity->setRemarks('GR Row' . $r->getRowIdentifer());
                    $stock_gr_entity->setWh($entity->getWarehouse());
                    $stock_gr_entity->setCreatedBy($u);
                    $stock_gr_entity->setCreatedOn($createdOn);
                    $stock_gr_entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
                    $this->doctrineEM->persist($stock_gr_entity);
                    
                    /**@todo create serial number*/
                    if($r->getItem()!==null){
                        if($r->getItem()->getMonitoredBy()==\Application\Model\Constants::ITEM_WITH_SERIAL_NO){
                            
                            for ($i = 0; $i < $r->getQuantity(); $i++) {
                               
                                //create serial number
                                $sn_entity = new \Application\Entity\NmtInventoryItemSerial();
                                $sn_entity->setItem($r->getItem());
                                $sn_entity->setInventoryTrx($stock_gr_entity);
                                $sn_entity->setIsActive(1);
                                
                                $sn_entity->setSysNumber($nmtPlugin->getDocNumber($sn_entity));
                                $sn_entity->setCreatedBy($u);
                                $sn_entity->setCreatedBy($u);
                                $sn_entity->setCreatedOn($createdOn);
                                $sn_entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
                                $this->doctrineEM->persist($sn_entity);
                                
                            } 
                            
                            
                        }
                    }
                    
                }
                
                $this->doctrineEM->flush();
                
                /**
                 * @ update relevant PR & PO
                 */
                
                /**
                 *
                 * @todo Create Entry Journal
                 * debit: 
                 * credit: other payables.
                 *      
                 */
                
                // LOGGING
                /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
                $nmtPlugin = $this->Nmtplugin();
                $changeArray = $nmtPlugin->objectsAreIdentical($oldEntity, $entity);
                
                $m = sprintf('[OK] GR #%s - %s posted.', $entity->getId(), $entity->getSysNumber());
                
                // Trigger Change Log. AbtractController is EventManagerAware.
                $this->getEventManager()->trigger('procure.change.log', __METHOD__, array(
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
                
                $this->flashMessenger()->addMessage($m);
                // $redirectUrl = "/finance/v-invoice/show?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
                $redirectUrl = "/procure/gr/list";
                return $this->redirect()->toUrl($redirectUrl);
            }
        }
        
        // NO POST
        // Initiate.....................
        // ==============================
        if ($request->getHeader('Referer') == null) {
            $redirectUrl = null;
            return $this->redirect()->toRoute('access_denied');
        } else {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }
        
        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');
        
        $gr = $res->getGr($id, $token);
        
        $entity = null;
        if ($gr[0] instanceof NmtProcureGr) {
            $entity = $gr[0];
        }
        
        if ($entity instanceof NmtProcureGr) {
            
            if ($entity->getDocStatus() == \Application\Model\Constants::DOC_STATUS_POSTED) {
                $m = sprintf('GR #%s - %s already posted!', $entity->getId(), $entity->getSysNumber());
                $this->flashMessenger()->addMessage($m);
                $redirectUrl = "/procure/gr/show?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
                return $this->redirect()->toUrl($redirectUrl);
            }
            
            if ($gr['active_row'] == 0) {
                $m = sprintf('[INFO] GR #%s has no lines.', $entity->getSysNumber());
                $m1 = $nmtPlugin->translate('Document is incomplete!');
                $this->flashMessenger()->addMessage($m1);
                $redirectUrl = "/procure/gr-row/add?token=" . $entity->getToken() . "&target_id=" . $entity->getId();
                return $this->redirect()->toUrl($redirectUrl);
            }
            
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'entity' => $entity,
                'errors' => null,
                'currency_list' => $currency_list,
                'total_row' => $gr['total_row'],
                'active_row' => $gr['active_row'],
                'max_row_number' => $gr['total_row']
            ));
        } else {
            // return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @deprecated POST GR
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
            $oldEntity = clone ($entity);
            
            // UPDATE status
            
            /**@var \Application\Entity\MlaUsers $u ;*/
            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                "email" => $this->identity()
            ));
            
            $lastchangeOn = new \Datetime();
            
            $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_POSTED);
            $entity->setIsDraft(0);
            $entity->getLastchangeBy($u);
            $entity->setLastchangeOn($lastchangeOn);
            
            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();
            
            $m = sprintf('[OK] GR #%s - %s posted', $entity->getId(), $entity->getSysNumber());
            $this->flashMessenger()->addMessage($m);
            
            // LOGGING
            
            // Trigger: finance.activity.log. AbtractController is EventManagerAware.
            $this->getEventManager()->trigger('procure.activity.log', __METHOD__, array(
                'priority' => \Zend\Log\Logger::INFO,
                'message' => $m,
                'createdBy' => $u,
                'createdOn' => $lastchangeOn,
                'entity_id' => $entity->getId(),
                'entity_class' => get_class($entity),
                'entity_token' => $entity->getToken()
            ));
            
            /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
            $nmtPlugin = $this->Nmtplugin();
            $changeArray = $nmtPlugin->objectsAreIdentical($oldEntity, $entity);
            
            $m = sprintf('[OK] GR #%s - %s posted.', $entity->getId(), $entity->getSysNumber());
            
            // Trigger Change Log. AbtractController is EventManagerAware.
            $this->getEventManager()->trigger('procure.change.log', __METHOD__, array(
                'priority' => 7,
                'message' => $m,
                'objectId' => $entity->getId(),
                'objectToken' => $entity->getToken(),
                'changeArray' => $changeArray,
                'changeBy' => $u,
                'changeOn' => $lastchangeOn,
                'revisionNumber' => $entity->getRevisionNo(),
                'changeDate' => $lastchangeOn,
                'changeValidFrom' => $lastchangeOn
            ));
            
            // UPDATE GR ROW & CREATE STOCK GR
            $criteria = array(
                'isActive' => 1,
                'gr' => $entity
            );
            $gr_rows = $this->doctrineEM->getRepository('Application\Entity\NmtProcureGrRow')->findBy($criteria);
            
            if (count($gr_rows) > 0) {
                $n = 0;
                foreach ($gr_rows as $r) {
                    /** @var \Application\Entity\NmtProcureGrRow $r ; */
                    
                    // UPDATE status
                    $n ++;
                    $r->setIsPosted(1);
                    $r->setIsDraft(0);
                    $r->setDocStatus($entity->getDocStatus());
                    $r->setTransactionType(\Application\Model\Constants::PROCURE_TRANSACTION_TYPE_GRNI);                    
                    $r->setRowIdentifer($entity->getSysNumber() . '-' . $n);
                    $r->setRowNumber($n);
                    
                    /**
                     * create procure good receipt.
                     * only for item controlled inventory
                     * ===================
                     */
                    
                    /**
                     *
                     * @todo: only for item in stock.
                     */
                    if ($r->getItem()->getIsStocked() == 0) {
                        // continue;
                    }
                    
                    $criteria = array(
                        'isActive' => 1,
                        'grRow' => $r
                    );
                    $stock_gr_entity_ck = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryTrx')->findOneBy($criteria);
                    
                    $stock_gr_entity = null;
                    
                    if ($stock_gr_entity_ck instanceof \Application\Entity\NmtInventoryTrx) {
                        $stock_gr_entity = $stock_gr_entity_ck;
                    } else {
                        $stock_gr_entity = new NmtInventoryTrx();
                    }
                    
                    $stock_gr_entity->setGrRow($r);
                    
                    $stock_gr_entity->setSourceClass(get_class($r));
                    $stock_gr_entity->setSourceId($r->getId());
                    
                    $stock_gr_entity->setTransactionType($r->getTransactionType());
                    $stock_gr_entity->setCurrentState($entity->getCurrentState());
                    $stock_gr_entity->setDocStatus($r->getDocStatus());
                    $stock_gr_entity->setIsActive($r->getIsActive());
                    
                    $stock_gr_entity->setVendor($entity->getVendor());
                    $stock_gr_entity->setFlow(\Application\Model\Constants::WH_TRANSACTION_IN);
                    
                    $stock_gr_entity->setItem($r->getItem());
                    $stock_gr_entity->setPrRow($r->getPrRow());
                    $stock_gr_entity->setPoRow($r->getPoRow());
                    $stock_gr_entity->setQuantity($r->getQuantity());
                    $stock_gr_entity->setVendorItemCode($r->getVendorItemCode());
                    $stock_gr_entity->setVendorItemUnit($r->getUnit());
                    $stock_gr_entity->setVendorUnitPrice($r->getUnitPrice());
                    $stock_gr_entity->setTrxDate($entity->getGrDate());
                    $stock_gr_entity->setCurrency($entity->getCurrency());
                    $stock_gr_entity->setRemarks('PO Gr ' . $r->getRowIdentifer());
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
            
            // Trigger Change Log. AbtractController is EventManagerAware.
            /*
             * $this->getEventManager()->trigger('procure.gr.post', __METHOD__, array(
             * 'entity' => $entity,
             * ));
             */
            
            // update P/O
            $res->updatePOofGR($entity->getId());
            
            $redirectUrl = "/procure/gr/list";
            return $this->redirect()->toUrl($redirectUrl);
        }
        return $this->redirect()->toRoute('access_denied');
    }

    /**
     * Adding new GR
     * Good Receipt First, Invoice Late
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function addAction()
    {
        $request = $this->getRequest();
        $this->layout("Procure/layout-fullscreen");
        
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $currency_list = $nmtPlugin->currencyList();
        
        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));
        
        $default_cur = null;
        if ($u->getCompany() instanceof \Application\Entity\NmtApplicationCompany) {
            $default_cur = $u->getCompany()->getDefaultCurrency();
        }
        
        // Is Posing
        // =============================
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
            
            if ($isActive != 1) {
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
            
            if ($vendor instanceof \Application\Entity\NmtBpVendor) {
                $entity->setVendor($vendor);
                $entity->setVendorName($vendor->getVendorName());
            } else {
                $errors[] = $nmtPlugin->translate('Vendor can\'t be empty. Please select a vendor!');
            }
            
            $currency = null;
            if ($currency_id > 0) {
                /** @var \Application\Entity\NmtApplicationCurrency  $currency ; */
                $currency = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->find($currency_id);
            }
            
            /**
             *
             * @todo might be problem with proxy class.
             */
            if ($currency !== null) {
                $entity->setCurrency($currency);
                $entity->setCurrencyIso3($currency->getCurrency());
            } else {
                $errors[] = 'Currency can\'t be empty. Please select a currency!';
            }
            
            $entity->setContractNo($contractNo);
            $validator = new Date();
            
            if ($contractDate !== null) {
                if (! $validator->isValid($contractDate)) {
                    $errors[] = 'Contract Date is not correct or empty!';
                } else {
                    $entity->setContractDate(new \DateTime($contractDate));
                }
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
            // Saving into Database..........
            // ++++++++++++++++++++++++++++++
            
            // assign documber while posting.
            $entity->setSysNumber(\Application\Model\Constants::SYS_NUMBER_UNASSIGNED);
            
            $createdOn = new \DateTime();
            $entity->setCreatedBy($u);
            
            $entity->setCreatedOn($createdOn);
            $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
            
            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();
            $m = sprintf("[OK] Contract /PO: %s created!", $currentDoc);
            $this->flashMessenger()->addMessage($m);
            
            $redirectUrl = "/procure/gr/review?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
            return $this->redirect()->toUrl($redirectUrl);
        }
        
        // NO POST
        // Initiate ......................
        // ================================
        
        $redirectUrl = null;
        if ($request->getHeader('Referer') !== null) {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }
        
        $entity = new NmtProcureGr();
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
     * @deprecated
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
    public function listOfPOAction()
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
