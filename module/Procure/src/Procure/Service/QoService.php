<?php
namespace Procure\Service;

use Inventory\Model\GR\AbstractGRStrategy;
use Inventory\Model\GR\GRStrategyFactory;
use Procure\Model\Ap\AbstractAPRowPostingStrategy;
use Application\Entity\FinVendorInvoiceRow;
use Application\Service\AbstractService;
use Zend\Math\Rand;
use Zend\Validator\Date;

/**
 * Quotation Invoice Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class QoService extends AbstractService
{
    
    /**
     *
     * @param \Application\Entity\NmtProcureQo $entity
     * @param array $data
     * @param boolean $isPosting
     */
    public function validateHeader(\Application\Entity\NmtProcureQo $entity, $data, $isPosting = false)
    {
        $errors = array();
        
        if (! $entity instanceof \Application\Entity\NmtProcureQo) {
            $errors[] = $this->controllerPlugin->translate('Quotation is not found!');
        } else {
            if ($entity->getLocalCurrency() == null) {
                $errors[] = $this->controllerPlugin->translate('Local currency is not found!');
            }
        }
        
        if ($data == null) {
            $errors[] = $this->controllerPlugin->translate('No input given');
        }
        
        if (count($errors) > 0) {
            return $errors;
        }
        
        // ====== OK ====== //
        
        // check vendor. ok
        $ck = $this->checkVendor($entity, $data, $isPosting);
        if (count($ck) > 0) {
            $errors = array_merge($errors, $ck);
        }
        
        // check currency and exchange rate
        $ck = $this->checkCurrency($entity, $data, $isPosting);
        if (count($ck) > 0) {
            $errors = array_merge($errors, $ck);
        }
        
        // check invoice number
        $ck = $this->checkRefNo($entity, $data, $isPosting);
        if (count($ck) > 0) {
            $errors = array_merge($errors, $ck);
        }
        
        // check invoice date
        $ck = $this->checkRefDate($entity, $data, $isPosting);
        if (count($ck) > 0) {
            $errors = array_merge($errors, $ck);
        }
        
        return $errors;
    }
    
    /**
     *
     * @param \Application\Entity\NmtProcureQo $entity
     * @param \Application\Entity\MlaUsers $u
     * @param boolean $isNew
     */
    public function saveHeader($entity, $u, $isNew = FALSE)
    {
        if ($u == null) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }
        
        if (! $entity instanceof \Application\Entity\NmtProcureQo) {
            throw new \Exception("Invalid Argument. Quoation Object not found!");
        }
        
        // validated.
        
        $changeOn = new \DateTime();
        
        if ($isNew == TRUE) {            
            $entity->setSysNumber(\Application\Model\Constants::SYS_NUMBER_UNASSIGNED);
            $entity->setCreatedBy($u);
            $entity->setCreatedOn($changeOn);
            $entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
        } else {
            $entity->setRevisionNo($entity->getRevisionNo() + 1);
            $entity->setLastchangeBy($u);
            $entity->setLastchangeOn($changeOn);
        }
        
        $this->doctrineEM->persist($entity);
        $this->doctrineEM->flush();
    }
    
    /**
     *
     * @param \Application\Entity\NmtProcureQo $target
     * @param \Application\Entity\NmtProcureQoRow $entity
     * @param \Application\Entity\MlaUsers $u
     */
    public function validateRow($target, $entity, $data)
    {
        $errors = array();
        
        $isActive = (int) $data['isActive'];
        $item_id = $data['item_id'];
        $pr_row_id = $data['pr_row_id'];
        
        $rowNumber = $data['rowNumber'];
        
        $vendorItemCode = $data['vendorItemCode'];
        $unit = $data['unit'];
        $conversionFactor = $data['conversionFactor'];
        //$converstionText = $data['converstionText'];
        
        $quantity = $data['quantity'];
        $unitPrice = $data['unitPrice'];
        $taxRate = $data['taxRate'];
        //$traceStock = $data['traceStock'];
        
        $remarks = $data['remarks'];
        
        if ($isActive !== 1) {
            $isActive = 0;
        }
        
        $entity->setIsActive($isActive);
        
        
        $pr_row = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->find($pr_row_id);
        $item = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->find($item_id);
        
        if ($pr_row == null) {
            // $errors[] = 'Item can\'t be empty!';
        } else {
            $entity->setPrRow($pr_row);
        }
        
        if ($item instanceof \Application\Entity\NmtInventoryItem) {
            $entity->setItem($item);
        } else {
            $errors[] = 'Item can\'t be empty!';
        }
        
        $entity->setVendorItemCode($vendorItemCode);
        $entity->setUnit($unit);
        $entity->setConversionFactor($conversionFactor);
        //$entity->setConverstionText($converstionText);
        
        $n_validated = 0;
        if ($quantity == null) {
            $errors[] = 'Please  enter quantity!';
        } else {
            
            if (! is_numeric($quantity)) {
                $errors[] = 'Quantity must be a number.';
            } else {
                if ($quantity <= 0) {
                    $errors[] = 'Quantity must be greater than 0!';
                } else {
                    $entity->setQuantity($quantity);
                    $n_validated ++;
                }
            }
        }
        
        if ($unitPrice == null) {
            $errors[] = 'Price is not given. It must be a number.';
        } else {
            
            if (! is_numeric($unitPrice)) {
                $errors[] = 'Price is not valid. It must be a number.';
            } else {
                if ($unitPrice <= 0) {
                    $errors[] = 'Price must be greate than 0!';
                } else {
                    $entity->setUnitPrice($unitPrice);
                    $n_validated ++;
                }
            }
        }
        
        if ($n_validated == 2) {
            $netAmount = $entity->getQuantity() * $entity->getUnitPrice();
            $entity->setNetAmount($netAmount);
        }
        
        if ($taxRate != null) {
            if (! is_numeric($taxRate)) {
                $errors[] = '$taxRate is not valid. It must be a number.';
            } else {
                if ($taxRate < 0) {
                    $errors[] = '$taxRate must be greate than 0!';
                } else {
                    $entity->setTaxRate($taxRate);
                    $n_validated ++;
                }
            }
        }
        
        if ($n_validated == 3) {
            $taxAmount = $entity->getNetAmount() * $entity->getTaxRate() / 100;
            $grossAmount = $entity->getNetAmount() + $taxAmount;
            $entity->setTaxAmount($taxAmount);
            $entity->setGrossAmount($grossAmount);
        }
        
        // $entity->setTraceStock($traceStock);
        $entity->setRemarks($remarks);
        
        return $errors;
    }
    
    /**
     *
     * @param \Application\Entity\NmtProcureQo $target
     * @param \Application\Entity\NmtProcureQoRow $entity
     * @param \Application\Entity\MlaUsers $u
     * @param boolean $isNew
     */
    public function saveRow($target, $entity, $u, $isNew = FALSE)
    {
        if ($u == null) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }
        
        if (! $target instanceof \Application\Entity\NmtProcureQo) {
            throw new \Exception("Invalid Argument. Quotation Object not found!");
        }
        
        if (! $entity instanceof \Application\Entity\NmtProcureQoRow) {
            throw new \Exception("Invalid Argument. Quotation Line Object not found!");
        }
        
        // validated.
      
        if ($isNew == TRUE) {
            $entity->setCurrentState($target->getCurrentState());
            $entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
            $entity->setCreatedBy($u);
            $entity->setCreatedOn(new \DateTime());
        } else {
            $entity->setRevisionNo($entity->getRevisionNo() + 1);
            $entity->setLastchangeBy($u);
            $entity->setLastchangeOn(new \DateTime());
        }
        
        $this->doctrineEM->persist($entity);
        $this->doctrineEM->flush();
    }
    
    /**
     *
     * @param \Application\Entity\FinVendorInvoice $entity
     * @param \Application\Entity\MlaUsers $u,
     * @param bool $isFlush,
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function post($entity, $u, $isFlush = false, $isPosting = 1)
    {
        if (! $entity instanceof \Application\Entity\FinVendorInvoice) {
            throw new \Exception("Invalid Argument! Invoice can't not found.");
        }
        
        if (! $u instanceof \Application\Entity\MlaUsers) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }
        
        $criteria = array(
            'isActive' => 1,
            'invoice' => $entity
        );
        $ap_rows = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoiceRow')->findBy($criteria);
        
        if (count($ap_rows) == 0) {
            throw new \Exception("Invoice is empty. No Posting will be made!");
        }
        
        // OK to post
        // +++++++++++++++++++
        
        $changeOn = new \DateTime();
        
        // Assign doc number
        if ($entity->getSysNumber() == \Application\Model\Constants::SYS_NUMBER_UNASSIGNED) {
            $entity->setSysNumber($this->controllerPlugin->getDocNumber($entity));
        }
        
        $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_POSTED);
        $entity->setTransactionType(\Application\Model\Constants::TRANSACTION_TYPE_PURCHASED);
        $entity->setRevisionNo($entity->getRevisionNo() + 1);
        $entity->setLastchangeBy($u);
        $entity->setLastchangeOn($changeOn);
        $this->doctrineEM->persist($entity);
        
        $n = 0;
        foreach ($ap_rows as $r) {
            
            /** @var \Application\Entity\FinVendorInvoiceRow $r ; */
            
            // ignore row with Zero quantity
            if ($r->getQuantity() == 0) {
                $r->setIsActive(0);
                continue;
            }
            
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
            $r->setRowIdentifer($entity->getSysNumber() . '-' . $n);
            $r->setRowNumber($n);
            $this->doctrineEM->persist($r);
            
            $tTransaction = $r->getTransactionType();
            $rowPostingStrategy = \Procure\Model\Ap\APRowPostingFactory::getPostingStrategy($tTransaction);
            
            if (! $rowPostingStrategy instanceof AbstractAPRowPostingStrategy) {
                throw new \Exception("Posting strategy is not identified for this transaction!");
            }
            
            $rowPostingStrategy->setContextService($this);
            $rowPostingStrategy->doPosting($entity, $r, $u);
        }
        
        
        
        /**
         *
         * @todo: Do Accounting Posting
         */
        $this->jeService->postAP($entity, $ap_rows, $u, $this->controllerPlugin);
        $this->doctrineEM->flush();
        
        try {
            
            $criteria = array(
                'isActive' => 1,
                'vendorInvoice' => $entity
            );
            
            $inventory_trx_rows = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryTrx')->findBy($criteria);
            
            if (count($inventory_trx_rows) > 0) {
                
                $inventoryPostingStrategy = GRStrategyFactory::getGRStrategy(\Inventory\Model\Constants::INVENTORY_GR_FROM_PURCHASING);
                
                if (! $inventoryPostingStrategy instanceof AbstractGRStrategy) {
                    throw new \Exception("Posting strategy is not identified for this inventory movement type!");
                }
                
                // do posting now
                $inventoryPostingStrategy->setContextService($this);
                $inventoryPostingStrategy->createMovement($inventory_trx_rows, $u, true, $entity->getGrDate(), $entity->getWarehouse());
            }
        } catch (\Exception $e) {
            // left bank.
            
            $m = sprintf('[ERROR] %s', $e->getMessage());
            $this->getEventManager()->trigger('inventory.activity.log', __METHOD__, array(
                'priority' => \Zend\Log\Logger::ERR,
                'message' => $m,
                'createdBy' => $u,
                'createdOn' => $changeOn
            ));
        }
        
        $m = sprintf('[OK] AP Invoice %s posted', $entity->getSysNumber());
        $this->getEventManager()->trigger('procure.activity.log', __METHOD__, array(
            'priority' => \Zend\Log\Logger::INFO,
            'message' => $m,
            'createdBy' => $u,
            'createdOn' => $changeOn
        ));
        
        $apSearchService = new \Procure\Service\APSearchService();
        $apSearchService->indexingAPRows($ap_rows);
        
    }
    
    /**
     *
     * @param \Application\Entity\FinVendorInvoice $entity
     * @param \Application\Entity\NmtProcurePo $target
     * @param \Application\Entity\MlaUsers $u
     *
     */
    public function copyFromPO($entity, $target, $u, $isFlush = false)
    {
        if (! $entity instanceof \Application\Entity\FinVendorInvoice) {
            throw new \Exception("Invalid Argument! Invoice is not found.");
        }
        
        if (! $target instanceof \Application\Entity\NmtProcurePo) {
            throw new \Exception("Invalid Argument! PO Object is not found.");
        }
        
        if (! $u instanceof \Application\Entity\MlaUsers) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }
        
        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        
        $po_rows = $res->getPOStatus($target->getId(), $target->getToken());
        
        if ($po_rows == null) {
            throw new \Exception("PO Object is empty. Nothing is copied");
        }
        
        $entity->setPo($target);
        $n = 0;
        
        foreach ($po_rows as $l) {
            
            // if all received, ignore it.
            if ($l['open_ap_qty'] == 0) {
                continue;
            }
            
            /** @var \Application\Entity\NmtProcurePoRow $l ; */
            $r = $l[0];
            
            $gl_account = null;
            $cost_center = null;
            
            if ($r->getItem() != null) {
                
                $criteria = array(
                    'isActive' => 1,
                    'id' => $r->getItem()->getItemGroup()
                );
                /** @var \Application\Entity\NmtInventoryItemGroup $item_group ; */
                $item_group = $this->doctrineEM->getRepository('\Application\Entity\NmtInventoryItemGroup')->findOneBy($criteria);
                
                if ($item_group != null) {
                    
                    if ($r->getItem()->getIsStocked() == 1) {
                        $gl_account = $item_group->getInventoryAccount();
                    } else {
                        $gl_account = $item_group->getExpenseAccount();
                        $cost_center = $item_group->getCostCenter();
                    }
                }
            }
            
            $n ++;
            $row_tmp = new FinVendorInvoiceRow();
            
            $row_tmp->setGlAccount($gl_account);
            $row_tmp->setCostCenter($cost_center);
            
            $row_tmp->setDocStatus($entity->getDocStatus());
            
            // Goods and Invoice receipt
            $row_tmp->setTransactionType(\Application\Model\Constants::PROCURE_TRANSACTION_TYPE_GRIR);
            
            $row_tmp->setInvoice($entity);
            $row_tmp->setIsDraft(1);
            $row_tmp->setIsActive(1);
            $row_tmp->setIsPosted(0);
            
            $row_tmp->setRowNumber($n);
            
            $row_tmp->setCurrentState("DRAFT");
            $row_tmp->setPoRow($r);
            
            // converted to purchase qty
            $row_tmp->setQuantity($l['open_ap_qty']);
            $row_tmp->setUnitPrice($r->getUnitPrice());
            $row_tmp->setUnit($r->getUnit());
            
            $row_tmp->setConvertedPurchaseQuantity($r->getQuantity());
            
            $row_tmp->setConversionFactor($r->getConversionFactor());
            
            $row_tmp->setDocQuantity($row_tmp->getQuantity() / $row_tmp->getConversionFactor());
            $row_tmp->setDocUnitPrice($row_tmp->getUnitPrice() * $row_tmp->getConversionFactor());
            $row_tmp->setDocUnit($r->getDocUnit());
            
            $row_tmp->setTaxRate($r->getTaxRate());
            
            $item = $r->getItem();
            $pr_row = $r->getPrRow();
            $row_tmp->setPrRow($pr_row);
            $row_tmp->setItem($item);
            
            $convertedStandardQuantity = $row_tmp->getQuantity();
            $convertedStandardUnitPrice = $row_tmp->getUnitPrice();
            
            // converted to standard qty
            $standardCF = 1;
            
            if ($pr_row != null) {
                $standardCF = $standardCF * $pr_row->getConversionFactor();
            }
            
            if ($item != null) {
                $convertedStandardQuantity = $convertedStandardQuantity * $standardCF;
                $convertedStandardUnitPrice = $convertedStandardUnitPrice / $standardCF;
                
                // calculate standard quantity
                $row_tmp->setConvertedStandardQuantity($convertedStandardQuantity);
                $row_tmp->setConvertedStandardUnitPrice($convertedStandardUnitPrice);
            }
            
            $netAmount = $row_tmp->getQuantity() * $row_tmp->getUnitPrice();
            $taxAmount = $netAmount * $row_tmp->getTaxRate() / 100;
            $grossAmount = $netAmount + $taxAmount;
            
            $row_tmp->setNetAmount($netAmount);
            $row_tmp->setTaxAmount($taxAmount);
            $row_tmp->setGrossAmount($grossAmount);
            
            $row_tmp->setCreatedBy($u);
            $row_tmp->setCreatedOn(new \DateTime());
            $row_tmp->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
            $row_tmp->setRemarks("Ref: PO#" . $r->getRowIdentifer());
            
            $row_tmp->setExwUnitPrice($r->getExwUnitPrice());
            $row_tmp->setDiscountRate($r->getDiscountRate());
            
            $this->doctrineEM->persist($row_tmp);
        }
        
        if ($n == 0) {
            throw new \Exception("P/O is billed fully!");
        }
        
        if ($isFlush == true) {
            $this->doctrineEM->flush();
        }
    }
    
    /**
     *
     * @param \Application\Entity\FinVendorInvoice $entity
     * @param \Application\Entity\NmtProcureGr $target
     * @param \Application\Entity\MlaUsers $u
     *
     */
    public function copyFromGR($entity, $target, $u, $isFlush = false)
    {
        if (! $entity instanceof \Application\Entity\FinVendorInvoice) {
            throw new \Exception("Invalid Argument! Invoice is not found.");
        }
        
        if (! $target instanceof \Application\Entity\NmtProcureGr) {
            throw new \Exception("Invalid Argument! GR Object is not found.");
        }
        
        if (! $u instanceof \Application\Entity\MlaUsers) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }
        
        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        $gr_rows = $res->getGRStatus($target->getId(), $target->getToken());
        
        if ($gr_rows == null) {
            throw new \Exception("GR Object is empty. Nothing is copied");
        }
        
        $entity->setWarehouse($target->getWarehouse());
        $entity->setGrDate($target->getGrDate());
        
        $n = 0;
        foreach ($gr_rows as $l) {
            
            // if all received, ignore it.
            if ($l['open_ap_qty'] == 0) {
                continue;
            }
            
            /** @var \Application\Entity\NmtProcureGrRow $l ; */
            $r = $l[0];
            
            $n ++;
            
            $gl_account = null;
            $cost_center = null;
            
            if ($r->getItem() !== null) {
                
                $criteria = array(
                    'isActive' => 1,
                    'id' => $r->getItem()->getItemGroup()
                );
                /** @var \Application\Entity\NmtInventoryItemGroup $item_group ; */
                $item_group = $this->doctrineEM->getRepository('\Application\Entity\NmtInventoryItemGroup')->findOneBy($criteria);
                
                if ($item_group != null) {
                    
                    if ($r->getItem()->getIsStocked() == 1) {
                        $gl_account = $item_group->getInventoryAccount();
                    } else {
                        $gl_account = $item_group->getExpenseAccount();
                        $cost_center = $item_group->getCostCenter();
                    }
                }
            }
            
            $n ++;
            $row_tmp = new FinVendorInvoiceRow();
            
            $row_tmp->setGlAccount($gl_account);
            $row_tmp->setCostCenter($cost_center);
            
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
            $row_tmp->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
            $row_tmp->setRemarks("Ref: GR #" . $r->getRowIdentifer());
            
            $row_tmp->setGlAccount($r->getGlAccount());
            $row_tmp->setCostCenter($r->getCostCenter());
            
            $this->doctrineEM->persist($row_tmp);
        }
        
        if ($n == 0) {
            throw new \Exception("GR is received fully!");
        }
        
        if ($isFlush == true) {
            $this->doctrineEM->flush();
        }
    }
    
    /**
     *
     * @param \Application\Entity\NmtProcureQo $entity
     * @param array $data
     * @param boolean $isPosting
     */
    private function checkVendor(\Application\Entity\NmtProcureQo $entity, $data, $isPosting)
    {
        $errors = array();
        if (! isset($data['vendor_id'])) {
            $errors[] = $this->controllerPlugin->translate('Vendor id is not set!');
            return $errors;
        }
        
        $vendor_id = (int) $data['vendor_id'];
        
        /** @var \Application\Entity\NmtBpVendor $vendor ; */
        $vendor = $this->doctrineEM->getRepository('Application\Entity\NmtBpVendor')->find($vendor_id);
        
        if ($vendor !== null) {
            $entity->setVendor($vendor);
            $entity->setVendorName($vendor->getVendorName());
        } else {
            $errors[] = $this->controllerPlugin->translate('Vendor can\'t be empty. Please select a vendor!');
        }
        return $errors;
    }
    
    /**
     *
     * @param \Application\Entity\NmtProcureQo $entity
     * @param array $data
     * @param boolean $isPosting
     */
    private function checkCurrency(\Application\Entity\NmtProcureQo $entity, $data, $isPosting)
    {
        $errors = array();
        
        if (! isset($data['currency_id'])) {
            $errors[] = $this->controllerPlugin->translate('Currency ID input is not set!');
        }
        
        if (! isset($data['exchangeRate'])) {
            $errors[] = $this->controllerPlugin->translate('Exchange rate input is not set!');
        }
        
        if (count($errors) > 0) {
            return $errors;
        }
        
        // ==========OK=========== //
        
        $currency_id = (int) $data['currency_id'];
        $exchangeRate = (double) $data['exchangeRate'];
        
        // ** @var \Application\Entity\NmtApplicationCurrency $currency ; */
        $currency = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->find($currency_id);
        
        /** @var \Application\Repository\NmtFinPostingPeriodRepository $p */
        $p = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod');
        
        if ($currency !== null) {
            $entity->setCurrency($currency);
            $entity->setCurrencyIso3($currency->getCurrency());
            
            if ($currency == $entity->getLocalCurrency()) {
                $entity->setExchangeRate(1);
            } else {
                
                // if user give exchange rate.
                if ($exchangeRate != 0 and $exchangeRate != 1) {
                    if (! is_numeric($exchangeRate)) {
                        $errors[] = $this->controllerPlugin->translate('Foreign exchange rate is not valid. It must be a number.');
                    } else {
                        if ($exchangeRate < 0) {
                            $errors[] = $this->controllerPlugin->translate('Foreign exchange rate must be greater than 0!');
                        } else {
                            $entity->setExchangeRate($exchangeRate);
                        }
                    }
                } else {
                    // get default exchange rate.
                    /** @var \Application\Entity\FinFx $lastest_fx */
                    
                    $lastest_fx = $p->getLatestFX($currency_id, $entity->getLocalCurrency()
                        ->getId());
                    if ($lastest_fx !== null) {
                        $entity->setExchangeRate($lastest_fx->getFxRate());
                    } else {
                        $errors[] = sprintf('FX rate for %s not definded yet!', $currency->getCurrency());
                    }
                }
            }
        } else {
            $errors[] = $this->controllerPlugin->translate('Currency can\'t be empty. Please select a Currency!');
        }
        
        return $errors;
    }
    
    /**
     *
     * @param \Application\Entity\NmtProcureQo $entity
     * @param array $data
     * @param boolean $isPosting
     */
    private function checkRefNo(\Application\Entity\NmtProcureQo $entity, $data, $isPosting)
    {
        $errors = array();
        
        if (! isset($data['quoteNo'])) {
            $errors[] = $this->controllerPlugin->translate('quoteNo input is not set!');
            return $errors;
        }
        
        $refNo = $data['quoteNo'];
        
        // check invoice number
        if ($refNo == null and $isPosting == TRUE) {
            $errors[] = $this->controllerPlugin->translate('Please enter Quotation Number!');
        } else {
            $entity->setContractNo($refNo);
        }
        return $errors;
    }
    
    /**
     *
     * @param \Application\Entity\NmtProcureQo $entity
     * @param array $data
     * @param boolean $isPosting
     */
    private function checkRefDate(\Application\Entity\NmtProcureQo $entity, $data, $isPosting)
    {
        $errors = array();
        
        if (! isset($data['quoteDate'])) {
            $errors[] = $this->controllerPlugin->translate('Quotation Date input is not set!');
        }
         
        if (count($errors) > 0) {
            return $errors;
        }
        
        // ==========OK=========== //
        
        $refDate = $data['quoteDate'];
          
        $validator = new Date();
        
        if (! $refDate == null) {
            if (! $validator->isValid($refDate)) {
                $errors[] = $this->controllerPlugin->translate('Quotation Date is not correct or empty!');
            } else {
                $entity->setInvoiceDate(new \DateTime($refDate));
            }
        }
         
        if (count($errors) > 0) {
            return $errors;
        }
        
        // ==========OK=========== //
        
        // check if closed period when posting
        if ($isPosting == TRUE) {
            // left blank
        }
        return $errors;
    }
    
    /**
     *
     * @param \Application\Entity\FinVendorInvoice $entity
     * @param array $data
     * @param boolean $isPosting
     */
    private function checkGrDate(\Application\Entity\FinVendorInvoice $entity, $data, $isPosting)
    {
        $errors = array();
        
        if (! isset($data['grDate'])) {
            $errors[] = $this->controllerPlugin->translate('Good Receipt input is not set!');
            return $errors;
        }
        
        // ==========OK=========== //
        $grDate = $data['grDate'];
        
        $validator = new Date();
        
        if (! $grDate == null) {
            if (! $validator->isValid($grDate)) {
                $errors[] = $this->controllerPlugin->translate('Good receipt Date is not correct or empty!');
                return $errors;
            } else {
                $entity->setGrDate(new \DateTime($grDate));
            }
        }
        
        // ==========OK=========== //
        
        // striclty check when posting.
        if ($isPosting == TRUE) {
            
            if ($entity->getGrDate() == null) {
                $errors[] = $this->controllerPlugin->translate('Good receipt Date is not correct or empty!');
                return $errors;
            }
            
            /** @var \Application\Repository\NmtFinPostingPeriodRepository $p */
            $p = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod');
            
            // check if posting period is closed
            /** @var \Application\Entity\NmtFinPostingPeriod $postingPeriod */
            $postingPeriod = $p->getPostingPeriod(new \DateTime($grDate));
            
            if ($postingPeriod == null) {
                $errors[] = sprintf('Posting period for [%s] not created!', $grDate);
            } else {
                if ($postingPeriod->getPeriodStatus() == \Application\Model\Constants::PERIOD_STATUS_CLOSED) {
                    $errors[] = sprintf('Period [%s] is closed for Good receipt!', $postingPeriod->getPeriodName());
                } else {
                    $entity->setGrDate(new \DateTime($grDate));
                }
            }
        }
        
        return $errors;
    }
    
    /**
     *
     * @param \Application\Entity\FinVendorInvoice $entity
     * @param array $data
     * @param boolean $isPosting
     */
    private function checkWarehouse(\Application\Entity\FinVendorInvoice $entity, $data, $isPosting)
    {
        $errors = array();
        
        if (! isset($data['target_wh_id'])) {
            $errors[] = $this->controllerPlugin->translate('Ware House ID input is not set!');
            return $errors;
        }
        // ==========OK=========== //
        
        $warehouse_id = (int) $data['target_wh_id'];
        $warehouse = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->find($warehouse_id);
        $entity->setWarehouse($warehouse);
        
        if ($isPosting == TRUE and $entity->getWarehouse() == null) {
            $errors[] = $this->controllerPlugin->translate('Warehouse can\'t be empty. Please select a warehouse!');
        }
        return $errors;
    }
}
