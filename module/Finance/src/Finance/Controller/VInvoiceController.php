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
 * 02/07
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class VInvoiceController extends AbstractActionController
{

    const CHAR_LIST = "_0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";

    protected $doctrineEM;

    /**
     * Adding new vendor invoce
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function addAction()
    {
        $request = $this->getRequest();
        $this->layout("Finance/layout-fullscreen");
        
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
            $exchangeRate = (double) $request->getPost('exchangeRate');
            
            $warehouse_id = (int) $request->getPost('target_wh_id');
            
            $postingDate = $request->getPost('postingDate');
            $grDate = $request->getPost('grDate');
            $invoiceDate = $request->getPost('invoiceDate');
            $invoiceNo = $request->getPost('invoiceNo');
            
            $sapDoc = $request->getPost('sapDoc');
            $isActive = (int) $request->getPost('isActive');
            $remarks = $request->getPost('remarks');
            
            $entity = new FinVendorInvoice();
            if ($isActive !== 1) {
                $isActive = 0;
            }
            
            if ($sapDoc == "") {
                $sapDoc = "N/A";
            }
            $entity->setSapDoc($sapDoc);
            
            $entity->setTransactionType(\Application\Model\Constants::TRANSACTION_TYPE_PURCHASED);
            $entity->setIsActive($isActive);
            $entity->setCurrentState($currentState);
            $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_DRAFT);
            
            // will check later.
            $entity->setInvoiceNo($invoiceNo);
            
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
                $errors[] = $nmtPlugin->translate('Company currency can\'t be defined!');
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
                    
                    if ($exchangeRate !== 0) {
                        if (! is_numeric($exchangeRate)) {
                            $errors[] = $nmtPlugin->translate('Foreign exchange rate is not valid. It must be a number.');
                        } else {
                            if ($exchangeRate <= 0) {
                                $errors[] = $nmtPlugin->translate('Foreign exchange rate must be greate than 0!');
                            }
                            $entity->setExchangeRate($exchangeRate);
                        }
                    } else {
                        // get default exchange rate.
                        /** @var \Application\Entity\FinFx $lastest_fx */
                        
                        $lastest_fx = $p->getLatestFX($currency_id, $default_cur->getId());
                        if ($lastest_fx !== null) {
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
            
            // check one more time while posting
            if (! $invoiceDate == null) {
                if (! $validator->isValid($invoiceDate)) {
                    $errors[] = $nmtPlugin->translate('Invoice Date is not valid');
                } else {
                    $entity->setInvoiceDate(new \DateTime($invoiceDate));
                }
            }
            
            // check one more time while posting
            if (! $postingDate == null) {
                if (! $validator->isValid($postingDate)) {
                    $errors[] = $nmtPlugin->translate('Posting Date is not valid!');
                } else {
                    // no check, of period is closed.
                    $entity->setPostingDate(new \DateTime($postingDate));
                }
            }
            
            // check one more time
            if (! $grDate == null) {
                if (! $validator->isValid($grDate)) {
                    $errors[] = $nmtPlugin->translate('Good receipt Date is not valid!');
                } else {
                    // No check, if period is closed.
                    $entity->setGrDate(new \DateTime($grDate));
                }
            }
            
            $warehouse = null;
            if ($warehouse_id > 0) {
                $warehouse = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->find($warehouse_id);
            }
            
            if ($warehouse instanceof \Application\Entity\NmtInventoryWarehouse) {
                $entity->setWarehouse($warehouse);
            } else {
                // check when posting
                // $errors[] = 'Warehouse can\'t be empty. Please select a vendor!';
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
            
            $entity->setSysNumber(\Application\Model\Constants::SYS_NUMBER_UNASSIGNED);
            
            $createdOn = new \DateTime();
            
            $entity->setCreatedBy($u);
            $entity->setCreatedOn($createdOn);
            $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
            
            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();
            
            $m = sprintf('[OK] A/P Invoice #%s created', $entity->getId());
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
            
            // $redirectUrl = "/finance/v-invoice/add1?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
            $redirectUrl = "/finance/v-invoice-row/add?token=" . $entity->getToken() . "&target_id=" . $entity->getId();
            
            return $this->redirect()->toUrl($redirectUrl);
        }
        
        // NO POST
        // Initiate ......................
        // ================================
        $redirectUrl = null;
        if ($request->getHeader('Referer') != null) {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }
        
        $entity = new FinVendorInvoice();
        $entity->setIsActive(1);
        
        // Default currency
        if ($default_cur instanceof \Application\Entity\NmtApplicationCurrency) {
            $entity->setCurrency($default_cur);
        }
        
        $entity->setCurrentState('finalInvoice');
        
        $default_wh = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->findOneBy(array(
            'isDefault' => 1
        ));
        
        if ($default_wh !== null) {
            $entity->setWarehouse($default_wh);
        }
        $entity->setSysNumber(\Application\Model\Constants::SYS_NUMBER_UNASSIGNED);
        
        return new ViewModel(array(
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'currency_list' => $currency_list
        ));
    }

    /**
     *
     * @deprecated
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
        
        /**@var \Application\Repository\FinVendorInvoiceRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice');
        
        // Is Posting .................
        // ============================
        if ($request->isPost()) {
            
            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');
            $id = (int) $request->getPost('entity_id');
            $token = $request->getPost('token');
            
            $invoice = $res->getVendorInvoice($id, $token);
            
            $entity = null;
            if ($invoice[0] instanceof FinVendorInvoice) {
                $entity = $invoice[0];
            }
            
            if (! $entity instanceof FinVendorInvoice) {
                return $this->redirect()->toRoute('access_denied');
            }
            
            // ========================
            
            if ($invoice['active_row'] == 0) {
                $m = sprintf('[INFO] AP Invoice #%s has no lines.', $entity->getSysNumber());
                $m1 = $nmtPlugin->translate('Document is incomplete!');
                $this->flashMessenger()->addMessage($m1);
                
                $redirectUrl = "/finance/v-invoice/review?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
                return $this->redirect()->toUrl($redirectUrl);
            }
            
            $contractDate = $request->getPost('contractDate');
            $contractNo = $request->getPost('contractNo');
            $currentState = $request->getPost('currentState');
            
            $vendor_id = (int) $request->getPost('vendor_id');
            $currency_id = (int) $request->getPost('currency_id');
            $exchangeRate = (double) $request->getPost('exchangeRate');
            $warehouse_id = (int) $request->getPost('target_wh_id');
            $postingDate = $request->getPost('postingDate');
            $grDate = $request->getPost('grDate');
            $invoiceDate = $request->getPost('invoiceDate');
            $invoiceNo = $request->getPost('invoiceNo');
            $sapDoc = $request->getPost('sapDoc');
            $isActive = (int) $request->getPost('isActive');
            $remarks = $request->getPost('remarks');
            
            if ($isActive != 1) {
                $isActive = 0;
            }
            $entity->setIsActive($isActive);
            
            if ($sapDoc == "") {
                $sapDoc = "N/A";
            }
            $entity->setSapDoc($sapDoc);
            
            $vendor = null;
            if ($vendor_id > 0) {
                /** @var \Application\Entity\NmtBpVendor $vendor ; */
                $vendor = $this->doctrineEM->getRepository('Application\Entity\NmtBpVendor')->find($vendor_id);
            }
            
            if ($vendor instanceof \Application\Entity\NmtBpVendor) {
                $entity->setVendor($vendor);
                $entity->setVendorName($vendor->getVendorName());
            } else {
                $errors[] = 'Vendor can\'t be empty. Please select a vendor!';
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
                                $errors[] = 'FX rate must be greate than 0!';
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
            
            if ($invoiceNo == null) {
                $errors[] = $nmtPlugin->translate('Please enter Invoice Number!');
            } else {
                $entity->setInvoiceNo($invoiceNo);
            }
            
            if (! $validator->isValid($invoiceDate)) {
                $errors[] = $nmtPlugin->translate('Invoice Date is not correct or empty!');
            } else {
                $entity->setInvoiceDate(new \DateTime($invoiceDate));
            }
            
            if (! $validator->isValid($postingDate)) {
                $errors[] = $nmtPlugin->translate('Posting Date is not correct or empty!');
            } else {
                
                $entity->setPostingDate(new \DateTime($postingDate));
                
                /** @var \Application\Entity\NmtFinPostingPeriod $postingPeriod */
                $postingPeriod = $p->getPostingPeriod(new \DateTime($postingDate));
                
                if (! $postingPeriod instanceof \Application\Entity\NmtFinPostingPeriod) {
                    $errors[] = sprintf('Posting period for [%s] not created!', $postingDate);
                } else {
                    if ($postingPeriod->getPeriodStatus() == \Application\Model\Constants::PERIOD_STATUS_CLOSED) {
                        $errors[] = sprintf('Posting period [%s] is closed!', $postingPeriod->getPeriodName());
                    }
                }
            }
            
            if (! $validator->isValid($grDate)) {
                $errors[] = $nmtPlugin->translate('Good receipt Date is not correct or empty!');
            } else {
                
                // check if posting period is close
                /** @var \Application\Entity\NmtFinPostingPeriod $postingPeriod */
                $postingPeriod = $p->getPostingPeriod(new \DateTime($grDate));
                
                if (! $postingPeriod instanceof \Application\Entity\NmtFinPostingPeriod) {
                    $errors[] = sprintf('Posting period for [%s] not created!', $grDate);
                } else {
                    if ($postingPeriod->getPeriodStatus() == \Application\Model\Constants::PERIOD_STATUS_CLOSED) {
                        $errors[] = sprintf('Period [%s] is closed for Good receipt!', $postingPeriod->getPeriodName());
                    } else {
                        $entity->setGrDate(new \DateTime($grDate));
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
                $errors[] = $nmtPlugin->translate('Warehouse can\'t be empty. Please select a warehouse!');
            }
            
            $entity->setRemarks($remarks);
            
            if (count($errors) > 0) {
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'entity' => $entity,
                    'errors' => $errors,
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
            }
            
            // No ERROR
            // Saving into Database..........
            // ++++++++++++++++++++++++++++++
            
            $changeOn = new \DateTime();
            $oldEntity = clone ($entity);
            
            // Assign doc number
            if ($entity->getSysNumber() == \Application\Model\Constants::SYS_NUMBER_UNASSIGNED) {
                $entity->setSysNumber($nmtPlugin->getDocNumber($entity));
            }
            
            $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_POSTED);
            $entity->setTransactionType(\Application\Model\Constants::TRANSACTION_TYPE_PURCHASED);
            $entity->setRevisionNo($entity->getRevisionNo() + 1);
            $entity->setLastchangeBy($u);
            $entity->setLastchangeOn($changeOn);
            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();
            
            // POST AP
            // change status of row created before.
            // $res->postAP($entity);
            
            // UPDATE AP ROW, CREATE GR & CREATE STOCK GR
            // total rows checked.
            
            $criteria = array(
                'isActive' => 1,
                'invoice' => $entity
            );
            $ap_rows = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoiceRow')->findBy($criteria);
            
            $n = 0;
            foreach ($ap_rows as $r) {
                
                /** @var \Application\Entity\FinVendorInvoiceRow $r ; */
                
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
                
                $r->setDocStatus($entity->getDocStatus());
                
                // DO NOT change transaction type.
                // $r->setTransactionType($entity->getTransactionType());
                
                $r->setRowIdentifer($entity->getSysNumber() . '-' . $n);
                $r->setRowNumber($n);
                $this->doctrineEM->persist($r);
                
                // posting upon transaction type.
                /**
                 * GR-NI
                 */
                if ($r->getTransactionType() === \Application\Model\Constants::PROCURE_TRANSACTION_TYPE_GRNI) :
                    //clearing
                
                endif;
                
                /**
                 * GR-IR
                 */
                if ($r->getTransactionType() === \Application\Model\Constants::PROCURE_TRANSACTION_TYPE_GRIR) :
                    
                    // create procure GR, even no PR, PO.
                    $criteria = array(
                        'isActive' => 1,
                        'apInvoiceRow' => $r
                    );
                    $gr_entity_ck = $this->doctrineEM->getRepository('Application\Entity\NmtProcureGrRow')->findOneBy($criteria);
                    
                    if (! $gr_entity_ck == null) {
                        $gr_entity = $gr_entity_ck;
                    } else {
                        $gr_entity = new \Application\Entity\NmtProcureGrRow();
                    }
                    
                    $gr_entity->setIsActive(1);
                    $gr_entity->setInvoice($entity);
                    $gr_entity->setApInvoiceRow($r);
                    
                    $gr_entity->setItem($r->getItem());
                    $gr_entity->setPrRow($r->getPrRow());
                    $gr_entity->setPoRow($r->getPoRow());
                    
                    $gr_entity->setTargetObject(get_class($entity));
                    $gr_entity->setTargetObjectId($entity->getId());
                    $gr_entity->setTransactionType($r->getTransactionType());
                    
                    $gr_entity->setIsDraft($r->getIsDraft());
                    $gr_entity->setIsPosted($r->getIsPosted());
                    $gr_entity->setDocStatus($r->getDocStatus());
                    
                    $gr_entity->setQuantity($r->getQuantity());
                    $gr_entity->setUnit($r->getUnit());
                    $gr_entity->setUnitPrice($r->getUnitPrice());
                    $gr_entity->setNetAmount($r->getNetAmount());
                    $gr_entity->setTaxRate($r->getTaxRate());
                    $gr_entity->setTaxAmount($r->getTaxAmount());
                    $gr_entity->setGrossAmount($r->getGrossAmount());
                    $gr_entity->setDiscountRate($r->getDiscountRate());
                    
                    $gr_entity->setCreatedBy($u);
                    $gr_entity->setCreatedOn($createdOn);
                    $gr_entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
                    $this->doctrineEM->persist($gr_entity);
                    
                    /**
                     * create stock good receipt.
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
                        'invoiceRow' => $r
                    );
                    $stock_gr_entity_ck = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryTrx')->findOneBy($criteria);
                    
                    if (! $stock_gr_entity_ck == null) {
                        $stock_gr_entity = $stock_gr_entity_ck;
                    } else {
                        $stock_gr_entity = new NmtInventoryTrx();
                    }
                    
                    $stock_gr_entity->setIsActive(1);
                    $stock_gr_entity->setTrxDate($entity->getGrDate());
                    
                    $stock_gr_entity->setVendorInvoice($entity);
                    $stock_gr_entity->setInvoiceRow($r);
                    $stock_gr_entity->setItem($r->getItem());
                    $stock_gr_entity->setPrRow($r->getPrRow());
                    $stock_gr_entity->setPoRow($r->getPoRow());
                    $stock_gr_entity->setGrRow($gr_entity);
                    
                    $stock_gr_entity->setIsDraft($r->getIsDraft());
                    $stock_gr_entity->setIsPosted($r->getIsPosted());
                    $stock_gr_entity->setDocStatus($r->getDocStatus());
                    
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
                    
                    $stock_gr_entity->setRemarks('AP Row' . $r->getRowIdentifer());
                    $stock_gr_entity->setWh($entity->getWarehouse());
                    $stock_gr_entity->setCreatedBy($u);
                    $stock_gr_entity->setCreatedOn($createdOn);
                    $stock_gr_entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
                    $this->doctrineEM->persist($stock_gr_entity);
                    
                    /**
                     *
                     * @todo create serial number
                     *       if item with Serial
                     *       or Fixed Asset
                     */
                    if ($r->getItem() !== null) {
                        if ($r->getItem()->getMonitoredBy() == \Application\Model\Constants::ITEM_WITH_SERIAL_NO or $r->getItem()->getIsFixedAsset() == 1) {
                            
                            for ($i = 0; $i < $r->getQuantity(); $i ++) {
                                
                                // create new serial number
                                $sn_entity = new \Application\Entity\NmtInventoryItemSerial();
                                
                                $sn_entity->setItem($r->getItem());
                                $sn_entity->setApRow($r);
                                
                                $sn_entity->setInventoryTrx($stock_gr_entity);
                                $sn_entity->setIsActive(1);
                                $sn_entity->setSysNumber($nmtPlugin->getDocNumber($sn_entity));
                                 $sn_entity->setCreatedBy($u);
                                $sn_entity->setCreatedOn($createdOn);
                                $sn_entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
                                $this->doctrineEM->persist($sn_entity);
                            }
                        }
                    }
                    
                endif;
                
            }
            
            $this->doctrineEM->flush();
            
            /**
             * @ update relevant PR & PO
             */
            
            /**
             *
             * @todo Create Entry Journal
             *      
             */
            
            // LOGGING
            /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
            $nmtPlugin = $this->Nmtplugin();
            $changeArray = $nmtPlugin->objectsAreIdentical($oldEntity, $entity);
            
            $m = sprintf('[OK] AP Invoice #%s - %s posted.', $entity->getId(), $entity->getSysNumber());
            
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
            
            $this->flashMessenger()->addMessage($m);
            // $redirectUrl = "/finance/v-invoice/show?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
            $redirectUrl = "/finance/v-invoice/list";
            return $this->redirect()->toUrl($redirectUrl);
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
        
        $invoice = $res->getVendorInvoice($id, $token);
        
        $entity = null;
        if ($invoice[0] instanceof FinVendorInvoice) {
            $entity = $invoice[0];
        }
        
        if ($entity instanceof FinVendorInvoice) {
            
            if ($entity->getDocStatus() == \Application\Model\Constants::DOC_STATUS_POSTED) {
                $m = sprintf('AP Invoice #%s - %s already posted!', $entity->getId(), $entity->getSysNumber());
                $this->flashMessenger()->addMessage($m);
                $redirectUrl = "/finance/v-invoice/show?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
                return $this->redirect()->toUrl($redirectUrl);
            }
            
            if ($invoice['active_row'] == 0) {
                $m = sprintf('[INFO] AP Invoice #%s has no lines.', $entity->getSysNumber());
                $m1 = $nmtPlugin->translate('Document is incomplete!');
                $this->flashMessenger()->addMessage($m1);
                $redirectUrl = "/finance/v-invoice-row/add?token=" . $entity->getToken() . "&target_id=" . $entity->getId();
                return $this->redirect()->toUrl($redirectUrl);
            }
            
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
     *
     * @deprecated Posting A/P Invoice: Document can not be changed:
     *            
     *             1. change doc status of invoice and its rows to "posted"
     *             2. update relevant document
     *             - update procurement good receipts
     *             - upate PR
     *             - update PO
     *             - update GR
     *             - update stock good receipt
     *             3. Created Accounting Journal Entry
     *            
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function postAction()
    {
        $this->layout("Procure/layout-fullscreen");
        
        $request = $this->getRequest();
        
        $redirectUrl = null;
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        } else {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }
        
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
            
            /**
             * check header
             */
            $validator = new Date();
            
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
                
                /** @var \Application\Entity\NmtFinPostingPeriod $postingPeriod */
                $postingPeriod = $p->getPostingPeriod(new \DateTime($postingDate));
                
                if (! $postingPeriod instanceof \Application\Entity\NmtFinPostingPeriod) {
                    $errors[] = sprintf('Posting period for [%s] not created!', $postingDate);
                } else {
                    if ($postingPeriod->getPeriodStatus() == \Application\Model\Constants::PERIOD_STATUS_CLOSED) {
                        $errors[] = sprintf('Posting period [%s] is closed!', $postingPeriod->getPeriodName());
                    }
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
                
                if (! $postingPeriod instanceof \Application\Entity\NmtFinPostingPeriod) {
                    $errors[] = sprintf('Posting period for [%s] not created!', $grDate);
                } else {
                    if ($postingPeriod->getPeriodStatus() == \Application\Model\Constants::PERIOD_STATUS_CLOSED) {
                        $errors[] = sprintf('Posting period [%s] is closed!', $postingPeriod->getPeriodName());
                    }
                }
            }
            
            /**
             * check header
             */
            
            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                "email" => $this->identity()
            ));
            
            $changeOn = new \DateTime();
            $oldEntity = clone ($entity);
            
            $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_POSTED);
            $entity->setTransactionType(\Application\Model\Constants::TRANSACTION_TYPE_PURCHASED);
            $entity->setRevisionNo($entity->getRevisionNo() + 1);
            $entity->setLastchangeBy($u);
            $entity->setLastchangeOn($changeOn);
            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();
            
            // POST AP
            // change status of row created before.
            // $res->postAP($entity);
            
            // UPDATE AP ROW, CREATE GR & CREATE STOCK GR
            $criteria = array(
                'isActive' => 1,
                'invoice' => $entity
            );
            $ap_rows = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoiceRow')->findBy($criteria);
            
            if (count($ap_rows) > 0) {
                $n = 0;
                foreach ($ap_rows as $r) {
                    
                    /** @var \Application\Entity\FinVendorInvoiceRow $r ; */
                    // ignore row with 0 quantity
                    if ($r->getQuantity() == 0) {
                        $r->setIsActive(0);
                        continue;
                    }
                    
                    $createdOn = new \DateTime();
                    
                    // UPDATE status
                    $n ++;
                    $r->setIsPosted(1);
                    $r->setIsDraft(0);
                    $r->setDocStatus($entity->getDocStatus());
                    $r->setTransactionType($entity->getTransactionType());
                    $r->setRowIdentifer($entity->getSysNumber() . '-' . $n);
                    $r->setRowNumber($n);
                    
                    /**
                     * create procure good receipt.
                     * ============================
                     */
                    
                    $criteria = array(
                        'isActive' => 1,
                        'apInvoiceRow' => $r
                    );
                    $gr_entity_ck = $this->doctrineEM->getRepository('Application\Entity\NmtProcureGrRow')->findOneBy($criteria);
                    
                    if ($gr_entity_ck instanceof \Application\Entity\NmtProcureGrRow) {
                        $gr_entity = $gr_entity_ck;
                    } else {
                        $gr_entity = new \Application\Entity\NmtProcureGrRow();
                    }
                    
                    $gr_entity->setIsActive(1);
                    $gr_entity->setInvoice($entity);
                    $gr_entity->setApInvoiceRow($r);
                    
                    $gr_entity->setItem($r->getItem());
                    $gr_entity->setPrRow($r->getPrRow());
                    $gr_entity->setPoRow($r->getPoRow());
                    
                    $gr_entity->setTargetObject(get_class($entity));
                    $gr_entity->setTargetObjectId($entity->getId());
                    $gr_entity->setTransactionType($entity->getTransactionType());
                    
                    $gr_entity->setIsDraft($r->getIsDraft());
                    $gr_entity->setIsPosted($r->getIsPosted());
                    $gr_entity->setDocStatus($r->getDocStatus());
                    
                    $gr_entity->setQuantity($r->getQuantity());
                    $gr_entity->setUnit($r->getUnit());
                    $gr_entity->setUnitPrice($r->getUnitPrice());
                    $gr_entity->setNetAmount($r->getNetAmount());
                    $gr_entity->setTaxRate($r->getTaxRate());
                    $gr_entity->setTaxAmount($r->getTaxAmount());
                    $gr_entity->setGrossAmount($r->getGrossAmount());
                    $gr_entity->setDiscountRate($r->getDiscountRate());
                    
                    $gr_entity->setCreatedBy($u);
                    $gr_entity->setCreatedOn($createdOn);
                    $gr_entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
                    $this->doctrineEM->persist($gr_entity);
                    
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
                        'invoiceRow' => $r
                    );
                    $stock_gr_entity_ck = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryTrx')->findOneBy($criteria);
                    
                    if ($stock_gr_entity_ck instanceof \Application\Entity\NmtInventoryTrx) {
                        $stock_gr_entity = $stock_gr_entity_ck;
                    } else {
                        $stock_gr_entity = new NmtInventoryTrx();
                    }
                    
                    $stock_gr_entity->setIsActive(1);
                    $stock_gr_entity->setTrxDate($entity->getGrDate());
                    
                    $stock_gr_entity->setVendorInvoice($entity);
                    $stock_gr_entity->setInvoiceRow($r);
                    $stock_gr_entity->setItem($r->getItem());
                    $stock_gr_entity->setPrRow($r->getPrRow());
                    $stock_gr_entity->setPoRow($r->getPoRow());
                    $stock_gr_entity->setGrRow($gr_entity);
                    
                    $stock_gr_entity->setIsDraft($r->getIsDraft());
                    $stock_gr_entity->setIsPosted($r->getIsPosted());
                    $stock_gr_entity->setDocStatus($r->getDocStatus());
                    $stock_gr_entity->setTransactionType($entity->getTransactionType());
                    
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
                    
                    $stock_gr_entity->setRemarks('AP Row' . $r->getRowIdentifer());
                    $stock_gr_entity->setWh($entity->getWarehouse());
                    $stock_gr_entity->setCreatedBy($u);
                    $stock_gr_entity->setCreatedOn($createdOn);
                    $stock_gr_entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
                    $this->doctrineEM->persist($stock_gr_entity);
                }
                
                $this->doctrineEM->flush();
            }
            
            // LOGGING
            /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
            $nmtPlugin = $this->Nmtplugin();
            $changeArray = $nmtPlugin->objectsAreIdentical($oldEntity, $entity);
            
            $m = sprintf('[OK] AP Invoice #%s - %s posted.', $entity->getId(), $entity->getSysNumber());
            
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
            
            $this->flashMessenger()->addMessage($m);
            $redirectUrl = "/finance/v-invoice/show?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
            
            return $this->redirect()->toUrl($redirectUrl);
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     * Make A/P Invoice from PO
     * case GR-IR
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function copyFromPoAction()
    {
        $request = $this->getRequest();
        $this->layout("Finance/layout-fullscreen");
        
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $currency_list = $nmtPlugin->currencyList();
        
        // Is Posting .................
        // ============================
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
            
            if ($po != null) {
                if ($po[0] instanceof NmtProcurePo) {
                    $target = $po[0];
                }
            }
            
            if (! $target instanceof \Application\Entity\NmtProcurePo) {
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
            
            $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_DRAFT);
            $entity->setIsActive($isActive);
            $entity->setPo($target);
            $entity->setCurrentState($currentState);
            
            if (! $contractDate == null) {
                if (! $validator->isValid($contractDate)) {
                    $errors[] = 'Contract Date is not correct or empty!';
                } else {
                    $entity->setContractDate(new \DateTime($contractDate));
                }
            }
            $entity->setContractNo($contractNo);
            
            $vendor = null;
            if ($vendor_id > 0) {
                /** @var \Application\Entity\NmtBpVendor $vendor ; */
                $vendor = $this->doctrineEM->getRepository('Application\Entity\NmtBpVendor')->find($vendor_id);
            }
            
            if ($vendor instanceof \Application\Entity\NmtBpVendor) {
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
            
            if ($currency instanceof \Application\Entity\NmtApplicationCurrency) {
                $entity->setCurrency($currency);
                $entity->setCurrencyIso3($currency->getCurrency());
            } else {
                $errors[] = 'Currency can\'t be empty. Please select a currency!';
            }
            
            // check later
            $entity->setInvoiceNo($invoiceNo);
            
            $entity->setSapDoc($sapDoc);
            
            if (! $invoiceDate == null) {
                if (! $validator->isValid($invoiceDate)) {
                    $errors[] = 'Invoice Date is not correct or empty!';
                } else {
                    $entity->setInvoiceDate(new \DateTime($invoiceDate));
                }
            }
            
            // check if posting period is close
            /** @var \Application\Repository\NmtFinPostingPeriodRepository $p */
            $p = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod');
            
            // check one more time
            if (! $postingDate == null) {
                if (! $validator->isValid($postingDate)) {
                    $errors[] = 'Posting Date is not correct or empty!';
                } else {
                    
                    $entity->setPostingDate(new \DateTime($postingDate));
                    
                    /** @var \Application\Entity\NmtFinPostingPeriod $postingPeriod */
                    $postingPeriod = $p->getPostingPeriod(new \DateTime($postingDate));
                    
                    if (! $postingPeriod instanceof \Application\Entity\NmtFinPostingPeriod) {
                        $errors[] = sprintf('Posting period for [%s] not created!', $postingDate);
                    } else {
                        if ($postingPeriod->getPeriodStatus() == \Application\Model\Constants::PERIOD_STATUS_CLOSED) {
                            $errors[] = sprintf('Posting period [%s] is closed!', $postingPeriod->getPeriodName());
                        }
                    }
                }
            }
            
            // check one more time
            if (! $grDate == null) {
                if (! $validator->isValid($grDate)) {
                    $errors[] = 'Good receipt Date is not correct or empty!';
                } else {
                    
                    // check if posting period is close
                    /** @var \Application\Entity\NmtFinPostingPeriod $postingPeriod */
                    $postingPeriod = $p->getPostingPeriod(new \DateTime($grDate));
                    
                    if (! $postingPeriod instanceof \Application\Entity\NmtFinPostingPeriod) {
                        $errors[] = sprintf('Posting period for [%s] not created!', $grDate);
                    } else {
                        if ($postingPeriod->getPeriodStatus() == \Application\Model\Constants::PERIOD_STATUS_CLOSED) {
                            $errors[] = sprintf('Posting period [%s] is closed!', $postingPeriod->getPeriodName());
                        } else {
                            $entity->setGrDate(new \DateTime($grDate));
                        }
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
                // check later
                // $errors[] = 'Warehouse can\'t be empty. Please select a vendor!';
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
            // Saving into Database..........
            // ++++++++++++++++++++++++++++++
            
            // $entity->setSysNumber($nmtPlugin->getDocNumber($entity));
            $entity->setSysNumber(\Application\Model\Constants::SYS_NUMBER_UNASSIGNED);
            
            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                "email" => $this->identity()
            ));
            
            $entity->setCreatedBy($u);
            $entity->setCreatedOn(new \DateTime());
            $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
            
            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();
            
            // COPY open PO Row to AP Row
            
            /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
            $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
            $po_rows = $res->getPOStatus($id, $token);
            
            if (count($po_rows > 0)) {
                $n = 0;
                foreach ($po_rows as $l) {
                    
                    /** @var \Application\Entity\NmtProcurePoRow $l ; */
                    $r = $l[0];
                    
                    $n ++;
                    $row_tmp = new FinVendorInvoiceRow();
                    $row_tmp->setDocStatus($entity->getDocStatus());
                    
                    // Goods and Invoice receipt
                    $row_tmp->setTransactionType(\Application\Model\Constants::PROCURE_TRANSACTION_TYPE_GRIR);
                    
                    /**
                     *
                     * @todo Change entity
                     */
                    $row_tmp->setInvoice($entity);
                    $row_tmp->setIsDraft(1);
                    $row_tmp->setIsActive(1);
                    $row_tmp->setIsPosted(0);
                    
                    $row_tmp->setRowNumber($n);
                    
                    // do when posted.
                    // $row_tmp->setRowIndentifer($entity->getSysNumber() . "-$n");
                    
                    $row_tmp->setCurrentState("DRAFT");
                    $row_tmp->setPoRow($r);
                    $row_tmp->setPrRow($r->getPrRow());
                    $row_tmp->setItem($r->getItem());
                    $row_tmp->setQuantity($l['open_ap_qty']);
                    
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
                    $row_tmp->setCreatedOn(new \DateTime());
                    $row_tmp->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
                    $row_tmp->setRemarks("Ref: PO #" . $r->getRowIdentifer());
                    
                    $this->doctrineEM->persist($row_tmp);
                }
                $this->doctrineEM->flush();
            }
            
            $m = sprintf("[OK] AP #%s created from P/O #%s", $entity->getSysNumber(), $target->getSysNumber());
            $this->flashMessenger()->addMessage($m);
            
            $redirectUrl = "/finance/v-invoice/review?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
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
     * Make A/P Invoice from GR
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function copyFromGrAction()
    {
        $request = $this->getRequest();
        $this->layout("Finance/layout-fullscreen");
        
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $currency_list = $nmtPlugin->currencyList();
        
        // Is Posting .................
        // ============================
        if ($request->isPost()) {
            
            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');
            
            $id = (int) $request->getPost('target_id');
            $token = $request->getPost('target_token');
            
            /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
            $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
            $gr = $res->getGr($id, $token);
            
            /**@var \Application\Entity\NmtProcureGr $target ;*/
            $target = null;
            
            if ($gr != null) {
                if ($gr[0] instanceof \Application\Entity\NmtProcureGr) {
                    $target = $gr[0];
                }
            }
            
            if (! $target instanceof \Application\Entity\NmtProcureGr) {
                $errors[] = 'GR can\'t be empty!';
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
            // $grDate = $request->getPost('grDate');
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
            
            $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_DRAFT);
            $entity->setIsActive($isActive);
            // $entity->setGr($target);
            $entity->setCurrentState($currentState);
            
            if (! $contractDate == null) {
                if (! $validator->isValid($contractDate)) {
                    $errors[] = 'Contract Date is not correct or empty!';
                } else {
                    $entity->setContractDate(new \DateTime($contractDate));
                }
            }
            $entity->setContractNo($contractNo);
            
            $vendor = null;
            if ($vendor_id > 0) {
                /** @var \Application\Entity\NmtBpVendor $vendor ; */
                $vendor = $this->doctrineEM->getRepository('Application\Entity\NmtBpVendor')->find($vendor_id);
            }
            
            if ($vendor instanceof \Application\Entity\NmtBpVendor) {
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
            
            if ($currency instanceof \Application\Entity\NmtApplicationCurrency) {
                $entity->setCurrency($currency);
                $entity->setCurrencyIso3($currency->getCurrency());
            } else {
                $errors[] = 'Currency can\'t be empty. Please select a currency!';
            }
            
            // check later
            $entity->setInvoiceNo($invoiceNo);
            
            $entity->setSapDoc($sapDoc);
            
            if (! $invoiceDate == null) {
                if (! $validator->isValid($invoiceDate)) {
                    $errors[] = 'Invoice Date is not correct or empty!';
                } else {
                    $entity->setInvoiceDate(new \DateTime($invoiceDate));
                }
            }
            
            // check if posting period is close
            /** @var \Application\Repository\NmtFinPostingPeriodRepository $p */
            $p = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod');
            
            // check one more time
            if (! $postingDate == null) {
                if (! $validator->isValid($postingDate)) {
                    $errors[] = 'Posting Date is not correct or empty!';
                } else {
                    
                    $entity->setPostingDate(new \DateTime($postingDate));
                    
                    /** @var \Application\Entity\NmtFinPostingPeriod $postingPeriod */
                    $postingPeriod = $p->getPostingPeriod(new \DateTime($postingDate));
                    
                    if (! $postingPeriod instanceof \Application\Entity\NmtFinPostingPeriod) {
                        $errors[] = sprintf('Posting period for [%s] not created!', $postingDate);
                    } else {
                        if ($postingPeriod->getPeriodStatus() == \Application\Model\Constants::PERIOD_STATUS_CLOSED) {
                            $errors[] = sprintf('Posting period [%s] is closed!', $postingPeriod->getPeriodName());
                        }
                    }
                }
            }
            
            // check one more time
            
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
            // Saving into Database..........
            // ++++++++++++++++++++++++++++++
            
            // $entity->setSysNumber($nmtPlugin->getDocNumber($entity));
            $entity->setSysNumber(\Application\Model\Constants::SYS_NUMBER_UNASSIGNED);
            
            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                "email" => $this->identity()
            ));
            
            $entity->setCreatedBy($u);
            $entity->setCreatedOn(new \DateTime());
            $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
            
            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();
            
            // COPY open GR Row to AP Row
            
            /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
            $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
            $gr_rows = $res->getGRStatus($id, $token);
            
            if (count($gr_rows > 0)) {
                $n = 0;
                foreach ($gr_rows as $l) {
                    
                    /** @var \Application\Entity\NmtProcureGrRow $l ; */
                    $r = $l[0];
                    
                    $n ++;
                    $row_tmp = new FinVendorInvoiceRow();
                    $row_tmp->setDocStatus($entity->getDocStatus());
                    // $row_tmp->setTransactionType($entity->getTransactionStatus());
                    
                    // Goods receipt, Invoice Not receipt
                    $row_tmp->setTransactionType(\Application\Model\Constants::PROCURE_TRANSACTION_TYPE_GRNI);
                    
                    /**
                     *
                     * @todo Change entity
                     */
                    $row_tmp->setInvoice($entity);
                    $row_tmp->setIsDraft(1);
                    $row_tmp->setIsActive(1);
                    $row_tmp->setIsPosted(0);
                    
                    // $row_tmp->setRowNumber($n);
                    
                    // do when posted.
                    // $row_tmp->setRowIndentifer($entity->getSysNumber() . "-$n");
                    
                    $row_tmp->setCurrentState("DRAFT");
                    $row_tmp->setGrRow($r);
                    $row_tmp->setPrRow($r->getPrRow());
                    $row_tmp->setPoRow($r->getPoRow());
                    
                    $row_tmp->setItem($r->getItem());
                    $row_tmp->setQuantity($l['open_ap_qty']);
                    
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
                    $row_tmp->setCreatedOn(new \DateTime());
                    $row_tmp->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
                    $row_tmp->setRemarks("Ref: PO #" . $r->getRowIdentifer());
                    
                    $this->doctrineEM->persist($row_tmp);
                }
                $this->doctrineEM->flush();
            }
            
            $m = sprintf("[OK] AP #%s created from P/O #%s", $entity->getSysNumber(), $target->getSysNumber());
            $this->flashMessenger()->addMessage($m);
            
            $redirectUrl = "/finance/v-invoice/review?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
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
        
        $id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');
        
        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        $gr = $res->getGr($id, $token);
        
        if ($gr == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        /**@var \Application\Entity\NmtProcureGr $target ;*/
        
        $target = null;
        if ($gr[0] instanceof \Application\Entity\NmtProcureGr) {
            $target = $gr[0];
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
        
        // $entity->setSysNumber($nmtPlugin->getDocNumber($entity));
        $entity->setSysNumber(\Application\Model\Constants::SYS_NUMBER_UNASSIGNED);
        
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
     * @deprecated
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
     * @deprecated
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
                
                if ($invoiceNo == null) {
                    $errors[] = 'Please enter Invoice Number!';
                } else {
                    $entity->setInvoiceNo($invoiceNo);
                }
                
                $entity->setSapDoc($sapDoc);
                
                if ($invoiceDate !== null) {
                    
                    if (! $validator->isValid($invoiceDate)) {
                        $errors[] = 'Invoice Date is not correct or empty!';
                    } else {
                        $entity->setInvoiceDate(new \DateTime($invoiceDate));
                    }
                }
                
                if ($postingDate !== null) {
                    if (! $validator->isValid($postingDate)) {
                        $errors[] = 'Posting Date is not correct or empty!';
                    } else {
                        
                        $entity->setPostingDate(new \DateTime($postingDate));
                    }
                }
                
                if ($grDate !== null) {
                    if (! $validator->isValid($grDate)) {
                        $errors[] = 'Good receipt Date is not correct or empty!';
                    } else {
                        $entity->setGrDate(new \DateTime($grDate));
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
                
                $m = sprintf('[OK] AP Invoice #%s - %s updated. Change No.: %s.', $entity->getId(), $entity->getSysNumber(), count($changeArray));
                
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
                
                /**
                 * === Update current state of row invoice row ===
                 */
                
                $query = $this->doctrineEM->createQuery('
UPDATE Application\Entity\FinVendorInvoiceRow r SET r.currentState = :new_state WHERE r.invoice =:invoice_id
                    ')->setParameters(array(
                    'new_state' => $entity->getCurrentState(),
                    'invoice_id' => $entity->getId()
                ));
                $query->getResult();
                
                /**
                 * === Update current state of stock row.
                 * ===
                 */
                
                $criteria = array(
                    'isActive' => 1,
                    'invoice' => $entity->getId()
                );
                $sort_criteria = array();
                $invoice_rows = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoiceRow')->findBy($criteria, $sort_criteria);
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
            $sort_by = "InvoiceDate";
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
