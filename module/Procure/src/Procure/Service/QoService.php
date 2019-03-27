<?php
namespace Procure\Service;

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
        
        $remarks = $data['remarks'];
        $isActive = (int) $data['isActive'];
        
        if ($isActive !== 1) {
            $isActive = 0;
        }
        
        $entity->setIsActive($isActive);
        $entity->setRemarks($remarks);
        
        
        // check vendor. ok
        $ck = $this->checkVendor($entity, $data, $isPosting);
        if (count($ck) > 0) {
            $errors = array_merge($errors, $ck);
        }
        
        $ck = $this->checkIncoterm($entity, $data, $isPosting);
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
        
        $entity->setDocType(\Application\Model\Constants::PROCURE_DOC_TYPE_QUOTE);
        

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

        $vendorItemCode = $data['vendorItemCode'];
        $unit = $data['unit'];
        $conversionFactor = $data['conversionFactor'];

        // $converstionText = $data['converstionText'];

        $quantity = $data['quantity'];
        $unitPrice = $data['unitPrice'];
        $taxRate = $data['taxRate'];
        $rowNumber = $data['rowNumber'];
        $vendorItemName = $data['vendorItemName'];
        
        
        
        // $traceStock = $data['traceStock'];

        $remarks = $data['remarks'];
        $descriptionText = $data['descriptionText'];
        
        
        $entity->setRemarks($remarks);
        $entity->setDescriptionText($descriptionText);
        $entity->setVendorItemName($vendorItemName);
        

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
                 }
            }
        }

        if ($taxRate != null) {
            if (! is_numeric($taxRate)) {
                $errors[] = 'Tax Rate is not valid. It must be a number.';
            } else {
                if ($taxRate < 0) {
                    $errors[] = 'Tax Rate must be greate than 0!';
                } else {
                    $entity->setTaxRate($taxRate);
                 }
            }
        }
        
        if ($rowNumber != null) {
            if (! is_numeric($taxRate)) {
                $errors[] = 'Row Number is not valid. It must be a number.';
            } else {
                if ($taxRate < 0) {
                    $errors[] = 'Row Number must be greate than 0!';
                } else {
                    $entity->setRowNumber($rowNumber);
                }
            }
        }

         return $errors;
    }
    
    /**
     * 
     * @param \Application\Entity\NmtProcureQoRow $entity
     * @param array $data
     */
    public function validateRowAjax($entity, $data)
    {
        $errors = array();
    
        // see quote/show/phtml
        $quantity = $data['row_quantity'];
        $unitPrice = $data['row_unit_price'];
        $faRemarks = $data['fa_remarks'];
        $rowNumber = $data['row_number'];
        
        
        $entity->setFaRemarks($faRemarks);
    
        if ($quantity == null) {
            $errors[] = 'Please  enter quantity!';
        } else {
            
            if (!is_numeric($quantity)) {
                $errors[] = 'Quantity must be a number.';
            } else {
                if ($quantity <= 0) {
                    $errors[] = 'Quantity must be greater than 0!';
                } else {
                    $entity->setQuantity($quantity);
                }
            }
        }
        
        if ($unitPrice == null) {
            $errors[] = 'Price is not given. It must be a number.';
        } else {
            
            $unitPrice = str_replace(",", "",$unitPrice);
            if (!is_numeric($unitPrice)) {
                $errors[] = $unitPrice . ' // Price is not valid. It must be a number.';
            } else {
                if ($unitPrice <= 0) {
                    $errors[] = 'Price must be greate than 0!';
                } else {
                    $entity->setUnitPrice($unitPrice);
                }
            }
        }
        
        if ($rowNumber != null) {
            if (! is_numeric($rowNumber)) {
                $errors[] = 'Row Number is not valid. It must be a number.';
            } else {
                if ($rowNumber < 0) {
                    $errors[] = 'Row Number must be greate than 0!';
                } else {
                    $entity->setRowNumber($rowNumber);
                }
            }
        }
        
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
        
        $netAmount = $entity->getQuantity() * $entity->getUnitPrice();
        $entity->setNetAmount($netAmount);
        
        $taxAmount = $entity->getNetAmount() * $entity->getTaxRate() / 100;
        $grossAmount = $entity->getNetAmount() + $taxAmount;
        $entity->setTaxAmount($taxAmount);
        $entity->setGrossAmount($grossAmount);
        
        

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
     * @param \Application\Entity\NmtProcureQo $entity
     * @param \Application\Entity\MlaUsers $u,
     * @param bool $isFlush,
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function post($entity, $u, $isFlush = false, $isPosting = 1)
    {
        if (! $entity instanceof \Application\Entity\NmtProcureQo) {
            throw new \Exception("Invalid Argument! Quotation can't not found.");
        }

        if (! $u instanceof \Application\Entity\MlaUsers) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        $criteria = array(
            'isActive' => 1,
            'qo' => $entity
        );
        $ap_rows = $this->doctrineEM->getRepository('Application\Entity\NmtProcureQoRow')->findBy($criteria);

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
        $entity->setRevisionNo($entity->getRevisionNo() + 1);
        $entity->setLastchangeBy($u);
        $entity->setLastchangeOn($changeOn);
        $this->doctrineEM->persist($entity);

        $n = 0;
        foreach ($ap_rows as $r) {

            /** @var \Application\Entity\NmtProcureQoRow $r ; */

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
        }

        /*
            $apSearchService = new \Procure\Service\APSearchService();
            $apSearchService->indexingAPRows($ap_rows);
        */
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
    private function checkIncoterm(\Application\Entity\NmtProcureQo $entity, $data, $isPosting)
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
           
               // check invoice number
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
                $entity->setContractDate(new \DateTime($refDate));
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
    
    function is_decimal ($price){
        $value= trim($price); // trim space keys
        str_replace(",", "",$value);
        return $value;
    }
}
