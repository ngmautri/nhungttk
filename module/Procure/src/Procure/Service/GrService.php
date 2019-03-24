<?php
namespace Procure\Service;

use Application\Entity\NmtInventoryTrx;
use Application\Entity\NmtProcureGrRow;
use Application\Service\AbstractService;
use Zend\Math\Rand;
use Zend\Validator\Date;
use Inventory\Model\InventoryTransactionStrategyFactory;
use Inventory\Model\AbstractTransactionStrategy;

/**
 * Good Receipt Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GrService extends AbstractService
{

    /**
     *
     * @param \Application\Entity\NmtProcureGr $entity
     * @param array $data
     * @param boolean $isNew
     * @param boolean $isPosting
     */
    public function validateHeader(\Application\Entity\NmtProcureGr $entity, $data, $isNew = false, $isPosting = false)
    {
        $errors = array();

        if (! $entity instanceof \Application\Entity\NmtProcureGr) {
            $errors[] = $this->controllerPlugin->translate('Procure Good receipt is not found!');
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
        if ($entity->getDocStatus() == \Application\Model\Constants::DOC_STATUS_POSTED or $entity->getDocStatus() == \Application\Model\Constants::DOC_STATUS_REVERSED) {
            return null;
        }

        $isActive = (int) $data['isActive'];
        $remarks = $data['remarks'];

        if ($isActive != 1) {
            $isActive = 0;
        }
        $entity->setIsActive($isActive);

        // check vendor. ok
        $ck = $this->checkVendor($entity, $data, $isPosting);
        if (count($ck) > 0) {
            $errors = array_merge($errors, $ck);
        }

        // check PO Date
        $ck = $this->checkPODate($entity, $data, $isPosting);
        if (count($ck) > 0) {
            $errors = array_merge($errors, $ck);
        }

        // check currency and exchange rate
        $ck = $this->checkCurrency($entity, $data, $isPosting);
        if (count($ck) > 0) {
            $errors = array_merge($errors, $ck);
        }

        // check good receipt date
        $ck = $this->checkGrDate($entity, $data, $isPosting);
        if (count($ck) > 0) {
            $errors = array_merge($errors, $ck);
        }

        // check warehouse
        $ck = $this->checkWarehouse($entity, $data, $isPosting);
        if (count($ck) > 0) {
            $errors = array_merge($errors, $ck);
        }

        return $errors;
    }

    /**
     *
     * @param \Application\Entity\NmtProcureGr $entity
     * @param array $data
     * @param \Application\Entity\MlaUsers $u
     * @param boolean $isNew
     */
    public function saveHeader(\Application\Entity\NmtProcureGr $entity, $data, $u, $isNew = FALSE, $trigger = null)
    {
        $errors = array();

        if (! $u instanceof \Application\Entity\MlaUsers) {
            $errors[] = $this->controllerPlugin->translate("Invalid Argument! User is not indentided for this transaction");
        }

        if (! $entity instanceof \Application\Entity\NmtProcureGr) {
            $errors[] = $this->controllerPlugin->translate("Invalid Argument. Procure Good receipt object is not found!");
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // ====== VALIDATED ====== //

        if ($isNew == FALSE) {
            $oldEntity = clone ($entity);
        }

        $ck = $this->validateHeader($entity, $data, $isNew, false);

        if (count($ck) > 0) {
            return $ck;
        }

        // $errors[] = $this->controllerPlugin->translate("Always errors for testing" . count($ck) );
        // return $errors;

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
                $m = sprintf('[OK] GR #%s created.', $entity->getId());
            } else {

                $m = sprintf('[OK] GR #%s updated.', $entity->getId());

                $this->getEventManager()->trigger('procure.change.log', $trigger, array(
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

            $this->getEventManager()->trigger('procure.activity.log', $trigger, array(
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
     * @param \Application\Entity\NmtProcureGr $target
     * @param \Application\Entity\NmtProcureGrRow $entity
     * @return NULL[]|string[]
     */
    public function validateRow($target, $entity, $data)
    {

        // do validating
        $errors = array();

        if (! $target instanceof \Application\Entity\NmtProcureGr) {
            throw new \Exception("Invalid Argument. GR Object not found!");
        }

        if (! $entity instanceof \Application\Entity\NmtProcureGrRow) {
            throw new \Exception("Invalid Argument.GR Line Object not found!");
        }

        $item_id = (int) $data['item_id'];
        $gl_account_id = (int) $data['gl_account_id'];
        $pr_row_id = $data['pr_row_id'];
        $po_row_id = $data['po_row_id'];
        $isActive = (int) $data['isActive'];
        $rowNumber = $data['rowNumber'];

        $vendorItemCode = $data['vendorItemCode'];
        $unit = $data['unit'];
        $conversionFactor = $data['conversionFactor'];

        $quantity = $data['quantity'];
        $unitPrice = $data['unitPrice'];
        $taxRate = $data['taxRate'];

        $remarks = $data['remarks'];

        if ($isActive != 1) {
            $isActive = 0;
        }

        $entity->setIsActive($isActive);
        $entity->setRowNumber($rowNumber);

        /** @var \Application\Entity\NmtProcurePrRow $po_row ; */
        $pr_row = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->find($pr_row_id);

        /** @var \Application\Entity\NmtProcurePoRow $po_row ; */
        $po_row = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePoRow')->find($po_row_id);

        /** @var \Application\Entity\NmtInventoryItem $item ; */
        $item = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->find($item_id);

        /** @var \Application\Entity\FinAccount $gl ; */
        $gl = $this->doctrineEM->getRepository('Application\Entity\FinAccount')->find($gl_account_id);

        if ($gl == null) {
            $errors[] = 'G/L account can\'t be empty!';
        } else {
            $entity->setGlAccount($gl);
        }

        if ($pr_row != null) {
            $entity->setPrRow($pr_row);
        }

        if ($po_row != null) {
            $entity->setPoRow($po_row);
            $entity->setPrRow($po_row->getPrRow());
            $entity->setItem($po_row->getItem());
        }

        if ($item == null) {
            $errors[] = 'Item can\'t be empty!';
        } else {
            $entity->setItem($item);
        }

        $entity->setVendorItemCode($vendorItemCode);
        $entity->setUnit($unit);

        if ($quantity == null) {
            $errors[] = $this->controllerPlugin->translate('Please  enter quantity!');
        } else {

            if (! is_numeric($quantity)) {
                $errors[] = $this->controllerPlugin->translate('Quantity must be a number.');
            } else {
                if ($quantity <= 0) {
                    $errors[] = $this->controllerPlugin->translate('Quantity must be greater than 0!');
                } else {
                    $entity->setDocQuantity($quantity);
                }
            }
        }

        if ($unitPrice !== null) {
            if (! is_numeric($unitPrice)) {
                $errors[] = $this->controllerPlugin->translate('Price is not valid. It must be a number.');
            } else {
                if ($unitPrice < 0) {
                    // accept ZERO PRICE
                    $errors[] = $this->controllerPlugin->translate('Price must be greate or equal 0!');
                } else {
                    $entity->setDocUnitPrice($unitPrice);
                }
            }
        }

        if ($taxRate !== null) {
            if (! is_numeric($taxRate)) {
                $errors[] = $this->controllerPlugin->translate('taxRate is not valid. It must be a number.');
            } else {
                if ($taxRate < 0) {
                    $errors[] = $this->controllerPlugin->translate('$taxRate must be greate than 0!');
                } else {
                    $entity->setTaxRate($taxRate);
                }
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

        // $entity->setTraceStock($traceStock);
        $entity->setRemarks($remarks);

        return $errors;
    }

    /**
     *
     * @param \Application\Entity\NmtProcureGr $target
     * @param \Application\Entity\NmtProcureGrRow $entity
     * @param \Application\Entity\MlaUsers $u
     * @param boolean $isNew
     */
    public function saveRow($target, $entity, $u, $isNew = FALSE)
    {
        if ($u == null) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        if (! $target instanceof \Application\Entity\NmtProcureGr) {
            throw new \Exception("Invalid Argument. GR Object not found!");
        }

        if (! $entity instanceof \Application\Entity\NmtProcureGrRow) {
            throw new \Exception("Invalid Argument.GR Line Object not found!");
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

        // converstion factor to purchase quantity
        $conversionFactor = $entity->getConversionFactor();
        $standardCF = $entity->getConversionFactor();

        $convertedPurchaseQuantity = $convertedPurchaseQuantity * $conversionFactor;
        $convertedPurchaseUnitPrice = $convertedPurchaseUnitPrice / $conversionFactor;

        $pr_row = $entity->getPrRow();

        if ($pr_row != null) {
            $standardCF = $standardCF * $pr_row->getConversionFactor();
        }

        // quantity /unit price is converted purchase quantity to clear PR

        $entity->setQuantity($convertedPurchaseQuantity);
        $entity->setUnitPrice($convertedPurchaseUnitPrice);

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
            $changeOn = new \DateTime();
            $entity->setRevisionNo($entity->getRevisionNo() + 1);
            $entity->setLastchangeBy($u);
            $entity->setLastchangeOn($changeOn);
        }

        $this->doctrineEM->persist($entity);
        $this->doctrineEM->flush();
    }

    /**
     *
     * @param \Application\Entity\NmtProcureGr $entity
     * @param \Application\Entity\MlaUsers $u
     *
     */
    public function doPosting($entity, array $data, $u, $isFlush = false)
    {
        if ($u == null) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        if (! $entity instanceof \Application\Entity\NmtProcureGr) {
            throw new \Exception("Invalid Argument. Good receipt is not found!");
        }

        $criteria = array(
            'isActive' => 1,
            'gr' => $entity
        );
        $gr_rows = $this->doctrineEM->getRepository('Application\Entity\NmtProcureGrRow')->findBy($criteria);

        if (count($gr_rows) == 0) {
            throw new \Exception("Good receipt is empty. No Posting will be made!");
        }

        // OK to post
        // ++++++++++++++++++++++++++++

        /**
         *
         * @todo Update Entitiy!
         */

        $changeOn = new \DateTime();

        // Assign doc number
        if ($entity->getSysNumber() == \Application\Model\Constants::SYS_NUMBER_UNASSIGNED) {
            $entity->setSysNumber($this->controllerPlugin->getDocNumber($entity));
        }

        // set posted
        $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_POSTED);
        $entity->setRevisionNo($entity->getRevisionNo() + 1);
        $entity->setLastchangeBy($u);
        $entity->setLastchangeOn($changeOn);
        $this->doctrineEM->persist($entity);

        $n = 0;
        $inventory_trx_rows = array();
        foreach ($gr_rows as $r) {

            /** @var \Application\Entity\NmtProcureGrRow $r ; */

            /**
             * Double check only.
             * Receipt of ZERO quantity not allowed
             */
            if ($r->getQuantity() == 0) {
                continute;
            }

            // UPDATE row status
            $r->setIsPosted(1);
            $r->setIsDraft(0);
            $r->setDocStatus($entity->getDocStatus());
            $r->setRowIdentifer($entity->getSysNumber() . '-' . $n);
            $r->setRowNumber($n);
            $r->setLastchangeOn($changeOn);

            if ($r->getItem() !== null) {

                /**
                 *
                 * @todo create serial number
                 *       if item with Serial
                 *       or Fixed Asset
                 */
                if ($r->getItem()->getMonitoredBy() == \Application\Model\Constants::ITEM_WITH_SERIAL_NO or $r->getItem()->getIsFixedAsset() == 1) {

                    for ($i = 0; $i < $r->getQuantity(); $i ++) {

                        // create new serial number
                        $sn_entity = new \Application\Entity\NmtInventoryItemSerial();

                        $sn_entity->setItem($r->getItem());
                        $sn_entity->setGrRow($r);
                        $sn_entity->setIsActive(1);
                        $sn_entity->setSysNumber($this->controllerPlugin->getDocNumber($sn_entity));
                        $sn_entity->setCreatedBy($u);
                        $sn_entity->setCreatedOn($changeOn);
                        $sn_entity->setToken(\Zend\Math\Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . \Zend\Math\Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
                        $this->doctrineEM->persist($sn_entity);
                    }
                }

                if ($r->getItem()->getIsStocked() == 1) {

                    // count inventory item
                    $n ++;

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

                    $stock_gr_entity->setGr($entity);
                    $stock_gr_entity->setGrRow($r);
                    $stock_gr_entity->setExchangeRate($r->getGr()
                        ->getExchangeRate());
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

                    $stock_gr_entity->setVendorItemCode($r->getVendorItemCode());
                    $stock_gr_entity->setVendorItemUnit($r->getUnit());

                    $qty = $r->getQuantity();
                    if ($r->getConvertedStandardQuantity() != null) {
                        $qty = $r->getConvertedStandardQuantity();
                    }

                    $unitPrice = $r->getUnitPrice();
                    if ($r->getConvertedStandardUnitPrice() != null) {
                        $unitPrice = $r->getConvertedStandardUnitPrice();
                    }

                    $stock_gr_entity->setQuantity($qty);
                    $stock_gr_entity->setVendorUnitPrice($unitPrice);

                    $convertedPurchaseQuantity = $r->getQuantity();
                    if ($r->getConvertedPurchaseQuantity() != null) {
                        $convertedPurchaseQuantity = $r->getConvertedPurchaseQuantity();
                    }

                    $stock_gr_entity->setConvertedPurchaseQuantity($convertedPurchaseQuantity);

                    $stock_gr_entity->setVendorUnitPrice($r->getUnitPrice());
                    $stock_gr_entity->setTrxDate($entity->getGrDate());
                    $stock_gr_entity->setCurrency($entity->getCurrency());
                    $stock_gr_entity->setRemarks('PO-GR.' . $r->getRowIdentifer());
                    $stock_gr_entity->setWh($entity->getWarehouse());
                    $stock_gr_entity->setCreatedBy($u);
                    $stock_gr_entity->setCreatedOn(new \DateTime());
                    $stock_gr_entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
                    $stock_gr_entity->setChecksum(Rand::getString(32, \Application\Model\Constants::CHAR_LIST, true));

                    $stock_gr_entity->setTaxRate($r->getTaxRate());
                    $this->doctrineEM->persist($stock_gr_entity);

                    $inventory_trx_rows[] = $stock_gr_entity;
                }
            }

            if ($n == 0) {
                throw new \Exception("No Posting will be made!");
            }

            /**
             *
             * @todo: Do Accounting Posting
             */
            $this->jeService->postGR($entity, $gr_rows, $u, $this->controllerPlugin);
            $this->doctrineEM->flush();

            try {
                $inventoryPostingStrategy = InventoryTransactionStrategyFactory::getMovementStrategy(\Inventory\Model\Constants::INVENTORY_GR_FROM_PURCHASING);

                if (! $inventoryPostingStrategy instanceof AbstractTransactionStrategy) {
                    throw new \Exception("Posting strategy is not identified for this inventory movement type!");
                }

                // do posting now
                $inventoryPostingStrategy->setContextService($this);
                $inventoryPostingStrategy->createMovement($inventory_trx_rows, $u, true, $entity->getGrDate(), $entity->getWarehouse());
            } catch (\Exception $e) {
                // left bank.

                $m = sprintf('[ERROR] %s', $e->getMessage());
                $this->getEventManager()->trigger('inventory.activity.log', __METHOD__, array(
                    'priority' => \Zend\Log\Logger::INFO,
                    'message' => $m,
                    'createdBy' => $u,
                    'createdOn' => $changeOn
                ));
            }

            $m = sprintf('[OK] Goods Receipt %s posted', $entity->getSysNumber());
            $this->getEventManager()->trigger('procure.activity.log', __METHOD__, array(
                'priority' => \Zend\Log\Logger::INFO,
                'message' => $m,
                'createdBy' => $u,
                'createdOn' => $changeOn
            ));
        }
    }

    /**
     *
     * @param \Application\Entity\NmtProcureGr $entity
     * @param array $data
     * @param \Application\Entity\MlaUsers $u
     * @param boolean $isFlush
     * @return array
     */
    public function postGR($entity, array $data, $u, $isFlush = false, $trigger = null)
    {
        $errors = array();

        if (! $u instanceof \Application\Entity\MlaUsers) {
            $errors[] = $this->controllerPlugin->translate("Invalid Argument! User is not indentided for this transaction");
        }

        if (! $entity instanceof \Application\Entity\NmtProcureGr) {
            $errors[] = $this->controllerPlugin->translate("Invalid Argument. Good receipt object is not found!");
        }

        $criteria = array(
            'isActive' => 1,
            'gr' => $entity
        );
        $gr_rows = $this->doctrineEM->getRepository('Application\Entity\NmtProcureGrRow')->findBy($criteria);

        if (count($gr_rows) == 0) {
            $errors[] = $this->controllerPlugin->translate("Invalid Argument. Good receipt is empty");
        }

        $inventoryPostingStrategy = InventoryTransactionStrategyFactory::getMovementStrategy(\Inventory\Model\Constants::INVENTORY_GR_FROM_PURCHASING);

        if (! $inventoryPostingStrategy instanceof AbstractTransactionStrategy) {
            $errors[] = $this->controllerPlugin->translate("Posting strategy is not identified for this inventory movement type!");
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // ====== VALIDATED ====== //

        $ck = $this->validateHeader($entity, $data, FALSE, TRUE);

        if (count($ck) > 0) {
            return $ck;
        }

        // $errors[] = $this->controllerPlugin->translate("Always errors for testing" . count($ck) );
        // return $errors;

        // ====== VALIDATED ====== //

        try {

            $changeOn = new \DateTime();

            // Assign doc number
            if ($entity->getSysNumber() == \Application\Model\Constants::SYS_NUMBER_UNASSIGNED) {
                $entity->setSysNumber($this->controllerPlugin->getDocNumber($entity));
            }

            // set posted
            $entity->setDocType(\Application\Model\Constants::PROCURE_DOC_TYPE_GR);
            $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_POSTED);

            $entity->setRevisionNo($entity->getRevisionNo() + 1);
            $entity->setLastchangeBy($u);
            $entity->setLastchangeOn($changeOn);
            $this->doctrineEM->persist($entity);

            $n = 0;
            $inventory_trx_rows = array();

            foreach ($gr_rows as $r) {

                /** @var \Application\Entity\NmtProcureGrRow $r ; */

                /**
                 * Double check only.
                 * Receipt of ZERO quantity not allowed
                 */
                if ($r->getQuantity() == 0) {
                    continute;
                }

                // UPDATE row status
                $r->setIsPosted(1);
                $r->setIsDraft(0);
                $r->setDocStatus($entity->getDocStatus());
                $r->setRowIdentifer($entity->getSysNumber() . '-' . $n);
                $r->setRowNumber($n);
                $r->setLastchangeOn($changeOn);

                if ($r->getItem() !== null) {

                    /**
                     *
                     * @todo create serial number if item with Serial or Fixed Asset
                     */
                    if ($r->getItem()->getMonitoredBy() == \Application\Model\Constants::ITEM_WITH_SERIAL_NO or $r->getItem()->getIsFixedAsset() == 1) {

                        for ($i = 0; $i < $r->getQuantity(); $i ++) {

                            // create new serial number
                            $sn_entity = new \Application\Entity\NmtInventoryItemSerial();

                            $sn_entity->setItem($r->getItem());
                            $sn_entity->setGrRow($r);
                            $sn_entity->setIsActive(1);
                            $sn_entity->setSysNumber($this->controllerPlugin->getDocNumber($sn_entity));
                            $sn_entity->setCreatedBy($u);
                            $sn_entity->setCreatedOn($changeOn);
                            $sn_entity->setToken(\Zend\Math\Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . \Zend\Math\Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
                            $this->doctrineEM->persist($sn_entity);
                        }
                    }

                    /**
                     * Inventory Item
                     */
                    if ($r->getItem()->getIsStocked() == 1) {

                        // count inventory item.
                        $n ++;

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

                        $stock_gr_entity->setGr($entity);
                        $stock_gr_entity->setDocCurrency($entity->getDocCurrency());
                        $stock_gr_entity->setLocalCurrency($entity->getLocalCurrency());

                        $stock_gr_entity->setGrRow($r);
                        $stock_gr_entity->setExchangeRate($r->getGr()
                            ->getExchangeRate());
                        $stock_gr_entity->setSourceClass(get_class($r));
                        $stock_gr_entity->setSourceId($r->getId());

                        $stock_gr_entity->setTransactionType($r->getTransactionType());
                        $stock_gr_entity->setCurrentState($entity->getCurrentState());
                        $stock_gr_entity->setDocStatus($r->getDocStatus());
                        $stock_gr_entity->setIsActive($r->getIsActive());

                        $stock_gr_entity->setVendor($entity->getVendor());
                        $stock_gr_entity->setDocType($entity->getDocType());
                        $stock_gr_entity->setFlow(\Application\Model\Constants::WH_TRANSACTION_IN);

                        $stock_gr_entity->setItem($r->getItem());
                        $stock_gr_entity->setPrRow($r->getPrRow());
                        $stock_gr_entity->setPoRow($r->getPoRow());

                        $stock_gr_entity->setVendorItemCode($r->getVendorItemCode());
                        $stock_gr_entity->setVendorItemUnit($r->getUnit());

                        $qty = $r->getQuantity();
                        if ($r->getConvertedStandardQuantity() != null) {
                            $qty = $r->getConvertedStandardQuantity();
                        }

                        $unitPrice = $r->getUnitPrice();
                        if ($r->getConvertedStandardUnitPrice() != null) {
                            $unitPrice = $r->getConvertedStandardUnitPrice();
                        }

                        $stock_gr_entity->setQuantity($qty);
                        $stock_gr_entity->setVendorUnitPrice($unitPrice);

                        $convertedPurchaseQuantity = $r->getQuantity();
                        if ($r->getConvertedPurchaseQuantity() != null) {
                            $convertedPurchaseQuantity = $r->getConvertedPurchaseQuantity();
                        }

                        $stock_gr_entity->setConvertedPurchaseQuantity($convertedPurchaseQuantity);

                        $stock_gr_entity->setVendorUnitPrice($r->getUnitPrice());
                        
                        $stock_gr_entity->setDocUnitPrice($r->getUnit());
                        $stock_gr_entity->setLocalUnitPrice($stock_gr_entity->getDocCurrency()*$stock_gr_entity->getExchangeRate()  );
                        
                        
                        $stock_gr_entity->setTrxDate($entity->getGrDate());
                        $stock_gr_entity->setCurrency($entity->getCurrency());
                        $stock_gr_entity->setRemarks('PO-GR.' . $r->getRowIdentifer());
                        $stock_gr_entity->setWh($entity->getWarehouse());
                        $stock_gr_entity->setCreatedBy($u);
                        $stock_gr_entity->setCreatedOn(new \DateTime());
                        $stock_gr_entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
                        $stock_gr_entity->setChecksum(Rand::getString(32, \Application\Model\Constants::CHAR_LIST, true));

                        $stock_gr_entity->setTaxRate($r->getTaxRate());
                        $this->doctrineEM->persist($stock_gr_entity);

                        $inventory_trx_rows[] = $stock_gr_entity;
                    }
                }
            }

            /**
             *
             * @todo: Do Accounting Posting
             */
            $this->jeService->postGR($entity, $gr_rows, $u, $this->controllerPlugin, false, $trigger);

            // Inventory items found.
            if ($n > 0) {

                // Post WH Goods receipt.
                $inventoryPostingStrategy->setContextService($this);
                $inventoryPostingStrategy->createMovement($inventory_trx_rows, $u, true, $entity->getGrDate(), $entity->getWarehouse(), $trigger);
            }

            // now it is time to flush
            $this->doctrineEM->flush();

            // LOGGING
            $m = sprintf('[OK] Goods Receipt %s posted', $entity->getSysNumber());
            $this->getEventManager()->trigger('procure.activity.log', $trigger, array(
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
     * @param \Application\Entity\NmtProcureGr $entity
     * @param array $data
     * @param \Application\Entity\MlaUsers $u,
     * @param bool $isFlush,
     *
     */
    public function reverse($entity, $u, $reversalDate, $reversalReason, $trigger = null)
    {
        $errors = array();

        if (! $entity instanceof \Application\Entity\NmtProcureGr) {
            $errors[] = $this->controllerPlugin->translate('Invalid Argument! Invoice not found)');
        } else {

            // only when posted.
            if ($entity->getDocStatus() !== \Application\Model\Constants::DOC_STATUS_POSTED) {
                $errors[] = $this->controllerPlugin->translate('GR not posted yet. Reserval imposible.');
            }

            // only when not reversed..
            if ($entity->getIsReversed() == 1) {
                $errors[] = $this->controllerPlugin->translate('GR reversed already.');
            }

            // check if blocked for reversed.
            if ($entity->getReversalBlocked() == 1) {
                $errors[] = $this->controllerPlugin->translate('GR can not be reversed, becasue sequence documents is created.');
            }
        }

        if (! $u instanceof \Application\Entity\MlaUsers) {
            $errors[] = $this->controllerPlugin->translate('Invalid Argument! User not found)');
        }

        $criteria = array(
            'isActive' => 1,
            'gr' => $entity
        );
        $rows = $this->doctrineEM->getRepository('Application\Entity\NmtProcureGrRow')->findBy($criteria);

        if (count($rows) == 0) {
            $errors[] = $this->controllerPlugin->translate('GR is empty. Reserval imposible.');
        }

        $inventoryPostingStrategy = InventoryTransactionStrategyFactory::getMovementStrategy(\Inventory\Model\Constants::INVENTORY_GR_FROM_PURCHASING_REVERSAL);

        if (! $inventoryPostingStrategy instanceof AbstractTransactionStrategy) {
            $errors[] = $this->controllerPlugin->translate('Posting strategy is not identified for this inventory movement type');
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // ====== VALIDATED ====== //

        /** @var \Application\Entity\NmtProcureGr $newEntity ; */
        $newEntity = clone ($entity);
        $newEntity->setInvoiceDate(null);
        $newEntity->setPostingDate(null);

        $data = array();
        $data['grDate'] = $reversalDate;

        /**
         * Check Reversal Date.
         */
        $ck = $this->checkGRDate($newEntity, $data, TRUE);

        if (count($ck) > 0) {
            $errors = array_merge($errors, $ck);
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // ====== VALIDATED ====== //

        try {

            /**
             *
             * @todo: REVERSAL WH GOODs RECEIPT
             * ================================
             */
            $criteria = array(
                'isActive' => 1,
                'gr' => $entity
            );

            $inventory_trx_rows = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryTrx')->findBy($criteria);

            /** @var \Application\Entity\NmtInventoryTrx $trx ; */

            foreach ($inventory_trx_rows as $trx) {

                if ($trx->getReversalBlocked() == 1) {
                    $errors[] = $this->controllerPlugin->translate('Sequence document is blocked for  reversal');
                    return $errors;
                }
            }

            $changeOn = new \DateTime();

            /**
             * UPDATE OLD HEADER
             */
            // =======================
            $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_REVERSED);
            $entity->setIsReversed(1);
            $entity->setReversalReason($reversalReason);
            $entity->setReversalDate(new \DateTime($reversalDate));
            $entity->setRevisionNo($entity->getRevisionNo() + 1);
            $entity->setLastchangeBy($u);
            $entity->setLastchangeOn($changeOn);
            $this->doctrineEM->persist($entity);

            $newEntity->setDocStatus(\Application\Model\Constants::DOC_STATUS_REVERSED);
            $newEntity->setDocType(\Application\Model\Constants::PROCURE_DOC_TYPE_INVOICE_REVERSAL);
            $newEntity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
            $entity->setSysNumber($this->controllerPlugin->getDocNumber($entity));
            $entity->setRemarks($reversalReason);

            $this->doctrineEM->persist($newEntity);

            /**
             * POSTING ROW
             */
            // ======================

            $n = 0;
            foreach ($rows as $r) {

                // ignore row with Zero quantity
                if ($r->getQuantity() == 0) {
                    $r->setIsActive(0);
                    continue;
                }

                $n ++;

                /** @var \Application\Entity\NmtProcureGrRow $old_r ; */
                $old_r = clone ($r);
                $old_r->setGr($newEntity); // important!
                $this->doctrineEM->persist($old_r);

                /** @var \Application\Entity\NmtProcureGrRow $r ; */

                // important!
                $r->setRemarks($entity->getRemarks());
                $r->setIsReversed(1);
                $r->setReversalReason($reversalReason);
                $r->setReversalDate(new \DateTime($reversalDate));
                $this->doctrineEM->persist($r);
            }

            /**
             * POSTING JOURNAL ENTRY
             * =============================
             */

            $this->jeService->reverseGR($entity, $rows, $u, $this->controllerPlugin, false, true, $trigger);

            // if have inventory item.
            if (count($inventory_trx_rows) > 0) {
                // do posting now
                $inventoryPostingStrategy->setContextService($this);
                $inventoryPostingStrategy->createMovement($inventory_trx_rows, $u, true, $entity->getReversalDate(), $entity->getWarehouse(), $trigger);
            }

            /**
             * UPDATING SEARCH INDEX
             */
            // $apSearchService = new \Procure\Service\APSearchService();
            // $apSearchService->indexingAPRows($ap_rows);

            // Time to flush.
            $this->doctrineEM->flush();

            /**
             * LOGGING
             * ======================
             */
            $m = sprintf('[OK] GRPO  %s reversed.', $entity->getSysNumber());
            $this->getEventManager()->trigger('procure.activity.log', $trigger, array(
                'priority' => \Zend\Log\Logger::INFO,
                'message' => $m,
                'createdBy' => $u,
                'createdOn' => $changeOn
            ));

            // Time to flush.
            $this->doctrineEM->flush();
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }

        return $errors;
    }

    /**
     *
     * @param \Application\Entity\NmtProcureGr $entity
     * @param \Application\Entity\NmtProcurePo $target
     * @param \Application\Entity\MlaUsers $u
     *
     */
    public function copyFromPO($entity, $target, $u, $isFlush = false)
    {
        if (! $entity instanceof \Application\Entity\NmtProcureGr) {
            throw new \Exception("Invalid Argument! GR Object is not found.");
        }

        if (! $target instanceof \Application\Entity\NmtProcurePo) {
            throw new \Exception("Invalid Argument! PO Object is not found.");
        }

        if (! $u instanceof \Application\Entity\MlaUsers) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        $createdOn = new \DateTime();

        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        $po_rows = $res->getPOStatus($target->getId(), $target->getToken());

        if ($po_rows == null) {
            throw new \Exception("PO is empty!");
        }

        $n = 0;
        foreach ($po_rows as $l) {

            // if all received, ignore it.
            if ($l['open_gr_qty'] == 0) {
                continue;
            }

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

            // new
            $convertedPurchaseQuantity = $r->getQuantity();

            if ($r->getConvertedPurchaseQuantity() != null) {
                $convertedPurchaseQuantity = $r->getConvertedPurchaseQuantity();
            }

            // new
            $convertedStandardQuantity = $r->getQuantity();

            if ($r->getConvertedStandardQuantity() != null) {
                $convertedStandardQuantity = $r->getConvertedStandardQuantity();
            }

            // new
            $convertedStandardUnitPrice = $r->getUnitPrice() / $convertedStandardQuantity;

            $convertedStockQuantity = $r->getQuantity();

            if ($r->getConvertedStockQuantity() != null) {
                $convertedStockQuantity = $r->getConvertedStockQuantity();
            }

            $convertedStockUnitPrice = $r->getUnitPrice() / $convertedStockQuantity;

            $row_tmp->setConvertedPurchaseQuantity($convertedPurchaseQuantity);
            $row_tmp->setConvertedStandardQuantity($convertedStandardQuantity);
            $row_tmp->setConvertedStockQuantity($convertedStockQuantity);
            $row_tmp->setConvertedStandardUnitPrice($convertedStandardUnitPrice);
            $row_tmp->setConvertedStockUnitPrice($convertedStockUnitPrice);

            $netAmount = $row_tmp->getQuantity() * $row_tmp->getUnitPrice();
            $taxAmount = $netAmount * $row_tmp->getTaxRate() / 100;
            $grossAmount = $netAmount + $taxAmount;

            $row_tmp->setNetAmount($netAmount);
            $row_tmp->setTaxAmount($taxAmount);
            $row_tmp->setGrossAmount($grossAmount);

            $row_tmp->setCreatedBy($u);
            $row_tmp->setCreatedOn($createdOn);

            $row_tmp->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
            $row_tmp->setRemarks("Ref: PO #" . $r->getRowIdentifer());
            $row_tmp->setExwUnitPrice($r->getExwUnitPrice());

            $this->doctrineEM->persist($row_tmp);
        }

        if ($n == 0) {
            throw new \Exception("PO is empty!");
        }

        if ($isFlush == True) {
            $this->doctrineEM->flush();
        }
    }

    /**
     *
     * @param \Application\Entity\NmtProcureGr $entity
     * @param \Application\Entity\NmtProcurePo $target
     * @param \Application\Entity\MlaUsers $u
     *
     */
    public function selectFromPO($entity, $target, $u, $isFlush = false)
    {}

    /**
     *
     * @param \Application\Entity\NmtProcureGr $entity
     * @param array $data
     * @param boolean $isPosting
     */
    private function checkVendor(\Application\Entity\NmtProcureGr $entity, $data, $isPosting)
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
            $errors[] = $this->controllerPlugin->translate('Vendor not found!');
        }
        return $errors;
    }

    /**
     *
     * @param \Application\Entity\NmtProcureGr $entity
     * @param array $data
     * @param boolean $isPosting
     */
    private function checkCurrency(\Application\Entity\NmtProcureGr $entity, $data, $isPosting)
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
            $entity->setDocCurrency($currency);
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
            $errors[] = $this->controllerPlugin->translate('Currency not set.');
        }

        return $errors;
    }

    /**
     *
     * @param \Application\Entity\NmtProcureGr $entity
     * @param array $data
     * @param boolean $isPosting
     */
    private function checkGrDate(\Application\Entity\NmtProcureGr $entity, $data, $isPosting)
    {
        $errors = array();

        if (! isset($data['grDate'])) {
            $errors[] = $this->controllerPlugin->translate('Good Receipt Date input is not set!');
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
     * @param \Application\Entity\NmtProcureGr $entity
     * @param array $data
     * @param boolean $isPosting
     */
    private function checkWarehouse(\Application\Entity\NmtProcureGr $entity, $data, $isPosting)
    {
        $errors = array();

        if (! isset($data['target_wh_id'])) {
            $errors[] = $this->controllerPlugin->translate('Warehouse ID is not set!');
            return $errors;
        }
        // ==========OK=========== //

        $warehouse_id = (int) $data['target_wh_id'];
        $warehouse = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->find($warehouse_id);
        $entity->setWarehouse($warehouse);

        if ($isPosting == TRUE and $entity->getWarehouse() == null) {
            $errors[] = $this->controllerPlugin->translate('Warehouse not found!');
        }
        return $errors;
    }

    /**
     *
     * @param \Application\Entity\FinVendorInvoice $entity
     * @param array $data
     * @param boolean $isPosting
     */
    private function checkPODate(\Application\Entity\NmtProcureGr $entity, $data, $isPosting)
    {
        $errors = array();

        if (! isset($data['contractDate'])) {
            $errors[] = $this->controllerPlugin->translate('PO Date input is not set!');
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // ==========OK=========== //

        $poDate = $data['contractDate'];

        $validator = new Date();

        if (! $poDate == null) {
            if (! $validator->isValid($poDate)) {
                $errors[] = $this->controllerPlugin->translate('PO Date is not correct or empty!');
            } else {
                $entity->setContractDate(new \DateTime($poDate));
            }
        }
        return $errors;
    }
}
