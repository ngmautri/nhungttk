<?php
namespace Procure\Service;

use Inventory\Model\GR\AbstractGRStrategy;
use Inventory\Model\GR\GRStrategyFactory;
use Procure\Model\Ap\AbstractAPRowPostingStrategy;
use Application\Entity\FinVendorInvoiceRow;
use Application\Service\AbstractService;
use Zend\Math\Rand;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class APInvoiceService extends AbstractService
{

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
        $gl_account_id = $data['gl_account_id'];
        $cost_center_id = $data['cost_center_id'];
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
        $entity->setDocStatus($target->getDocStatus());
        $entity->setRowNumber($rowNumber);
        $entity->setVendorItemCode($vendorItemCode);
        $entity->setUnit($unit);

        $pr_row = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->find($pr_row_id);
        $item = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->find($item_id);
        $gl = $this->doctrineEM->getRepository('Application\Entity\FinAccount')->find($gl_account_id);
        $costCenter = $this->doctrineEM->getRepository('Application\Entity\FinCostCenter')->find($cost_center_id);

        if ($gl == null) {
            $errors[] = $this->controllerPlugin->translate('G/L account can\'t be empty!. Pls select G/L');
        } else {
            $entity->setGlAccount($gl);
        }

        $entity->setCostCenter($costCenter);

        // PR and be empty
        $entity->setPrRow($pr_row);

        // Item cant be empty
        if ($item == null) {
            $errors[] = $this->controllerPlugin->translate('Item can\'t be empty. Please select item.');
        } else {
            $entity->setItem($item);
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
    public function post($entity, $u, $isFlush = false)
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
            
            $row_tmp->setDocQuantity($l['open_ap_qty']);
            $row_tmp->setDocUnitPrice($r->getUnitPrice());
       
            $row_tmp->setConversionFactor(1);
            $row_tmp->setConvertedPurchaseQuantity($r->getQuantity());
            
            $row_tmp->setUnit($r->getUnit());
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
    
}
