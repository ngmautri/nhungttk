<?php
namespace Procure\Service;

use Application\Service\AbstractService;
use Zend\Math\Rand;
use Procure\Helper\RowConvertor;
use Zend\Validator\Date;

/**
 * PO Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoService extends AbstractService
{

    /**
     *
     * @param \Application\Entity\NmtProcurePo $entity
     * @param array $data
     * @/param boolean $isPosting
     */
    public function validateHeader(\Application\Entity\NmtProcurePo $entity, $data, $isPosting = FALSE, $isPosted = FALSE)
    {
        $errors = array();

        if (! $entity instanceof \Application\Entity\NmtProcurePo) {
            $errors[] = $this->controllerPlugin->translate('AP invoicePO');
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

        $remarks = $data['remarks'];

        // only remarks changeble, when posted.
        if ($isPosted == TRUE) {
            $entity->setRemarks($remarks);
            return null;
        }

        $contractDate = $data['contractDate'];
        $contractNo = $data['contractNo'];
        $vendor_id = (int) $data['vendor_id'];
        $isActive = (int) $data['isActive'];
        

        if ($isActive !== 1) {
            $isActive = 0;
        }

        $entity->setIsActive($isActive);

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

        // check currency and exchange rate
        $ck = $this->checkCurrency($entity, $data, $isPosting);
        if (count($ck) > 0) {
            $errors = array_merge($errors, $ck);
        }
        
       

        if ($contractNo == "") {
            $errors[] = 'Contract is not correct or empty!';
        } else {
            $entity->setContractNo($contractNo);
        }

        $validator = new Date();

        if (! $validator->isValid($contractDate)) {
            $errors[] = 'Contract Date is not correct or empty!';
        } else {
            $entity->setContractDate(new \DateTime($contractDate));
        }
        
        // check currency and exchange rate
        $ck = $this->checkIncoterm($entity, $data, $isPosting);
        if (count($ck) > 0) {
            $errors = array_merge($errors, $ck);
        }
        
        // check currency and exchange rate
        $ck = $this->checkPaymentTerm($entity, $data, $isPosting);
        if (count($ck) > 0) {
            $errors = array_merge($errors, $ck);
        }
        
        $entity->setRemarks($remarks);

        return $errors;
    }
    

    /**
     *
     * @param \Application\Entity\NmtProcurePo $entity
     * @param \Application\Entity\MlaUsers $u
     * @param boolean $isNew
     */
    public function saveHeader($entity, $u, $isNew = FALSE)
    {
        if ($u == null) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        if (! $entity instanceof \Application\Entity\NmtProcurePo) {
            throw new \Exception("Invalid Argument. PO Object not found!");
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
     * @param \Application\Entity\NmtProcurePo $target
     * @param \Application\Entity\NmtProcurePoRow $entity
     * @return NULL[]|string[]
     */
    public function validateRow($target, $entity, $data)
    {

        // do validating
        $errors = array();

        if (! $target instanceof \Application\Entity\NmtProcurePo) {
            $errors[] = $this->controllerPlugin->translate('PO is not found!');
        } else {
            if ($target->getLocalCurrency() == null) {
                $errors[] = $this->controllerPlugin->translate('Local currency is not found!');
            }
        }

        if (! $entity instanceof \Application\Entity\NmtProcurePoRow) {
            $errors[] = $this->controllerPlugin->translate('PO Row is not found!');
        }

        if ($data == null) {
            $errors[] = $this->controllerPlugin->translate('No input given');
        }

        if (count($errors) > 0) {
            return $errors;
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
        
        $descriptionText = $data['descriptionText'];
        $descriptionText = $data['descriptionText'];
        

        // Inventory Transaction and validating.
        
        $entity->setPrRow(null);
        
        if ($pr_row_id > 0) {
            /**@var \Application\Entity\NmtProcurePrRow $pr_row ;*/
            $pr_row = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->find($pr_row_id);
            if ($pr_row == null) {
                // $errors[] = 'Item can\'t be empty!';
            } else {
                $entity->setPrRow($pr_row);
            }
        }

        if ($item_id > 0) {

            /**@var \Application\Entity\NmtInventoryItem $item ;*/
            $item = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->find($item_id);
        }
        
        if ($item == null) {
            $errors[] = $this->controllerPlugin->translate('Item is not found. Please select item!');
        } else {
            $entity->setItem($item);
        }
        

        $entity->setVendorItemCode($vendorItemCode);
        $entity->setVendorItemName($vendorItemCode);
        
        
        
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

        //$entity->setRemarks($remarks . 'pr_id:' . $pr_row_id);
        $entity->setRemarks($remarks);
        $entity->setDescriptionText($descriptionText);
        
        
        return $errors;
    }

    /**
     *
     * @param \Application\Entity\NmtProcurePoRow $entity
     * @return NULL[]|string[]
     */
    public function validateRow1($entity, $data)
    {

        // do validating
        $errors = array();

        if (! $entity instanceof \Application\Entity\NmtProcurePoRow) {
            $errors[] = $this->controllerPlugin->translate('PO Row is not found!');
        }

        if ($data == null) {
            $errors[] = $this->controllerPlugin->translate('No input given');
        }

        if (count($errors) > 0) {
            return $errors;
        }

        $quantity = $data['doc_qty'];
        $unitPrice = $data['doc_unit_price'];
        $faRemarks = $data['fa_remarks'];
        $rowNumber = $data['row_number'];

        $entity->setRowNumber($rowNumber);
        $entity->setFaRemarks($faRemarks);
        // Inventory Transaction and validating.

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

        $unitPrice = str_replace(",", "", $unitPrice);

        if (! is_numeric($unitPrice)) {
            $errors[] = $this->controllerPlugin->translate($unitPrice . ' // Price is not valid. It must be a number.');
        } else {
            if ($unitPrice < 0) {
                $errors[] = $this->controllerPlugin->translate('Price must be >   0!');
            } else {
                // $entity->setUnitPrice($unitPrice);
                $entity->setDocUnitPrice($unitPrice);
            }
        }

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

    /**
     *
     * @param \Application\Entity\NmtProcurePo $entity
     * @param \Application\Entity\MlaUsers $u
     *
     */
    public function doPosting($entity, $u, $isFlush = false)
    {
        if ($u == null) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        if (! $entity instanceof \Application\Entity\NmtProcurePo) {
            throw new \Exception("Invalid Argument. PO Object not found!");
        }

        $criteria = array(
            'isActive' => 1,
            'po' => $entity
        );
        $po_rows = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePoRow')->findBy($criteria);

        if (count($po_rows) == 0) {
            throw new \Exception("PO is empty. No Posting will be made!");
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
        $entity->setDocType(\Application\Model\Constants::PROCURE_DOC_TYPE_PO);
        

        // set posted
        $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_POSTED);
        $entity->setRevisionNo($entity->getRevisionNo() + 1);
        $entity->setLastchangeBy($u);
        $entity->setLastchangeOn($changeOn);
        $this->doctrineEM->persist($entity);

        $n = 0;
        foreach ($po_rows as $r) {

            /** @var \Application\Entity\NmtProcurePoRow $r ; */

            /**
             * Double check only.
             * Receipt of ZERO quantity not allowed
             */
            if ($r->getQuantity() == 0) {
                continute;
            }

            $n ++;

            // UPDATE row status
            $r->setIsPosted(1);
            $r->setIsDraft(0);
            $r->setDocStatus($entity->getDocStatus());
            $r->setRowIdentifer($entity->getSysNumber() . '-' . $n);
            $r->setRowNumber($n);
            $r->setLastchangeOn($changeOn);
        }

        $poSearchService = new \Procure\Service\PoSearchService();
        $poSearchService->indexingPoRows($po_rows);

        if ($isFlush == true) {
            $this->doctrineEM->flush();
        }
    }

    /**
     *
     * @param \Application\Entity\NmtProcurePo $po
     * @param \Application\Entity\NmtProcureQo $qo
     * @param \Application\Entity\MlaUsers $u
     * @param boolean $isFlush
     */
    public function copyFromQO(\Application\Entity\NmtProcurePo $po, \Application\Entity\NmtProcureQo $qo, \Application\Entity\MlaUsers $u, $isFlush = false)
    {
        if (! $po instanceof \Application\Entity\NmtProcurePo) {
            throw new \Exception("Invalid Argument! PO Object can't not found.");
        }

        if (! $qo instanceof \Application\Entity\NmtProcureQo) {
            throw new \Exception("Invalid Argument! Quotation Object can't not found.");
        }

        $criteria = array(
            'isActive' => 1,
            'qo' => $qo
        );
        $rows = $this->doctrineEM->getRepository('Application\Entity\NmtProcureQoRow')->findBy($criteria);

        if (count($rows) == 0) {
            throw new \Exception($qo . " // Quotation is empty. No thing will be copied!");
        }

        $convertor = new RowConvertor();
        $n = 0;

        foreach ($rows as $r) {

            /** @var \Application\Entity\NmtProcureQoRow $r ; */

            if ($r->getQuantity() == 0) {
                continue;
            }

            $po_row = new \Application\Entity\NmtProcurePoRow();
            $po_row->setPo($po);
            $po_row = $convertor->createPoRowfromQoRow($r, $po_row, $u);

            if ($po_row !== null) {
                $n ++;
                // $po_row->setRowNumber($n);
                $this->doctrineEM->persist($po_row);
            }
        }

        if ($n == 0) {
            throw new \Exception("Quotation is empty. No thing will be copied!");
        }

        if ($isFlush == true) {
            $this->doctrineEM->flush();
        }
    }

    /**
     *
     * @param \Application\Entity\NmtProcurePo $entity
     * @param \Application\Entity\MlaUsers $u
     *
     */
    public function updateStatus($entity, $u, $isFlush = false)
    {}

    /**
     *
     * @param \Application\Entity\NmtProcurePo $entity
     * @param \Application\Entity\MlaUsers $u
     *
     */
    public function copyToGR($entity, $u, $isFlush = false)
    {}

    /**
     *
     * @param \Application\Entity\NmtProcurePo $entity
     * @param array $data
     * @param boolean $isPosting
     */
    private function checkCurrency(\Application\Entity\NmtProcurePo $entity, $data, $isPosting)
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
     * @param \Application\Entity\NmtProcurePo $entity
     * @param array $data
     * @param boolean $isPosting
     */
    private function checkIncoterm(\Application\Entity\NmtProcurePo $entity, $data, $isPosting)
    {
        $errors = array();
        if (! isset($data['incoterm_id'])) {
            $errors[] = $this->controllerPlugin->translate('Incoterm id is not set!');
            return $errors;
        }
        
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
            //$errors[] = $this->controllerPlugin->translate('Vendor can\'t be empty. Please select a vendor!');
        }
        return $errors;
    }

    /**
     *
     * @param \Application\Entity\FinVendorInvoice $entity
     * @param array $data
     * @param boolean $isPosting
     */
    private function checkPaymentTerm(\Application\Entity\NmtProcurePo $entity, $data, $isPosting)
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
