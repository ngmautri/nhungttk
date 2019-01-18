<?php
namespace Procure\Helper;

use Zend\Math\Rand;

/**
 * Helper
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class RowConvertor
{

    /**
     *
     * @param \Application\Entity\NmtProcureQoRow $s
     * @param \Application\Entity\NmtProcurePoRow $row
     * @param \Application\Entity\MlaUsers $u
     * @return NULL|\Application\Entity\NmtProcurePoRow
     */
    public function createPoRowfromQoRow(\Application\Entity\NmtProcureQoRow $s, \Application\Entity\NmtProcurePoRow $row = null, \Application\Entity\MlaUsers $u)
    {
        if (! $s instanceof \Application\Entity\NmtProcureQoRow) {
            return null;
        }

        if (! $u instanceof \Application\Entity\MlaUsers) {
            return null;
        }

        if ($row == null) {
            $row = new \Application\Entity\NmtProcurePoRow();
        }
    
        $row->setIsPosted(0);
        $row->setIsActive(1);
        $row->setIsDraft(1);
        $row->setRowNumber($s->getRowNumber());
        $row->setPrRow($s->getPrRow());
        $row->setItem($s->getItem());
   
        $row->setUnit($s->getUnit());
        
        
        $row->setDocQuantity($s->getQuantity());
        $row->setDocUnit($s->getUnit());
        $row->setDocUnitPrice($s->getUnitPrice());
        $row->setConversionFactor($s->getConversionFactor());
        $row->setTaxRate($s->getTaxRate());
        $row->setExwUnitPrice($s->getUnitPrice());
        $row->setVendorItemCode($s->getVendorItemCode());
        $row->setDiscountRate($s->getDiscountRate());
        $row->setRemarks($s->getRemarks());
        $row->setCreatedBy($u);
        $row->setCreatedOn(new \DateTime());
        $row->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
        $row->setRemarks("Ref: " . $s->getRowIdentifer());
        
        
        $netAmount = $row->getDocQuantity() * $row->getDocUnitPrice();
        $row->setNetAmount($netAmount);
        
        $taxAmount = $row->getNetAmount() * $row->getTaxRate() / 100;
        $grossAmount = $row->getNetAmount() + $taxAmount;
        
        $row->setTaxAmount($taxAmount);
        $row->setGrossAmount($grossAmount);
        
        $convertedPurchaseQuantity = $row->getDocQuantity();
        $convertedPurchaseUnitPrice = $row->getDocUnitPrice();
        
        $conversionFactor = $row->getConversionFactor();
        $standardCF = $row->getConversionFactor();
        
        $pr_row = $row->getPrRow();
        
        if ($pr_row != null) {
            $convertedPurchaseQuantity = $convertedPurchaseQuantity * $conversionFactor;
            $convertedPurchaseUnitPrice = $convertedPurchaseUnitPrice / $conversionFactor;
            $standardCF = $standardCF * $pr_row->getConversionFactor();
        }
        
        // quantity /unit price is converted purchase quantity to clear PR
        
        $row->setQuantity($convertedPurchaseQuantity);
        $row->setUnitPrice($convertedPurchaseUnitPrice);
        
        $convertedStandardQuantity = $row->getDocQuantity();
        $convertedStandardUnitPrice = $row->getDocUnitPrice();
        
        $item = $row->getItem();
        if ($item !== null) {
            $convertedStandardQuantity = $convertedStandardQuantity * $standardCF;
            $convertedStandardUnitPrice = $convertedStandardUnitPrice / $standardCF;
        }
        
        // calculate standard quantity
        $row->setConvertedPurchaseQuantity($convertedPurchaseQuantity);
        $row->setConvertedPurchaseUnitPrice($convertedPurchaseUnitPrice);
        
        $row->setConvertedStandardQuantity($convertedStandardQuantity);
        $row->setConvertedStandardUnitPrice($convertedStandardUnitPrice);
        
        return $row;
    }
}
