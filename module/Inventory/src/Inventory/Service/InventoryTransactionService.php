<?php
namespace Inventory\Service;

use Application\Service\AbstractService;
use Zend\Math\Rand;
use Zend\Validator\Date;
use Inventory\Model\InventoryTransactionStrategyFactory;
use Inventory\Model\AbstractTransactionStrategy;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class InventoryTransactionService extends AbstractService
{

    /**
     *
     * @param \Application\Entity\NmtInventoryMv $entity
     * @param array $data
     * @param boolean $isNew
     * @param boolean $isPosting
     *
     * @return array
     */
    public function validateHeader(\Application\Entity\NmtInventoryMv $entity, $data, $u, $isNew = TRUE, $isPosting = false)
    {
        $errors = array();

        if (! $entity instanceof \Application\Entity\NmtInventoryMv) {
            $errors[] = $this->controllerPlugin->translate('Inventory Transaction object is not found!');
        }

        if ($data == null) {
            $errors[] = $this->controllerPlugin->translate('No input given');
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // ====== Validated 1 ====== //

        if (isset($data['remarks'])) {
            $remarks = $data['remarks'];
            $entity->setRemarks($remarks);
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given remarks');
        }

        // only update remark posible, when posted.
        if ($entity->getDocStatus() == \Application\Model\Constants::DOC_STATUS_POSTED or $entity->getDocStatus() == \Application\Model\Constants::DOC_STATUS_REVERSED) {
            return null;
        }

        if (isset($data['isActive'])) {
            $isActive = (int) $data['isActive'];
            if ($isActive != 1) {
                $isActive = 0;
            }

            $entity->setIsActive($isActive);
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given isActive?');
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // ====== Validated 2 ====== //

        // check movement date
        $ck = $this->checkMovementDate($entity, $data, $isPosting);
        if (count($ck) > 0) {
            $errors = array_merge($errors, $ck);
        }

        // check movement date
        $ck = $this->checkCurrency($entity, $data, $isPosting);
        if (count($ck) > 0) {
            $errors = array_merge($errors, $ck);
        }

        // only allow to change transacion type and warehouse, when no line is created.
        $criteria = array(
            'isActive' => 1,
            'movement' => $entity
        );
        $rows = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryTrx')->findBy($criteria);

        if (count($rows) == 0) {
            // check movement type
            $ck = $this->checkMovementType($entity, $data, $rows, $isPosting);
            if (count($ck) > 0) {
                $errors = array_merge($errors, $ck);
            }

            // check warehouse
            $ck = $this->checkWarehouse($entity, $data, $u, $isPosting);
            if (count($ck) > 0) {
                $errors = array_merge($errors, $ck);
            }
        }

        // do specific checking.

        // Movement Strategy.
        $check_result = null;

        $mvStrategy = InventoryTransactionStrategyFactory::getMovementStrategy($entity->getMovementType());
        if (! $mvStrategy instanceof \Inventory\Model\AbstractTransactionStrategy) {
            $errors[] = $this->controllerPlugin->translate("Invalid Argument! No strategy found.");
        } else {
            $mvStrategy->setContextService($this);
            $check_result = $mvStrategy->validateHeader($entity, $data, $u, $isNew, $isPosting);
        }

        if (count($check_result) > 0) {
            $errors = array_merge($errors, $check_result);
        }

        return $errors;
    }

    /**
     *
     * @param \Application\Entity\NmtInventoryMv $entity
     * @param array $data
     * @param \Application\Entity\MlaUsers $u,
     * @param boolean $isNew
     */
    public function saveHeader($entity, $data, $u, $isNew = FALSE, $trigger = null)
    {
        $errors = array();

        if (! $u instanceof \Application\Entity\MlaUsers) {
            $errors[] = $this->controllerPlugin->translate("Invalid Argument! User is not indentided for this transaction");
        }

        if (! $entity instanceof \Application\Entity\NmtInventoryMv) {
            $errors[] = $this->controllerPlugin->translate("Invalid Argument. Inventory Transaction object is not found!");
        } else {
            if ($entity->getLocalCurrency() == null) {
                $errors[] = $this->controllerPlugin->translate("Invalid Argument. Local currency is not defined!");
            }
        }

        if ($isNew == false) {

            // only ower or its superviour can edit this.
            $checkALC = $this->controllerPlugin->isParent($u, $entity->getCreatedBy());

            if (isset($checkALC['result']) and isset($checkALC['message'])) {
                if ($checkALC['result'] == 0) {
                    $errors[] = $this->controllerPlugin->translate("No authority to perform this operation on this object!");
                }
            } else {
                $errors[] = $this->controllerPlugin->translate("ACL checking failed");
            }
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // ====== VALIDATED 1====== //

        if ($isNew == FALSE) {
            $oldEntity = clone ($entity);
        }

        $ck = $this->validateHeader($entity, $data, $u, $isNew, FALSE);

        if (count($ck) > 0) {
            return $ck;
        }

        // ====== VALIDATED 2 ====== //
        Try {

            $changeOn = new \DateTime();
            $changeArray = array();

            if ($isNew == TRUE) {

                $entity->setSysNumber(\Application\Model\Constants::SYS_NUMBER_UNASSIGNED);
                $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_DRAFT);

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
                // $entity->setLastchangeBy($u);
                $entity->setLastchangeOn($changeOn);
            }
            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();

            // LOGGING
            if ($isNew == TRUE) {
                $m = sprintf('[OK] WH Transaction #%s created.', $entity->getId());
            } else {

                $m = sprintf('[OK] WH Transaction #%s updated.', $entity->getId());

                $this->getEventManager()->trigger('inventory.change.log', $trigger, array(
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

            $this->getEventManager()->trigger('inventory.activity.log', $trigger, array(
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
     * @param \Application\Entity\NmtInventoryMv $target
     * @param \Application\Entity\NmtInventoryTrx $entity
     * @param \Application\Entity\MlaUsers $u
     * @param array $data
     * @param boolean $isNew
     * @param $isPosting $isNew
     *
     */
    protected function validateRow($target, $entity, $u, $data, $isNew = false, $isPosting = false)
    {
        // do validating
        $errors = array();

        if (! $target instanceof \Application\Entity\NmtInventoryMv) {
            $errors[] = $this->controllerPlugin->translate('WH movement is not found!');
        } else {
            if ($target->getLocalCurrency() == null) {
                $errors[] = $this->controllerPlugin->translate('Local currency is not found!');
            }
        }

        if (! $entity instanceof \Application\Entity\NmtInventoryTrx) {
            $errors[] = $this->controllerPlugin->translate('WH movement is not found!');
        }

        if ($data == null) {
            $errors[] = $this->controllerPlugin->translate('No input given');
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // ====== VALIDATED 1 ====== //

        /*
         * $convert_factor = 1;
         * if (isset($data['convert_factor'])) {
         * $convert_factor = $data['convert_factor'];
         * }else {
         * $errors[] = $this->controllerPlugin->translate('No input given "convert_factor"');
         * }
         */

        $isActive = 0;
        if (isset($data['isActive'])) {
            $isActive = (int) $data['isActive'];
            if ($isActive != 1) {
                $isActive = 0;
            }
            $entity->setIsActive($isActive);
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "convert_factor"');
        }

        if (isset($data['remarks'])) {
            $remarks = $data['remarks'];
            $entity->setRemarks($remarks);
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // ====== VALIDATED 2 ====== //

        // check movement date
        $ck = $this->checkOnHandQuantity($entity, $data, $u, $isPosting);
        if (count($ck) > 0) {
            $errors = array_merge($errors, $ck);
        }

        /*
         * if (! is_numeric($convert_factor)) {
         * $errors[] = $this->controllerPlugin->translate('Convert factor must be a number!');
         * } else {
         * if ($quantity <= 0) {
         * $errors[] = $this->controllerPlugin->translate('Convert factor must be greater than 0!');
         * } else {
         * $entity->setConversionFactor($convert_factor);
         * }
         * }
         */

        // Movement Strategy.
        $check_result = null;

        $mvStrategy = InventoryTransactionStrategyFactory::getMovementStrategy($target->getMovementType());
        if (! $mvStrategy instanceof \Inventory\Model\AbstractTransactionStrategy) {
            $errors[] = $this->controllerPlugin->translate("Invalid Argument! No strategy found.");
        } else {
            $mvStrategy->setContextService($this);
            $check_result = $mvStrategy->validateRow($entity, $data, $u, $isNew, $isPosting);
        }

        if (count($check_result) > 0) {
            $errors = array_merge($errors, $check_result);
        }

        // check on-hand quantity.

        return $errors;
    }

    /**
     *
     * @param \Application\Entity\NmtInventoryMv $target
     * @param \Application\Entity\NmtInventoryTrx $entity
     * @param \Application\Entity\MlaUsers $u
     * @param array $data
     * @param boolean $isNew
     * @param boolean $isNew
     * @param string $trigger
     */
    public function saveRow($target, $entity, $data, $u, $isNew = false, $isPosting = false, $trigger = null)
    {
        $errors = array();

        if (! $u instanceof \Application\Entity\MlaUsers) {
            $m = $this->controllerPlugin->translate("Invalid Argument! User is not identified for this transaction.");
            $errors[] = $m;
        }

        if (! $target instanceof \Application\Entity\NmtInventoryMv) {
            $m = $this->controllerPlugin->translate("Invalid Argument. Inventory Movement Object not found!");
            $errors[] = $m;
        }

        if (! $entity instanceof \Application\Entity\NmtInventoryTrx) {
            $m = $this->controllerPlugin->translate("Invalid Argument. Inventory Movement line not found!");
            $errors[] = $m;
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // ====== VALIDATED 1 ====== //

        if ($isNew == FALSE) {
            $oldEntity = clone ($entity);
        }

        if ($isNew == TRUE) {
            $entity->setMovement($target);
            $entity->setTransactionType($target->getMovementType());
            $entity->setFlow($target->getMovementFlow());
            $entity->setLocalCurrency($target->getLocalCurrency());
            $entity->setDocCurrency($target->getDocCurrency());
            $entity->setDocType($target->getDocType());
            $entity->setDocStatus($target->getDocStatus());
            $entity->setWh($target->getWarehouse());
            $entity->setTrxDate($target->getMovementDate()); // important.
        }

        $ck = $this->validateRow($target, $entity, $u, $data, $isNew, false);

        if (count($ck) > 0) {
            return $ck;
        }

        // ====== VALIDATED 2 ====== //
        try {

            $changeOn = new \DateTime();
            $changeArray = array();

            if ($isNew == TRUE) {
                $entity->setCreatedBy($u);
                $entity->setCreatedOn($changeOn);
                $entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
            } else {

                $changeArray = $this->controllerPlugin->objectsAreIdentical($oldEntity, $entity);
                if (count($changeArray) == 0) {
                    $errors[] = sprintf('Nothing changed.');
                    return $errors;
                }

                // $entity->setRevisionNo($entity->getRevisionNo() + 1);
                $entity->setLastchangeBy($u);
                $entity->setLastchangeOn($changeOn);
            }

            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();

            // LOGGING
            if ($isNew == TRUE) {
                $m = sprintf('[OK] WH transaction line #%s created.', $entity->getId());
            } else {

                $m = sprintf('[OK] WH transaction line #%s updated.', $entity->getId());

                $this->getEventManager()->trigger('inventory.change.log', $trigger, array(
                    'priority' => 7,
                    'message' => $m,
                    'objectId' => $entity->getId(),
                    'objectToken' => $entity->getToken(),
                    'changeArray' => $changeArray,
                    'changeBy' => $u,
                    'changeOn' => $changeOn,
                    'revisionNumber' => 1,
                    'changeDate' => $changeOn,
                    'changeValidFrom' => $changeOn
                ));
            }

            $this->getEventManager()->trigger('inventory.activity.log', $trigger, array(
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
     * @param \Application\Entity\NmtInventoryMv $entity
     * @param array $data
     * @param \Application\Entity\MlaUsers $u,
     * @param bool $isFlush,
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function post($entity, $data = null, $u, $isFlush = false, $isPosting = TRUE, $trigger = null)
    {
        $errors = array();

        if (! $u instanceof \Application\Entity\MlaUsers) {
            $m = $this->controllerPlugin->translate("Invalid Argument! User is not identified for this transaction.");
            $errors[] = $m;
        }

        if (! $entity instanceof \Application\Entity\NmtInventoryMv) {
            $m = $this->controllerPlugin->translate("Invalid Argument. WH transaction not found!");
            $errors[] = $m;
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // ====== VALIDATED 1 ====== //

        $default_cur = null;
        if ($u->getCompany() instanceof \Application\Entity\NmtApplicationCompany) {
            $default_cur = $u->getCompany()->getDefaultCurrency();
            $entity->setCurrency($default_cur);
            $entity->setLocalCurrency($default_cur);
        }

        $ck = $this->validateHeader($entity, $data, $u, false, true);

        if (count($ck) > 0) {
            return $ck;
        }

        // ====== VALIDATED 2 ====== //

        try {
            $postingStrategy = InventoryTransactionStrategyFactory::getMovementStrategy($entity->getMovementType());

            if (! $postingStrategy instanceof AbstractTransactionStrategy) {
                throw new \Exception("Posting Strategy can't not be identified for this inventory movement type!");
            }

            // Do posting now
            $postingStrategy->setContextService($this);

            if ($entity->getMovementFlow() == \Inventory\Model\Constants::WH_TRANSACTION_OUT) {
                $postingStrategy->runGIPosting($entity, $u, true);
            }
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }

        return $errors;
    }

    /**
     *
     * @param \Application\Entity\NmtInventoryMv $entity
     * @param array $data
     * @param \Application\Entity\MlaUsers $u,
     * @param bool $isFlush,
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function postTransfer($entity, $data = null, $u, $isFlush = false, $isPosting = TRUE, $trigger = null)
    {
        $errors = array();

        if (! $u instanceof \Application\Entity\MlaUsers) {
            $m = $this->controllerPlugin->translate("Invalid Argument! User is not identified for this transaction.");
            $errors[] = $m;
        }

        if (! $entity instanceof \Application\Entity\NmtInventoryMv) {
            $m = $this->controllerPlugin->translate("Invalid Argument. WH transaction not found!");
            $errors[] = $m;
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // ====== VALIDATED 1 ====== //

        $default_cur = null;
        if ($u->getCompany() instanceof \Application\Entity\NmtApplicationCompany) {
            $default_cur = $u->getCompany()->getDefaultCurrency();
            $entity->setCurrency($default_cur);
            $entity->setLocalCurrency($default_cur);
        }

        $ck = $this->validateHeader($entity, $data, $u, false, true);

        if (count($ck) > 0) {
            return $ck;
        }

        // ====== VALIDATED 2 ====== //

        try {
            $postingStrategy = InventoryTransactionStrategyFactory::getMovementStrategy($entity->getMovementType());

            if (! $postingStrategy instanceof AbstractTransactionStrategy) {
                throw new \Exception("Posting Strategy can't not be identified for this inventory movement type!");
            }

            // Do posting now
            $postingStrategy->setContextService($this);
            $postingStrategy->runTransferPosting($entity, $u, true);
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }

        return $errors;
    }

    /**
     *
     * @param \Application\Entity\NmtInventoryMv $entity
     * @param array $data
     * @param \Application\Entity\MlaUsers $u,
     * @param bool $isFlush,
     *
     */
    public function reverse($entity, $u, $reversalDate, $reversalReason, $trigger = null)
    {
        $errors = array();

        if (! $entity instanceof \Application\Entity\NmtInventoryMv) {
            $errors[] = $this->controllerPlugin->translate('Invalid Argument! WH transaction not found)');
        } else {

            // only when posted.
            if ($entity->getDocStatus() !== \Application\Model\Constants::DOC_STATUS_POSTED) {
                $errors[] = $this->controllerPlugin->translate('WH transaction not posted yet. Reserval imposible.');
            }

            // only when not reversed..
            if ($entity->getIsReversed() == 1) {
                $errors[] = $this->controllerPlugin->translate('WH transaction reversed already.');
            }

            // check if subsequce document.
            // only when not reversed..
            // 1 mean not.
            if ($entity->getIsReversable() == 1) {
                $errors[] = $this->controllerPlugin->translate('WH transaction is not reservable, becasue sequence document is created.');
            }
        }

        if (! $u instanceof \Application\Entity\MlaUsers) {
            $errors[] = $this->controllerPlugin->translate('Invalid Argument! User not found)');
        }

        $criteria = array(
            'isActive' => 1,
            'movement' => $entity
        );
        $ap_rows = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryTrx')->findBy($criteria);

        if (count($ap_rows) == 0) {
            $errors[] = $this->controllerPlugin->translate('WH transaction is empty. Reserval imposible.');
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // ====== VALIDATED 1 ====== //

        /** @var \Application\Entity\NmtInventoryMv $newEntity ; */
        $newEntity = clone ($entity);
        $newEntity->setMovementDate(null);

        $data = array();
        $data['movementDate'] = $reversalDate;

        /**
         * Check Reversal Date.
         * in open period.
         * Date must greater than document to be reversed.
         */
        $ck = $this->checkReversalDate($newEntity, $entity, $data, TRUE);

        if (count($ck) > 0) {
            $errors = array_merge($errors, $ck);
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // ====== VALIDATED 2 ====== //

        try {
            $postingStrategy = InventoryTransactionStrategyFactory::getMovementStrategy($entity->getMovementType());

            if (! $postingStrategy instanceof AbstractTransactionStrategy) {
                throw new \Exception("Posting Strategy can't not be identified for this inventory movement type!");
            }

            // Do posting now
            $postingStrategy->setContextService($this);

            if ($entity->getMovementFlow() == \Inventory\Model\Constants::WH_TRANSACTION_OUT) {
                $postingStrategy->runGIReversal($entity, $u, $reversalDate, $reversalReason, TRUE);
            }
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }

        return $errors;
    }

    /**
     *
     * @param \Application\Entity\FinVendorInvoice $entity
     * @param array $data
     * @param boolean $isPosting
     */
    private function checkCurrency(\Application\Entity\NmtInventoryMv $entity, $data, $isPosting)
    {
        $errors = array();

        if (! isset($data['currency_id']) and ! isset($data['exchangeRate'])) {
            $entity->setExchangeRate(1);
            $entity->setDocCurrency($entity->getLocalCurrency());
            return null;
        }

        // ==========OK=========== //

        $currency_id = (int) $data['currency_id'];
        $exchangeRate = (double) $data['exchangeRate'];

        // ** @var \Application\Entity\NmtApplicationCurrency $currency ; */
        $currency = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->find($currency_id);

        if ($currency == null) {
            $errors[] = $this->controllerPlugin->translate('Currency not defined!');
            return $errors;
        }

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
        }
    }

    /**
     *
     * @param \Application\Entity\NmtInventoryMv $newEntity
     * @param \Application\Entity\NmtInventoryMv $entity
     * @param string $data
     * @param boolean $isPosting
     */
    private function checkReversalDate(\Application\Entity\NmtInventoryMv $newEntity, \Application\Entity\NmtInventoryMv $entity, $data, $isPosting)
    {
        $errors = array();

        if (isset($data['movementDate'])) {
            $movementDate = $data['movementDate'];
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "movementDate"');
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // ==========Validated=========== //

        $validator = new Date();

        if (! $movementDate == null) {
            if (! $validator->isValid($movementDate)) {
                $errors[] = $this->controllerPlugin->translate('WH transaction date is not correct or empty!');
                return $errors;
            } else {
                $newEntity->setMovementDate(new \DateTime($movementDate));
            }
        }

        // ==========OK=========== //

        // Striclty check when posting.
        if ($isPosting == TRUE) {

            if ($newEntity->getMovementDate() == null) {
                $errors[] = $this->controllerPlugin->translate('WH transaction date  is not correct or empty!');
                return $errors;
            }

            /** @var \Application\Repository\NmtFinPostingPeriodRepository $p */
            $p = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod');

            // check if posting period is closed
            /** @var \Application\Entity\NmtFinPostingPeriod $postingPeriod */
            $postingPeriod = $p->getPostingPeriod(new \DateTime($movementDate));

            if ($postingPeriod == null) {
                $errors[] = sprintf('Posting period for [%s] not created!', $movementDate);
                return $errors;
            } else {

                if ($postingPeriod->getPeriodStatus() == \Application\Model\Constants::PERIOD_STATUS_CLOSED) {
                    $errors[] = sprintf('Period [%s] is closed for inventory transaction!', $postingPeriod->getPeriodName());
                    return $errors;
                }
            }

            $resveralDate = new \DateTime($movementDate);

            // not allow to reverse on the date < date of document.
            if ($resveralDate < $entity->getMovementDate()) {
                $errors[] = sprintf('It is posible to reverse on this date %s', date_format($resveralDate, "Y-m-d"));
                return $errors;
            }

            $newEntity->setMovementDate($resveralDate);
        }
    }

    /**
     *
     * @param \Application\Entity\NmtInventoryMv $entity
     * @param string $data
     * @param boolean $isPosting
     */
    private function checkMovementDate(\Application\Entity\NmtInventoryMv $entity, $data, $isPosting)
    {
        $errors = array();

        if (isset($data['movementDate'])) {
            $movementDate = $data['movementDate'];
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "movementDate"');
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // ==========Validated=========== //

        $validator = new Date();

        if (! $movementDate == null) {
            if (! $validator->isValid($movementDate)) {
                $errors[] = $this->controllerPlugin->translate('WH transaction date is not correct or empty!');
                return $errors;
            } else {
                $entity->setMovementDate(new \DateTime($movementDate));
            }
        }

        // ==========OK=========== //

        // striclty check when posting.
        if ($isPosting == TRUE) {

            if ($entity->getMovementDate() == null) {
                $errors[] = $this->controllerPlugin->translate('WH transaction date  is not correct or empty!');
                return $errors;
            }

            /** @var \Application\Repository\NmtFinPostingPeriodRepository $p */
            $p = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod');

            // check if posting period is closed
            /** @var \Application\Entity\NmtFinPostingPeriod $postingPeriod */
            $postingPeriod = $p->getPostingPeriod(new \DateTime($movementDate));

            if ($postingPeriod == null) {
                $errors[] = sprintf('Posting period for [%s] not created!', $movementDate);
            } else {
                if ($postingPeriod->getPeriodStatus() == \Application\Model\Constants::PERIOD_STATUS_CLOSED) {
                    $errors[] = sprintf('Period [%s] is closed for Good receipt!', $postingPeriod->getPeriodName());
                } else {
                    $entity->setMovementDate(new \DateTime($movementDate));
                }
            }
        }

        return $errors;
    }

    /**
     *
     * @param \Application\Entity\NmtInventoryMv $entity
     * @param string $data
     * @param boolean $isPosting
     */
    private function checkMovementType(\Application\Entity\NmtInventoryMv $entity, $data, $isPosting)
    {
        $errors = array();

        // ==========Validated 1=========== //

        if (isset($data['movementType'])) {
            $movementType = $data['movementType'];
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given movementType');
            return $errors;
        }

        // ==========Validated 2=========== //

        if ($movementType == null) {
            $errors[] = $this->controllerPlugin->translate('Inventory movement type is not selected!');
        } else {

            // validate movement Type.
            $movementStrategy = \Inventory\Model\InventoryTransactionStrategyFactory::getMovementStrategy($movementType);

            if (! $movementStrategy instanceof \Inventory\Model\AbstractTransactionStrategy) {
                $errors[] = $this->controllerPlugin->translate('Inventory movement strategy is not implemented yet!');
            } else {

                if ($movementStrategy->getFlow() == null) {
                    $errors[] = $this->controllerPlugin->translate('Inventory movement strategy is not implemented correctly!');
                } else {
                    $entity->setMovementType($movementType);
                    $entity->setMovementFlow($movementStrategy->getFlow());
                }
            }
        }

        return $errors;
    }

    /**
     *
     * @param \Application\Entity\NmtInventoryTrx $entity
     * @param string $data
     * @param \Application\Entity\MlaUsers $u
     * @param boolean $isPosting
     */
    private function checkOnHandQuantity(\Application\Entity\NmtInventoryTrx $entity, $data, $u, $isPosting)
    {
        $errors = array();

        // basic information.
        if (isset($data['item_id'])) {
            $item_id = $data['item_id'];
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "item_id"');
        }

        if (isset($data['quantity'])) {
            $quantity = $data['quantity'];
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "quantity"');
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // ====== VALIDATED 1 ====== //

        /**@var \Application\Entity\NmtInventoryItem $item ;*/
        $item = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->find($item_id);

        if ($item == null) {
            $errors[] = $this->controllerPlugin->translate('No Item is selected');
        } else {
            if ($item->getIsStocked() != 1) {
                $errors[] = $this->controllerPlugin->translate('Item is not kept in stock');
            } else {
                $entity->setItem($item);
            }
        }

        if ($quantity == null) {
            $errors[] = $this->controllerPlugin->translate('Please enter quantity!');
        } else {

            if (! is_numeric($quantity)) {
                $errors[] = $this->controllerPlugin->translate('Quantity must be a number!');
            } else {
                if ($quantity <= 0) {
                    $errors[] = $this->controllerPlugin->translate('Quantity must be greater than 0!');
                } else {
                    $entity->setQuantity($quantity);
                }
            }
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // ====== VALIDATED 2 ====== //

        // now checking onhand-quantity

        try {

            $itemReportService = new \Inventory\Service\Report\ItemReportService();

            $itemReportService->setDoctrineEM($this->getDoctrineEM());
            $itemReportService->setControllerPlugin($this->getControllerPlugin());

            $onhand = $itemReportService->getOnhandInWahrehouse($entity, $item, $u);

            if ($onhand < $quantity) {
                $m = $this->controllerPlugin->translate('Goods Issue imposible. Issue Quantity > On-hand Quantity');
                $m = sprintf($m . ' (%s>%s)', $quantity, $onhand);
                $m = $m . $this->controllerPlugin->translate('. Please check stock or change issue date.');

                $errors[] = $m;
            }
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }

        return $errors;
    }

    /**
     *
     * @param \Application\Entity\NmtInventoryMv $entity
     * @param string $data
     * @param boolean $isPosting
     */
    private function checkWarehouse(\Application\Entity\NmtInventoryMv $entity, $data, $u, $isPosting)
    {
        $errors = array();

        if (isset($data['source_wh_id'])) {
            $source_wh_id = $data['source_wh_id'];
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given source_wh_id');
            return $errors;
        }

        // ==========OK=========== //

        $warehouse = null;
        if ($source_wh_id > 0) {
            $warehouse = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->find($source_wh_id);
        }

        if (! $warehouse instanceof \Application\Entity\NmtInventoryWarehouse) {
            $errors[] = $this->controllerPlugin->translate('Warehouse is not selected');
            return $errors;
        }

        if ($warehouse->getWhController() == null) {
            $entity->setWarehouse($warehouse);
            return null;
        }

        // if WH has controller.
        $checkALC = $this->controllerPlugin->isParent($u, $warehouse->getWhController());

        if (isset($checkALC['result']) and isset($checkALC['message'])) {
            if ($checkALC['result'] == 0) {
                $errors[] = $this->controllerPlugin->translate("No authority to perform this operation on this Warehouse: " . $warehouse->getWhName());
            } else {
                $entity->setWarehouse($warehouse);
            }
        } else {
            $errors[] = $this->controllerPlugin->translate("ACL checking failed");
        }

        return $errors;
    }
}
