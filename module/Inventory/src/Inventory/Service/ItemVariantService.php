<?php
namespace Inventory\Service;

use Application\Service\AbstractService;
use Zend\Math\Rand;

/**
 * Item Variant Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemVariantService extends AbstractService
{

    /**
     *
     * @param \Application\Entity\NmtProcurePo $target
     * @param \Application\Entity\NmtProcurePoRow $entity
     * @return NULL[]|string[]
     */
    public function validateRow($target, $entity, $data)
    {

        // do validating
        $errors = array();

        if (! $target instanceof \Application\Entity\NmtProcurePo) {
            throw new \Exception("Invalid Argument. PO Object not found!");
        }

        if (! $entity instanceof \Application\Entity\NmtProcurePoRow) {
            throw new \Exception("Invalid Argument. PO Line Object not found!");
        }

        $item_id = (int) $data['item_id'];
        $pr_row_id = (int) $data['pr_row_id'];
        $isActive = (int) $data['isActive'];

        $rowNumber = $data['rowNumber'];

        $vendorItemCode = $data['vendorItemCode'];
        $unit = $data['unit'];
        $conversionFactor = $data['conversionFactor'];

        $quantity = $data['quantity'];
        $unitPrice = $data['unitPrice'];
        $exwUnitPrice = $data['exwUnitPrice'];

        $taxRate = $data['taxRate'];

        $remarks = $data['remarks'];

        if ($isActive != 1) {
            $isActive = 0;
        }

        $entity->setIsActive($isActive);
        $entity->setRowNumber($rowNumber);

        // Inventory Transaction and validating.

        /**@var \Application\Entity\NmtProcurePrRow $pr_row ;*/
        $pr_row = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->find($pr_row_id);

        /**@var \Application\Entity\NmtInventoryItem $item ;*/
        $item = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->find($item_id);

        if ($pr_row == null) {
            // $errors[] = 'Item can\'t be empty!';
        } else {
            $entity->setPrRow($pr_row);
        }

        if ($item == null) {
            $errors[] = $this->controllerPlugin->translate('Item is not found. Please select item!');
        } else {
            $entity->setItem($item);
        }

        $entity->setVendorItemCode($vendorItemCode);
        $entity->setUnit($unit);
        $entity->setDocUnit($unit);

        if (! is_numeric($quantity)) {
            $errors[] = $this->controllerPlugin->translate('Quantity must be a number.');
        } else {
            if ($quantity <= 0) {
                $errors[] = $this->controllerPlugin->translate('Quantity must be > 0!');
            } else {
                // $entity->setQuantity($quantity);
                $entity->setDocQuantity($quantity);
            }
        }

        if (! is_numeric($unitPrice)) {
            $errors[] = $this->controllerPlugin->translate('Price is not valid. It must be a number.');
        } else {
            if ($unitPrice < 0) {
                $errors[] = $this->controllerPlugin->translate('Price must be >   0!');
            } else {
                // $entity->setUnitPrice($unitPrice);
                $entity->setDocUnitPrice($unitPrice);
            }
        }

        if ($exwUnitPrice != null) {
            if (! is_numeric($exwUnitPrice)) {
                $errors[] = $this->controllerPlugin->translate('Exw Price is not valid. It must be a number.');
            } else {
                if ($exwUnitPrice <= 0) {
                    $errors[] = $this->controllerPlugin->translate('Exw Price must be >=0!');
                } else {
                    $entity->setExwUnitPrice($exwUnitPrice);
                    if ($entity->getQuantity() > 0) {
                        $entity->setTotalExwPrice($entity->getExwUnitPrice() * $entity->getDocQuantity());
                    }
                }
            }
        }

        if ($taxRate == null) {
            $taxRate = 0;
        }

        if (! is_numeric($taxRate)) {
            $errors[] = $this->controllerPlugin->translate('TaxRate is not valid. It must be a number.');
        } else {
            if ($taxRate < 0) {
                $errors[] = $this->controllerPlugin->translate('TaxRate must be > 0');
            } else {
                $entity->setTaxRate($taxRate);
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

        $entity->setRemarks($remarks);

        return $errors;
    }

    /**
     *
     * @param \Application\Entity\NmtProcurePo $target
     * @param \Application\Entity\NmtProcurePoRow $entity
     * @param \Application\Entity\MlaUsers $u
     * @param boolean $isNew
     */
    public function saveRow($target, $entity, $u, $isNew = FALSE)
    {
        if ($u == null) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        if (! $target instanceof \Application\Entity\NmtProcurePo) {
            throw new \Exception("Invalid Argument. PO Object not found!");
        }

        if (! $entity instanceof \Application\Entity\NmtProcurePoRow) {
            throw new \Exception("Invalid Argument. PO Line Object not found!");
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

        $pr_row = $entity->getPrRow();

        if ($pr_row != null) {
            $convertedPurchaseQuantity = $convertedPurchaseQuantity * $conversionFactor;
            $convertedPurchaseUnitPrice = $convertedPurchaseUnitPrice / $conversionFactor;
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

    

}
