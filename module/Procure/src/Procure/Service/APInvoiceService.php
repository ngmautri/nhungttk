<?php
namespace Procure\Service;

use Application\Entity\FinVendorInvoiceRow;
use Application\Service\AbstractService;
use Inventory\Model\GR\AbstractGRStrategy;
use Inventory\Model\GR\GRStrategyFactory;
use Procure\Model\Ap\AbstractAPRowPostingStrategy;
use Zend\Math\Rand;
use Zend\Validator\Date;

/**
 * AP Invoice Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class APInvoiceService extends AbstractService
{

    /**
     *
     * @param \Application\Entity\FinVendorInvoice $entity
     * @param array $data
     * @param boolean $isNew
     * @param boolean $isPosting
     */
    public function validateHeader(\Application\Entity\FinVendorInvoice $entity, $data, $isNew = false, $isPosting = false)
    {
        $errors = array();

        if (! $entity instanceof \Application\Entity\FinVendorInvoice) {
            $errors[] = $this->controllerPlugin->translate('AP invoice is not found!');
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

        $remarks = $data['remarks'];
        $entity->setRemarks($remarks);

        // only update remark posible, when posted.
        if ($entity->getDocStatus() == \Application\Model\Constants::DOC_STATUS_POSTED) {
            return null;
        }

        $sapDoc = $data['sapDoc'];
        $isActive = (int) $data['isActive'];
        $remarks = $data['remarks'];

        if ($isActive != 1) {
            $isActive = 0;
        }
        $entity->setIsActive($isActive);

        if ($sapDoc == "") {
            $sapDoc = "N/A";
        }
        $entity->setSapDoc($sapDoc);
        $entity->setRemarks($remarks);

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
        $ck = $this->checkInvoiceNo($entity, $data, $isPosting);
        if (count($ck) > 0) {
            $errors = array_merge($errors, $ck);
        }

        // check invoice date
        $ck = $this->checkInvoiceDate($entity, $data, $isPosting);
        if (count($ck) > 0) {
            $errors = array_merge($errors, $ck);
        }

        // check invoice date
        $ck = $this->checkGrDate($entity, $data, $isPosting);
        if (count($ck) > 0) {
            $errors = array_merge($errors, $ck);
        }

        // check warehouse
        $ck = $this->checkWarehouse($entity, $data, $isPosting);
        if (count($ck) > 0) {
            $errors = array_merge($errors, $ck);
        }

        // check currency and exchange rate
        $ck = $this->checkIncoterm($entity, $data, $isPosting);
        if (count($ck) > 0) {
            $errors = array_merge($errors, $ck);
        }
        
        // checkPaymentTerm
        $ck = $this->checkPaymentTerm($entity, $data, $isPosting);
        if (count($ck) > 0) {
            $errors = array_merge($errors, $ck);
        }

        return $errors;
    }

    /**
     *
     * @param \Application\Entity\FinVendorInvoice $entity
     * @param array $data
     * @param \Application\Entity\MlaUsers $u
     * @param boolean $isNew
     */
    public function saveHeader($entity, $data, $u, $isNew = FALSE)
    {
        $errors = array();

        if (! $u instanceof \Application\Entity\MlaUsers ) {
            $m = $this->controllerPlugin->translate("Invalid Argument! User can't be indentided for this transaction.");
            $errors [] = $m;
            
        }

        if (! $entity instanceof \Application\Entity\FinVendorInvoice) {
            $m = $this->controllerPlugin->translate("Invalid Argument. AP Object not found!");
            $errors [] = $m;
        }
        
        if (count($errors) > 0) {
            return $errors;
        }
  
        // ====== VALIDATED ====== //

        $oldEntity = clone ($entity);

        $ck = $this->validateHeader($entity, $data, $isNew);

        if (count($ck) > 0) {
            return $ck;
        }

        // ====== VALIDATED ====== //

        try {

            $changeOn = new \DateTime();
            $changeArray = array();

            if ($isNew == TRUE) {

                $entity->setSysNumber(\Application\Model\Constants::SYS_NUMBER_UNASSIGNED);
                $entity->setCreatedBy($u);
                $entity->setCreatedOn($changeOn);
                $entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
            } else {

                $changeArray = $this->controllerPlugin->objectsAreIdentical($oldEntity, $entity);
                if (count($changeArray) == 0) {
                    $errors[] = sprintf('Nothing changed.');
                    return $errors;
                }

                $entity->setRevisionNo($entity->getRevisionNo() + 1);
                $entity->setLastchangeBy($u);
                $entity->setLastchangeOn($changeOn);
            }

            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();

            // LOGGING
            if ($isNew == TRUE) {
                $m = sprintf('[OK] AP #%s created.', $entity->getId());
            } else {

                $m = sprintf('[OK] AP #%s updated.', $entity->getId());

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
            }

            $this->getEventManager()->trigger('finance.activity.log', __METHOD__, array(
                'priority' => \Zend\Log\Logger::INFO,
                'message' => $m,
                'createdBy' => $u,
                'createdOn' => $changeOn
            ));
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }

        return $errors;
    }

    /**
     *
     * @param \Application\Entity\FinVendorInvoice $target
     * @param \Application\Entity\FinVendorInvoiceRow $entity
     * @param array $data
     */
    public function validateRow($target, $entity, $data)
    {
        $errors = array();

        $item_id = $data['item_id'];
        $pr_row_id = $data['pr_row_id'];
        $po_row_id = $data['po_row_id'];

        $gl_account_id = $data['gl_account_id'];
        $cost_center_id = $data['cost_center_id'];
        $isActive = (int) $data['isActive'];
        $rowNumber = $data['rowNumber'];
        $vendorItemCode = $data['vendorItemCode'];

        $unit = $data['docUnit'];
        $conversionFactor = $data['conversionFactor'];
        $quantity = $data['docQuantity'];
        $unitPrice = $data['docUnitPrice'];
        $remarks = $data['remarks'];
        $taxRate = $data['taxRate'];

        $target_wh_id = $data['target_wh_id'];

        if ($isActive != 1) {
            $isActive = 0;
        }

        $entity->setIsActive($isActive);
        $entity->setDocStatus($target->getDocStatus());
        $entity->setRowNumber($rowNumber);
        $entity->setVendorItemCode($vendorItemCode);
        $entity->setDocUnit($unit);

        /** @var \Application\Entity\NmtProcurePrRow $pr_row ; */
        $pr_row = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->find($pr_row_id);

        /** @var \Application\Entity\NmtProcurePoRow $po_row ; */
        $po_row = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePoRow')->find($po_row_id);

        /** @var \Application\Entity\NmtInventoryItem $item ; */

        $item = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->find($item_id);

        /** @var \Application\Entity\NmtInventoryWarehouse $warehouse ; */
        $warehouse = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->find($target_wh_id);

        $gl = $this->doctrineEM->getRepository('Application\Entity\FinAccount')->find($gl_account_id);
        $costCenter = $this->doctrineEM->getRepository('Application\Entity\FinCostCenter')->find($cost_center_id);

        $entity->setCostCenter($costCenter);

        // PR and be empty
        $entity->setPrRow($pr_row);
        $entity->setPoRow($po_row);

        if ($po_row !== null) {
            $entity->setPrRow($po_row->getPrRow());
        }

        // Item cant be empty
        if ($item == null) {
            $errors[] = $this->controllerPlugin->translate('Item can\'t be empty. Please select item.');
        } else {
            $entity->setItem($item);

            if ($item->getIsStocked() == 1) {

                if ($warehouse == null) {
                    $errors[] = $this->controllerPlugin->translate('Warehouse is needed to inventory item!');
                } else {
                    $entity->setWarehouse($warehouse);
                }
            }
        }

        if (! is_numeric($quantity)) {
            $errors[] = $this->controllerPlugin->translate('Quantity must be a number.');
        } else {
            if ($quantity <= 0) {
                $errors[] = $this->controllerPlugin->translate('Quantity must be greater than 0!');
            } else {
                $entity->setDocQuantity($quantity);
            }
        }

        if ($conversionFactor == null) {
            $conversionFactor = 1;
        }

        if (! is_numeric($conversionFactor)) {
            $errors[] = $this->controllerPlugin->translate('conversion factor must be a number.');
        } else {
            if ($conversionFactor <= 0) {
                $errors[] = $this->controllerPlugin->translate('converstion factor must be greater than 0!');
            } else {
                $entity->setConversionFactor($conversionFactor);
            }
        }

        if (! is_numeric($unitPrice)) {
            $errors[] = $this->controllerPlugin->translate('Price is not valid. It must be a number.');
        } else {
            if ($unitPrice < 0) {
                $errors[] = $this->controllerPlugin->translate('Price must be greate than or equal 0!');
            } else {
                $entity->setDocUnitPrice($unitPrice);
            }
        }

        if (! is_numeric($taxRate)) {
            $errors[] = $this->controllerPlugin->translate('taxRate is not valid. It must be a number.');
        } else {
            if ($taxRate < 0) {
                $errors[] = $this->controllerPlugin->translate('taxRate must be greate than 0!');
            } else {
                $entity->setTaxRate($taxRate);
            }
        }

        if ($gl == null) {
            $errors[] = $this->controllerPlugin->translate('G/L account can\'t be empty!. Pls select G/L');
        } else {
            $entity->setGlAccount($gl);
        }

        $entity->setRemarks($remarks);

        return $errors;
    }

    /**
     *
     * @param \Application\Entity\FinVendorInvoice $target
     * @param \Application\Entity\FinVendorInvoiceRow $entity
     * @param \Application\Entity\MlaUsers $u
     * @param boolean $isNew
     */
    public function saveRow($target, $entity, $u, $isNew = FALSE)
    {
        if ($u == null) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        if (! $target instanceof \Application\Entity\FinVendorInvoice) {
            throw new \Exception("Invalid Argument. AP Object not found!");
        }

        if (! $entity instanceof \Application\Entity\FinVendorInvoiceRow) {
            throw new \Exception("Invalid Argument. AP Line Object not found!");
        }

        // validated.

        $netAmount = $entity->getDocQuantity() * $entity->getDocUnitPrice();
        $entity->setNetAmount($netAmount);

        $taxAmount = $entity->getNetAmount() * $entity->getTaxRate() / 100;
        $grossAmount = $entity->getNetAmount() + $taxAmount;

        $entity->setTaxAmount($taxAmount);
        $entity->setGrossAmount($grossAmount);

        $convertedPurchaseQuantity = $entity->getDocQuantity();
        $convertedPurchaseUnitPrice = $entity->getDocUnitPrice();

        $conversionFactor = $entity->getConversionFactor();
        $standardCF = $entity->getConversionFactor();

        // converted to purchase quantity
        $convertedPurchaseQuantity = $convertedPurchaseQuantity * $conversionFactor;
        $convertedPurchaseUnitPrice = $convertedPurchaseUnitPrice / $conversionFactor;

        // to check the unit price.

        $pr_row = $entity->getPrRow();
        $pr_unit = null;

        if ($pr_row != null) {
            $standardCF = $standardCF * $pr_row->getConversionFactor();
            $pr_unit = $pr_row->getRowUnit();
        }

        // quantity /unit: price is converted purchase quantity to clear PR

        $entity->setQuantity($convertedPurchaseQuantity);
        $entity->setUnitPrice($convertedPurchaseUnitPrice);
        $entity->setUnit($pr_unit);

        $convertedStandardQuantity = $entity->getDocQuantity();
        $convertedStandardUnitPrice = $entity->getDocUnitPrice();

        $item = $entity->getItem();

        if ($item != null) {
            $convertedStandardQuantity = $convertedStandardQuantity * $standardCF;
            $convertedStandardUnitPrice = $convertedStandardUnitPrice / $standardCF;
        }

        // calculate standard quantity
        $entity->setConvertedPurchaseQuantity($convertedPurchaseQuantity);
        $entity->setConvertedPurchaseUnitPrice($convertedPurchaseUnitPrice);

        $entity->setConvertedStandardQuantity($convertedStandardQuantity);
        $entity->setConvertedStandardUnitPrice($convertedStandardUnitPrice);

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
     * @param array $data
     * @param \Application\Entity\MlaUsers $u,
     * @param bool $isFlush,
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function post($entity, $data, $u, $isFlush = false, $isPosting = TRUE)
    {
        $errors = array();

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

        // ====== VALIDATED ====== //

        $oldEntity = clone($entity);
        
        $isPosting = TRUE;
        $isNew = FALSE;
        $ck = $this->validateHeader($entity, $data, $isNew, $isPosting);

        if (count($ck) > 0) {
            return $ck;
        }

        // ====== VALIDATED ====== //

        try {

            $changeOn = new \DateTime();
            
            /** UPDATE HEADER*/
            /** ======================*/
            

            // Assign doc number
            if ($entity->getSysNumber() == \Application\Model\Constants::SYS_NUMBER_UNASSIGNED) {
                $entity->setSysNumber($this->controllerPlugin->getDocNumber($entity));
            }

            $entity->setIsActive(1);
            $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_POSTED);
            $entity->setTransactionType(\Application\Model\Constants::TRANSACTION_TYPE_PURCHASED);
            $entity->setRevisionNo($entity->getRevisionNo() + 1);
            $entity->setLastchangeBy($u);
            $entity->setLastchangeOn($changeOn);
            $this->doctrineEM->persist($entity);

            
            /** POSTING ROW*/
            /** ======================*/
            
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

            /** POSTING JOURNAL ENTRY*/
            /** ======================*/
            $this->jeService->postAP($entity, $ap_rows, $u, $this->controllerPlugin);
            
            // need to flush
            $this->doctrineEM->flush();

            
            /** POSTING WH GOODs RECEIPT*/ 
            /** ======================*/
            $criteria = array(
                'isActive' => 1,
                'vendorInvoice' => $entity
            );

            $inventory_trx_rows = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryTrx')->findBy($criteria);

            // if have inventory item.
            if (count($inventory_trx_rows) > 0) {

                $inventoryPostingStrategy = GRStrategyFactory::getGRStrategy(\Inventory\Model\Constants::INVENTORY_GR_FROM_PURCHASING);

                if (! $inventoryPostingStrategy instanceof AbstractGRStrategy) {
                    throw new \Exception("Posting strategy is not identified for this inventory movement type!");
                }

                // do posting now
                $inventoryPostingStrategy->setContextService($this);
                $inventoryPostingStrategy->createMovement($inventory_trx_rows, $u, true, $entity->getGrDate(), $entity->getWarehouse());
            }

            /** UPDATING SEARCH INDEX */
            /** ======================*/
            $apSearchService = new \Procure\Service\APSearchService();
            $apSearchService->indexingAPRows($ap_rows);
            
            /** LOGGING */
            /** ======================*/
            $m = sprintf('[OK] AP Invoice %s posted', $entity->getSysNumber());
            $this->getEventManager()->trigger('procure.activity.log', __METHOD__, array(
                'priority' => \Zend\Log\Logger::INFO,
                'message' => $m,
                'createdBy' => $u,
                'createdOn' => $changeOn
            ));
            
            
            $changeArray = $this->controllerPlugin->objectsAreIdentical($oldEntity, $entity);
            if (count($changeArray) == 0) {
                
                $entity->setRevisionNo($entity->getRevisionNo() + 1);
                $entity->setLastchangeBy($u);
                $entity->setLastchangeOn($changeOn);
            }
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
              
            // need to flush
            $this->doctrineEM->flush();
            
            
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }

        return $errors;
    }
    
    /**
     *
     * @param \Application\Entity\FinVendorInvoice $entity
     * @param array $data
     * @param \Application\Entity\MlaUsers $u,
     * @param bool $isFlush,
     *
     */
    public function reverseAP($entity, $u, $reversalDate, $reversalReason)
    {
        $errors = array();
        
        if (! $entity instanceof \Application\Entity\FinVendorInvoice) {
             $errors[] = $this->controllerPlugin->translate('Invalid Argument! Invoice not found)');
        }else{
            
            // only when posted.
            if ($entity->getDocStatus()!== \Application\Model\Constants::DOC_STATUS_POSTED) {
                $errors[] = $this->controllerPlugin->translate('Invoice not posted yet. Reserval imposible.');
            }
        }
        
        if (! $u instanceof \Application\Entity\MlaUsers) {
            $errors[] = $this->controllerPlugin->translate('Invalid Argument! User not found)');
        }
        
        $criteria = array(
            'isActive' => 1,
            'invoice' => $entity
        );
        $ap_rows = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoiceRow')->findBy($criteria);
        
        if (count($ap_rows) == 0) {
            $errors[] = $this->controllerPlugin->translate('Invoice is empty. Reserval imposible.');
        }
        
        if (count($errors) > 0) {
            return $errors;
        }
        
        // ====== VALIDATED ====== //
        
        
        /** @var \Application\Entity\FinVendorInvoice $newEntity ; */
        $oldEntity = clone($entity);
        
        
        $data = array();
        $data['postingDate'] = $reversalDate;
        $data['invoiceDate'] = $reversalDate;
        
        /**
         * Check Reversal Date.
         */
        $ck = $this->checkInvoiceDate($oldEntity, $data, TRUE);
        
        if (count($ck) > 0) {
            return $errors;
        }
        
        // ====== VALIDATED ====== //
        
        try {
            
            $changeOn = new \DateTime();
            
            /** UPDATE OLD HEADER*/
            /** ======================*/
            
                
            $entity->setIsReversed(1);
            $entity->setReversalReason($reversalReason);
            $entity->setReversalDate(new \DateTime($reversalDate));
            $entity->setRevisionNo($entity->getRevisionNo() + 1);
            $entity->setLastchangeBy($u);
            $entity->setLastchangeOn($changeOn);
            $this->doctrineEM->persist($entity);
            
            /** POSTING ROW*/
            /** ======================*/
            
            $n = 0;
            foreach ($ap_rows as $r) {
           
                // ignore row with Zero quantity
                if ($r->getQuantity() == 0) {
                    $r->setIsActive(0);
                    continue;
                }
                
                $n++;
                
                $old_r = clone($r);
                
                // update new row.
                
                //important!
                $r->setRemarks($entity->getRemarks());
                $r->setIsReversed(1);
                $r->setReversalReason($reversalReason);
                $r->setReversalDate(new \DateTime($reversalDate));
                $this->doctrineEM->persist($r);
                
                /**
                 * @todo: Reverse Row - done
                 */
                $tTransaction = $r->getTransactionType();
                $rowPostingStrategy = \Procure\Model\Ap\APRowPostingFactory::getReversalStrategy($tTransaction);
                
                if (! $rowPostingStrategy instanceof AbstractAPRowPostingStrategy) {
                    throw new \Exception("Reversal strategy is not found for this transaction!");
                }
                
                $rowPostingStrategy->setContextService($this);
                $rowPostingStrategy->doPosting($entity, $r, $u);
                
              }
            
            /** POSTING JOURNAL ENTRY*/
            /** ======================*/
            
            /**
             * @todo: Reverval JE 
             */
            $this->jeService->reverseAP($entity, $ap_rows, $u, $this->controllerPlugin);
            
            // need to flush
            $this->doctrineEM->flush();
            
            
            /** REVERSAL WH GOODs RECEIPT*/
            /** ======================*/
            /**
             * @todo: Reverval Good Receipt WH.
             */
            $criteria = array(
                'isActive' => 1,
                'vendorInvoice' => $entity
            );
            
            $inventory_trx_rows = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryTrx')->findBy($criteria);
            
            // if have inventory item.
            if (count($inventory_trx_rows) > 0) {
                
                $inventoryPostingStrategy = GRStrategyFactory::getGRStrategy(\Inventory\Model\Constants::INVENTORY_GR_FROM_PURCHASING_REVERSAL);
                
                if (! $inventoryPostingStrategy instanceof AbstractGRStrategy) {
                    throw new \Exception("Posting strategy is not identified for this inventory movement type!");
                }
                
                // do posting now
                $inventoryPostingStrategy->setContextService($this);
                $inventoryPostingStrategy->createMovement($inventory_trx_rows, $u, true, $entity->getReversalDate(), $entity->getWarehouse());
            }
            
            /** UPDATING SEARCH INDEX */
            /** ======================*/
            
            
            /**
             * 
             * @todo
             */
            $apSearchService = new \Procure\Service\APSearchService();
            $apSearchService->indexingAPRows($ap_rows);
            
            /** LOGGING */
            /** ======================*/
            $m = sprintf('[OK] AP Invoice %s reversed.', $entity->getSysNumber());
            $this->getEventManager()->trigger('procure.activity.log', __METHOD__, array(
                'priority' => \Zend\Log\Logger::INFO,
                'message' => $m,
                'createdBy' => $u,
                'createdOn' => $changeOn
            ));
            
            
            // need to flush
            $this->doctrineEM->flush();
                  
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }
        
        return $errors;
        
        
    }

    /**
     *
     * @param \Application\Entity\FinVendorInvoice $entity
     * @param \Application\Entity\NmtProcureQo $target
     * @param \Application\Entity\MlaUsers $u
     *
     */
    public function copyFromQO($entity, $target, $u, $isFlush = false)
    {
        if (! $entity instanceof \Application\Entity\FinVendorInvoice) {
            throw new \Exception("Invalid Argument! Invoice is not found.");
        }

        if (! $target instanceof \Application\Entity\NmtProcureQo) {
            throw new \Exception("Invalid Argument! QO Object is not found.");
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
     * @param \Application\Entity\FinVendorInvoice $entity
     * @param array $data
     * @param boolean $isPosting
     */
    private function checkVendor(\Application\Entity\FinVendorInvoice $entity, $data, $isPosting)
    {
        $errors = array();
        if (! isset($data['vendor_id'])) {
            $errors[] = $this->controllerPlugin->translate('Vendor Id is not set!');
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
     * @param \Application\Entity\FinVendorInvoice $entity
     * @param array $data
     * @param boolean $isPosting
     */
    private function checkCurrency(\Application\Entity\FinVendorInvoice $entity, $data, $isPosting)
    {
        $errors = array();

        if (! isset($data['currency_id'])) {
            $errors[] = $this->controllerPlugin->translate('Currency Id input is not set!');
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

        // check if posting period is closed
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
     * @param \Application\Entity\FinVendorInvoice $entity
     * @param array $data
     * @param boolean $isPosting
     */
    private function checkInvoiceNo(\Application\Entity\FinVendorInvoice $entity, $data, $isPosting)
    {
        $errors = array();

        if (! isset($data['invoiceNo'])) {
            $errors[] = $this->controllerPlugin->translate('Invoice No input is not set!');
            return $errors;
        }

        $invoiceNo = $data['invoiceNo'];

        // check invoice number
        if ($invoiceNo == null and $isPosting == TRUE) {
            $errors[] = $this->controllerPlugin->translate('Please enter Invoice Number!');
        } else {
            $entity->setInvoiceNo($invoiceNo);
        }
        return $errors;
    }

    /**
     *
     * @param \Application\Entity\FinVendorInvoice $entity
     * @param array $data
     * @param boolean $isPosting
     */
    private function checkInvoiceDate(\Application\Entity\FinVendorInvoice $entity, $data, $isPosting)
    {
        $errors = array();

        if (! isset($data['invoiceDate'])) {
            $errors[] = $this->controllerPlugin->translate('Invoice Date input is not set!');
        }

        if (! isset($data['postingDate'])) {
            $errors[] = $this->controllerPlugin->translate('Posting date input is not set!');
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // ==========OK=========== //

        $invoiceDate = $data['invoiceDate'];
        $postingDate = $data['postingDate'];

        $validator = new Date();

        if (! $invoiceDate == null) {
            if (! $validator->isValid($invoiceDate)) {
                $errors[] = $this->controllerPlugin->translate('Invoice Date is not correct or empty!');
            } else {
                $entity->setInvoiceDate(new \DateTime($invoiceDate));
            }
        }
        if (! $postingDate == null) {
            if (! $validator->isValid($postingDate)) {
                $errors[] = $this->controllerPlugin->translate('Posting Date is not correct or empty!');
            } else {
                $entity->setPostingDate(new \DateTime($postingDate));
            }
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // ==========OK=========== //

        // check if closed period when posting
        if ($isPosting == TRUE) {

            if ($entity->getInvoiceDate() == null) {
                $errors[] = $this->controllerPlugin->translate('Invoice Date is not correct or empty!');
            }

            if ($entity->getPostingDate() == null) {
                $errors[] = $this->controllerPlugin->translate('Posting Date is not correct or empty!');
            } else {

                /** @var \Application\Repository\NmtFinPostingPeriodRepository $p */
                $p = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod');

                /** @var \Application\Entity\NmtFinPostingPeriod $postingPeriod */
                $postingPeriod = $p->getPostingPeriod($entity->getPostingDate());

                if ($postingPeriod == null) {
                    $errors[] = sprintf('Posting period for [%s] not created!', $postingDate);
                } else {
                    if ($postingPeriod->getPeriodStatus() == \Application\Model\Constants::PERIOD_STATUS_CLOSED) {
                        $errors[] = sprintf('Posting period [%s] is closed!', $postingPeriod->getPeriodName());
                    } else {
                        $entity->setPostingPeriod($postingPeriod);
                    }
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

    /**
     *
     * @param \Application\Entity\FinVendorInvoice $entity
     * @param array $data
     * @param boolean $isPosting
     */
    private function checkIncoterm(\Application\Entity\FinVendorInvoice $entity, $data, $isPosting)
    {
        $errors = array();
        if (isset($data['incoterm_id']) and isset($data['incotermPlace'])) {
            // $errors[] = $this->controllerPlugin->translate('Incoterm id is not set!');

            $incoterm_id = (int) $data['incoterm_id'];
            $incoterm_place = $data['incotermPlace'];

            /** @var \Application\Entity\NmtApplicationIncoterms $vendor ; */
            $incoterm = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationIncoterms')->find($incoterm_id);

            if ($incoterm !== null) {
                $entity->setIncoterm2($incoterm);

                if ($incoterm_place == null) {
                    $errors[] = $this->controllerPlugin->translate('Please give incoterm place!');
                } else {
                    $entity->setIncotermPlace($incoterm_place);
                }
            } else {
                // $errors[] = $this->controllerPlugin->translate('Vendor can\'t be empty. Please select a vendor!');
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
    private function checkPaymentTerm(\Application\Entity\FinVendorInvoice $entity, $data, $isPosting)
    {
        $errors = array();
        if (isset($data['payment_term_id'])) {
            // $errors[] = $this->controllerPlugin->translate('Incoterm id is not set!');
            
            $payment_term_id = (int) $data['payment_term_id'];
            
            /** @var \Application\Entity\NmtApplicationPmtTerm $payment_term ; */
            $payment_term = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationPmtTerm')->find($payment_term_id);
            
            if ($payment_term !== null) {
                $entity->setPmtTerm($payment_term);
             } else {
                $errors[] = $this->controllerPlugin->translate('Payment Term is not set.');
            }
        }
        return $errors;
    }
}
