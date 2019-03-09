<?php
namespace Payment\Service;

use Application\Service\AbstractService;
use Zend\Math\Rand;
use Zend\Validator\Date;

/**
 * PO Payment Service.
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
    public function validateHeader(\Application\Entity\PmtOutgoing $entity, $data, $isPosting = TRUE)
    {
        $errors = array();

        if (! $entity instanceof \Application\Entity\PmtOutgoing) {
            $errors[] = $this->controllerPlugin->translate('Payment  is not found!');
        } else {

            if ($entity->getLocalCurrency() == null) {
                $errors[] = $this->controllerPlugin->translate('Local currency is not found!');
            }

            $entity->setDocType(\Payment\Model\Constants::OUTGOING_PO);
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
     * @param array $data
     * @param \Application\Entity\MlaUsers $u
     * @param boolean $isNew
     * @throws \Exception
     */
    public function saveHeader($entity, array $data, $u, $isNew = FALSE)
    {
        $errors = array();

        if ($u == null) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        if (! $entity instanceof \Application\Entity\PmtOutgoing) {
            throw new \Exception("Invalid Argument. Outgoing Payment Object not found!");
        }

        $oldEntity = clone ($entity);

        $ck = $this->validateHeader($entity, $data, FALSE);

        if (count($ck) > 0) {
            return $ck;
        }

        // OK to save draft

        try {

            $entity->setPostingKey(\Finance\Model\Constants::POSTING_KEY_DEBIT);

            $changeOn = new \DateTime();

            if ($isNew == TRUE) {

                $entity->setSysNumber(\Application\Model\Constants::SYS_NUMBER_UNASSIGNED);
                $entity->setCreatedBy($u);
                $entity->setCreatedOn($changeOn);
                $entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
      
            } else {

                $changeArray = $this->controllerPlugin->objectsAreIdentical($oldEntity, $entity);
                if (count($changeArray) == 0) {
                    $errors[] = sprintf('Nothing changed!');
                    return $errors;
                }

                $entity->setRevisionNo($entity->getRevisionNo() + 1);
                $entity->setLastchangeBy($u);
                $entity->setLastchangeOn($changeOn);

                $m = sprintf('[OK] Payment #%s for PO %s updated.', $entity->getId(), $entity->getPo()->getId());
                $this->getEventManager()->trigger('payment.change.log', __METHOD__, array(
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
            
            if ($isNew == TRUE) {
                $m = sprintf('[OK] Payment #%s for PO %s  created.', $entity->getId(),$entity->getPo()->getSysNumber());
            }
            
            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();
            
            $this->getEventManager()->trigger('payment.activity.log', __METHOD__, array(
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
     * @param \Application\Entity\PmtOutgoing $entity
     * @param array $data
     * @param \Application\Entity\MlaUsers $u,
     * @param bool $isFlush,
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function post($entity, array $data, $u, $isNew = TRUE, $isFlush = false)
    {
        $errors = array();

        if ($u == null) {
            $errors[] = $this->controllerPlugin->translate("Invalid Argument! User can't be indentided for this transaction.");
        }

        if (! $entity instanceof \Application\Entity\PmtOutgoing) {
            $errors[] = $this->controllerPlugin->translate("Invalid Argument. Outgoing Payment Object not found!");
        } else {
            if ($entity->getDocStatus() == \Application\Model\Constants::DOC_STATUS_POSTED) {
                $errors[] = $this->controllerPlugin->translate("Outgoing Payment already posted");
            }
        }

        if (count($errors) > 0) {
            return $errors;
        }

        $oldEntity = clone ($entity);
        $errors = $this->validateHeader($entity, $data, TRUE);

        if (count($errors) > 0) {
            return $errors;
        }

        // OK to post
        // +++++++++++++++++++

        // Assign doc number
        if ($entity->getSysNumber() == \Application\Model\Constants::SYS_NUMBER_UNASSIGNED or $entity->getSysNumber() == null) {
            $entity->setSysNumber($this->controllerPlugin->getDocNumber($entity));
        }

        $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_POSTED);
        $entity->setIsActive(1);

        $entity->setLocalAmount($entity->getDocAmount() * $entity->getExchangeRate());
        $entity->setPostingKey(\Finance\Model\Constants::POSTING_KEY_DEBIT);

        $changeOn = new \DateTime();

        if ($isNew == TRUE) {

            $entity->setCreatedBy($u);
            $entity->setCreatedOn($changeOn);
            $entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
             
        } else {

            $changeArray = $this->controllerPlugin->objectsAreIdentical($oldEntity, $entity);
            if (count($changeArray) == 0) {
                $errors[] = sprintf('Nothing changed!');
                return $errors;
            }

            $entity->setRevisionNo($entity->getRevisionNo() + 1);
            $entity->setLastchangeBy($u);
            $entity->setLastchangeOn($changeOn);
            
            

            $m = sprintf('[OK] Payment #%s for PO %s updated and posted.', $entity->getId(), $entity->getPo()->getSysNumber());
            // Trigger Change Log. AbtractController is EventManagerAware.

            $this->getEventManager()->trigger('payment.change.log', __METHOD__, array(
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

        $this->doctrineEM->persist($entity);
        $this->doctrineEM->flush();

        if ($isNew == TRUE) {
            $m = sprintf('[OK] Payment #%s for AP %s  posted.', $entity->getSysNumber(),$entity->getPo()->getSysNumber());
        }
        
         $this->getEventManager()->trigger('payment.activity.log', __METHOD__, array(
            'priority' => \Zend\Log\Logger::INFO,
            'message' => $m,
            'createdBy' => $u,
            'createdOn' => $changeOn
        ));

        /**
         *
         * @todo: Do Accounting Posting
         */
        $this->jeService->postPOPayment($entity, $u, $this->controllerPlugin);
        $this->doctrineEM->flush();

        return null;
    }

    /**
     *
     * @param \Application\Entity\PmtOutgoing $entity
     * @param array $data
     * @param \Application\Entity\MlaUsers $u,
     * @param bool $isFlush,
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function reverse($entity, $u, $reversalDate, $reversalReason)
    {
        $errors = array();

        if ($u == null) {
            $errors[] = $this->controllerPlugin->translate("Invalid Argument! User can't be indentided for this transaction.");
        }

        if (! $entity instanceof \Application\Entity\PmtOutgoing) {
            $errors[] = $this->controllerPlugin->translate("Invalid Argument. Outgoing Payment Object not found!");
        } else {
            if ($entity->getIsReversed() == 1) {
                $errors[] = $this->controllerPlugin->translate("Outgoing Payment already reversed.");
            }
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // OK to post
        // +++++++++++++++++++

        /** @var \Application\Entity\PmtOutgoing $newEntity */
        $newEntity = clone ($entity);

        $newEntity->setPostingDate(null);
        $newEntity->setRemarks($reversalReason);

        $data = array();
        $data['postingDate'] = $reversalDate;

        /**
         * Check Posting Date.
         */
        $ck = $this->checkPostingDate($newEntity, $data, TRUE);
        if (count($ck) > 0) {
            $errors = array_merge($errors, $ck);
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // OK to reverse
        // +++++++++++++++++++
        
        

        $newEntity->setPostingKey(\Finance\Model\Constants::POSTING_KEY_CREDIT);
        
        // pay-po-1 or pay-ap-1
        $newEntity->setDocType($newEntity->getDocType().'-1');
        $newEntity->setIsReversed(1);
        $newEntity->setDocStatus(\Application\Model\Constants::DOC_STATUS_POSTED);

        // Assign doc number
        $newEntity->setSysNumber($this->controllerPlugin->getDocNumber($newEntity));
        $newEntity->setLocalAmount($newEntity->getDocAmount() * $newEntity->getExchangeRate());

        $changeOn = new \DateTime();

        $newEntity->setCreatedBy($u);
        $newEntity->setCreatedOn($changeOn);
        $newEntity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));

        $this->doctrineEM->persist($newEntity);

        $entity->setIsReversed(1);
        $entity->setReversalDate($newEntity->getPostingDate());

        $this->doctrineEM->persist($entity);

        $this->doctrineEM->flush();

        $entity->setReversalDoc($newEntity->getId());

        /**
         *
         * @todo: Do Accounting Posting
         */
        $this->jeService->reversePOPayment($entity, $u, $this->controllerPlugin);
        $this->doctrineEM->flush();

        $m = sprintf('[OK] Payment %s reversed.', $entity->getSysNumber());
        $this->getEventManager()->trigger('payment.activity.log', __METHOD__, array(
            'priority' => \Zend\Log\Logger::INFO,
            'message' => $m,
            'createdBy' => $u,
            'createdOn' => $changeOn
        ));

        return $errors;
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
            $errors[] = $this->controllerPlugin->translate('Posting date input is not set!');
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
            $postingPeriod = $p->getPostingPeriod($entity->getPostingDate());

            if ($postingPeriod == null) {
                $errors[] = sprintf('Posting period for [%s] not created!', $postingDate);
            } else {
                if ($postingPeriod->getPeriodStatus() == \Application\Model\Constants::PERIOD_STATUS_CLOSED) {
                    $errors[] = sprintf('Period [%s] is closed for accounting posting!', $postingPeriod->getPeriodName());
                } else {
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
