<?php
namespace Payment\Service;

use Application\Entity\FinVendorInvoiceRow;
use Application\Service\AbstractService;
use Zend\Math\Rand;
use Zend\Validator\Date;

/**
 * PO PaymentService.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class POPaymentService extends AbstractService
{

    /**
     *
     * @param \Application\Entity\PmtOutgoing $entity
     * @param array $data
     * @param boolean $isPosting
     */
    public function validateHeader(\Application\Entity\PmtOutgoing $entity, $data, $isPosting = false)
    {
        $errors = array();

        if (! $entity instanceof \Application\Entity\PmtOutgoing) {
            $errors[] = $this->controllerPlugin->translate('Payment  is not found!');
        }else{
            
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

        $sapDoc = $data['sapDoc'];
        $remarks = $data['remarks'];

        if ($sapDoc == "") {
            $sapDoc = "N/A";
        }
        $entity->setSapDoc($sapDoc);
        $entity->setRemarks($remarks);

        /**
         * Check Doc Date.
         */
        $ck = $this->checkDocDate($entity, $data, $isPosting);
        if (count($ck) > 0) {
            $errors = array_merge($errors, $ck);
        }

        /**
         * Check Posting Date.
         */
        $ck = $this->checkPostingDate($entity, $data, $isPosting);
        if (count($ck) > 0) {
            $errors = array_merge($errors, $ck);
        }

        /**
         * Check Payment Method.
         */
        if (isset($data['pmt_method_id'])) {

            $pmt_method_id = $data['pmt_method_id'];

            /** @var \Application\Entity\NmtApplicationPmtMethod $pmt_method ; */
            $pmt_method = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationPmtMethod')->find($pmt_method_id);

            if ($pmt_method == null) {
                $errors[] = $this->controllerPlugin->translate('Payment Method can\'t be empty!');
            } else {

                if (isset($data['gl_account_id'])) {

                    $gl_account_id = $data['gl_account_id'];

                    /** @var \Application\Entity\FinAccount $gl ; */
                    $gl = $this->doctrineEM->getRepository('Application\Entity\FinAccount')->find($gl_account_id);
                    if ($gl !== null) {
                        $pmt_method->setGlAccount($gl);
                    }
                }

                $entity->setPmtMethod($pmt_method);
            }
        } else {
            $errors[] = $this->controllerPlugin->translate('Payment method id is not set.');
        }

        /**
         * Check Vendor.
         */
        $ck = $this->checkVendor($entity, $data, $isPosting);
        if (count($ck) > 0) {
            $errors = array_merge($errors, $ck);
        }

        /**
         * Check Doc Amount.
         */
        if (isset($data['docAmount'])) {

            $docAmount = $data['docAmount'];

            if (! is_numeric($docAmount)) {
                $errors[] = $this->controllerPlugin->translate('Amount is not valid. It must be a number.');
            } else {
                if ($docAmount < 0) {
                    $errors[] = $this->controllerPlugin->translate('Amount must be greate than or equal 0!');
                } else {
                    $entity->setDocAmount($docAmount);
                }
            }
        }

        /**
         * Check currency and exchange rate
         */
        $ck = $this->checkCurrency($entity, $data, $isPosting);
        if (count($ck) > 0) {
            $errors = array_merge($errors, $ck);
        }

        return $errors;
    }

    /**
     *
     * @param \Application\Entity\PmtOutgoing $entity
     * @param \Application\Entity\MlaUsers $u
     * @param boolean $isNew
     */
    public function saveHeader($entity, $u, $isNew = FALSE)
    {
        if ($u == null) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        if (! $entity instanceof \Application\Entity\FinVendorInvoice) {
            throw new \Exception("Invalid Argument. Outgoing Payment Object not found!");
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
     * @param \Application\Entity\PmtOutgoing $entity
     * @param \Application\Entity\MlaUsers $u,
     * @param bool $isFlush,
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function post($entity, $u, $isFlush = false, $isPosting = True)
    {
        if (! $entity instanceof \Application\Entity\PmtOutgoing) {
            throw new \Exception("Invalid Argument! Payment Object can't not found.");
        }

        if (! $u instanceof \Application\Entity\MlaUsers) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

              // OK to post
        // +++++++++++++++++++

        $changeOn = new \DateTime();

        // Assign doc number
        if ($entity->getSysNumber() == \Application\Model\Constants::SYS_NUMBER_UNASSIGNED OR $entity->getSysNumber() ==null) {
            $entity->setSysNumber($this->controllerPlugin->getDocNumber($entity));
        }
        
        $entity->setLocalAmount($entity->getDocAmount()*$entity->getExchangeRate());

        $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_POSTED);
         $entity->setCreatedBy($u);
        $entity->setCreatedOn($changeOn);
        $this->doctrineEM->persist($entity);

        /**
         *
         * @todo: Do Accounting Posting
         */
        $this->jeService->postAPPayment($entity, $u, $this->controllerPlugin);
        $this->doctrineEM->flush();

        $m = sprintf('[OK] AP Payment %s posted', $entity->getSysNumber());
        $this->getEventManager()->trigger('finance.activity.log', __METHOD__, array(
            'priority' => \Zend\Log\Logger::INFO,
            'message' => $m,
            'createdBy' => $u,
            'createdOn' => $changeOn
        ));

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
     * @param \Application\Entity\PmtOutgoing $entity
     * @param array $data
     * @param boolean $isPosting
     */
    private function checkVendor(\Application\Entity\PmtOutgoing $entity, $data, $isPosting)
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
     * @param \Application\Entity\PmtOutgoing $entity
     * @param array $data
     * @param boolean $isPosting
     */
    private function checkCurrency(\Application\Entity\PmtOutgoing $entity, $data, $isPosting)
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

        /** @var \Application\Repository\NmtFinPostingPeriodRepository $p */
        $p = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod');

        if ($currency !== null) {
            $entity->setDocCurrency($currency);
            
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
     * @param \Application\Entity\PmtOutgoing $entity
     * @param array $data
     * @param boolean $isPosting
     */
    private function checkDocDate(\Application\Entity\PmtOutgoing $entity, $data, $isPosting)
    {
        $errors = array();

        if (! isset($data['docDate'])) {
            $errors[] = $this->controllerPlugin->translate('Doc Date input is not set!');
            return $errors;
        }

        // ==========OK=========== //
        $docDate = $data['docDate'];

        $validator = new Date();

        if (! $docDate == null) {
            if (! $validator->isValid($docDate)) {
                $errors[] = $this->controllerPlugin->translate('Doc Date is not correct or empty!');
                return $errors;
            } else {
                $entity->setDocDate(new \DateTime($docDate));
            }
        }

        // ==========OK=========== //

        // striclty check when posting.
        if ($isPosting == TRUE) {}

        return $errors;
    }

    /**
     *
     * @param \Application\Entity\PmtOutgoing $entity
     * @param array $data
     * @param boolean $isPosting
     */
    private function checkPostingDate(\Application\Entity\PmtOutgoing $entity, $data, $isPosting)
    {
        $errors = array();

        if (! isset($data['postingDate'])) {
            $errors[] = $this->controllerPlugin->translate('Posting Date input is not set!');
            return $errors;
        }

        // ==========OK=========== //
        $postingDate = $data['postingDate'];

        $validator = new Date();

        if (! $postingDate == null) {
            if (! $validator->isValid($postingDate)) {
                $errors[] = $this->controllerPlugin->translate('Posting Date is not correct or empty!');
                return $errors;
            } else {
                $entity->setPostingDate(new \DateTime($postingDate));
            }
        }

        // ==========OK=========== //

        // striclty check when posting.
        if ($isPosting == TRUE) {

            if ($entity->getPostingDate() == null) {
                $errors[] = $this->controllerPlugin->translate('Posting Date is not correct or empty!');
                return $errors;
            }

            /** @var \Application\Repository\NmtFinPostingPeriodRepository $p */
            $p = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod');

            // check if posting period is closed
            /** @var \Application\Entity\NmtFinPostingPeriod $postingPeriod */
            $postingPeriod = $p->getPostingPeriod(new \DateTime($postingDate));

            if ($postingPeriod == null) {
                $errors[] = sprintf('Posting period for [%s] not created!', $postingDate);
            } else {
                if ($postingPeriod->getPeriodStatus() == \Application\Model\Constants::PERIOD_STATUS_CLOSED) {
                    $errors[] = sprintf('Period [%s] is closed for accounting posting!', $postingPeriod->getPeriodName());
                } else {
                    $entity->setPostingDate(new \DateTime($postingDate));
                    $entity->setPostingPeriod($postingPeriod);
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
}
