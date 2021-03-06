<?php
namespace Inventory\Model;

use Inventory\Service\FIFOLayerService;
use Zend\Math\Rand;
use Zend\Validator\Date;

/**
 * Abstract inventory transaction.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractTransactionStrategy implements InventoryTransactionInterface
{

    protected $contextService;

    /**
     *
     * @param \Application\Entity\NmtInventoryMv $entity
     * @param array $data
     * @param \Application\Entity\MlaUsers $u
     * @param boolean $isNew
     * @param boolean $isPosting
     */
    public function validateHeader($entity, $data, $u, $isNew, $isPosting)
    {
        // blank left
    }

    /**
     * Templete Method
     *
     * @param \Application\Entity\NmtInventoryMv $entity
     * @param \Application\Entity\MlaUsers $u
     * @param bool $isFlush
     */
    public function runTransferPosting($entity, $u, $isFlush = TRUE)
    {
        if (! $entity instanceof \Application\Entity\NmtInventoryMv) {
            throw new \Exception("Movement is found");
        }
        $criteria = array(
            'movement' => $entity
        );

        $sort = array();

        $rows = $this->contextService->getDoctrineEM()
            ->getRepository('Application\Entity\NmtInventoryTrx')
            ->findBy($criteria, $sort);

        if (count($rows) == 0) {
            throw new \Exception("Movement is empty");
        }

        $this->contextService->getDoctrineEM()
            ->getConnection()
            ->beginTransaction(); // suspend auto-commit

        try {

            if ($entity->getSysNumber() == \Application\Model\Constants::SYS_NUMBER_UNASSIGNED) {
                $entity->setSysNumber($this->contextService->getControllerPlugin()
                    ->getDocNumber($entity));
            }
            $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_POSTED);
            $entity->setIsDraft(0);
            $entity->setIsPosted(1);
            $this->contextService->getDoctrineEM()->persist($entity);

            /**
             * STEP 1: Calculate COGS and create consumption of FIFO Layer.
             * =====================
             */
            $fifoLayerService = new FIFOLayerService();
            $fifoLayerService->setDoctrineEM($this->contextService->getDoctrineEM());
            $fifoLayerService->setControllerPlugin($this->contextService->getControllerPlugin());

            foreach ($rows as $r) {

                /** @var \Application\Entity\NmtInventoryTrx $r */
                if ($r->getQuantity() == 0) {
                    continue;
                }

                // update transaction row
                $r->setTrxDate($entity->getMovementDate());
                $r->setDocStatus(\Application\Model\Constants::DOC_STATUS_POSTED);
                $r->setDocType($entity->getMovementType());
                $r->setTransactionType($entity->getMovementType());

                // calculate COGS. it will generate consumption of FIFO layer.
                $cogs = $fifoLayerService->calculateCOGS($r, $r->getItem(), $entity->getWarehouse(), $r->getQuantity(), $u);
                $r->setCogsLocal($cogs);
                $this->contextService->getDoctrineEM()->persist($r);
            }

            /**
             * STEP 2: UPDATE HEADER
             * =====================
             */

            // flush
            $this->contextService->getDoctrineEM()->flush();

            /**
             * STEP 2: DO SPECIFIC TASK FOR EACH TYPE OF TRANSACTION, if need
             * =====================
             */
            $this->doPosting($entity, $u, false);

            /**
             *
             * @todo STEP 3: DO JOURNAL ENTRY POSTING
             *       =====================
             */

            // need flush now.
            if ($isFlush == TRUE) {
                $this->contextService->getDoctrineEM()->flush();
                $this->contextService->getDoctrineEM()->commit();
            }
        } catch (\Exception $e) {
            $this->contextService->getDoctrineEM()
                ->getConnection()
                ->rollBack();

            $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_DRAFT);
            $entity->setIsDraft(1);
            $entity->setIsPosted(0);

            throw $e;
        }
    }

    /**
     * Templete Method
     *
     * @param \Application\Entity\NmtInventoryMv $entity
     * @param \Application\Entity\MlaUsers $u
     * @param bool $isFlush
     */
    public function runTransferReserval($entity, $u, $reversalDate, $reversalReason, $isFlush = TRUE)
    {}

    /**
     * Templete Method
     *
     * @param \Application\Entity\NmtInventoryMv $entity
     * @param \Application\Entity\MlaUsers $u
     * @param bool $isFlush
     */
    public function runGIReversal($entity, $u, $reversalDate, $reversalReason, $isFlush = TRUE)
    {
        if (! $entity instanceof \Application\Entity\NmtInventoryMv) {
            throw new \Exception("WH Transaction is not found");
        }
        $criteria = array(
            'movement' => $entity
        );

        $sort = array();

        $rows = $this->contextService->getDoctrineEM()
            ->getRepository('Application\Entity\NmtInventoryTrx')
            ->findBy($criteria, $sort);

        if (count($rows) == 0) {
            throw new \Exception("WH Transaction is empty");
        }

        /**
         * STEP 1: Duplicate WH transaction, and change to opposite flow.
         * =====================
         */

        $changeOn = new \DateTime();

        /** @var \Application\Entity\NmtInventoryMv $newEntity ;*/
        $newEntity = clone ($entity);

        // updte new header to opposite flow.
        $newEntity->setMovementFlow(\Inventory\Model\Constants::WH_TRANSACTION_IN);

        $newEntity->setDocStatus(\Application\Model\Constants::DOC_STATUS_REVERSED);
        $newEntity->setDocType($entity->getMovementType() . "-1");
        $newEntity->setMovementType($entity->getMovementType() . "-1");

        $newEntity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
        $newEntity->setSysNumber($this->contextService->getControllerPlugin()
            ->getDocNumber($newEntity));
        $newEntity->setRemarks($reversalReason);

        $this->contextService->getDoctrineEM()->persist($newEntity);

        foreach ($rows as $r) {

            /** @var \Application\Entity\NmtInventoryTrx $r */
            if ($r->getQuantity() == 0) {
                continue;
            }

            /**
             *
             * @todo: get fifo layer consumption, and create new line
             */
            $criteria = array(
                'inventoryTrx' => $r
            );

            $consums = $this->contextService->getDoctrineEM()
                ->getRepository('Application\Entity\NmtInventoryFIFOLayerConsume')
                ->findBy($criteria);
            if (count($consums) > 0) {
                foreach ($consums as $c) {

                    // duplicate row
                    /** @var \Application\Entity\NmtInventoryFifoLayerConsume $c */

                    /** @var \Application\Entity\NmtInventoryTrx $new_row */
                    $new_row = new \Application\Entity\NmtInventoryTrx();

                    // important!
                    $new_row->setMovement($newEntity);
                    $new_row->setWh($r->getWh());
                    $new_row->setTrxDate(new \DateTime($reversalDate));
                    $new_row->setItem($r->getItem());

                    $new_row->setRemarks($reversalReason);
                    $new_row->setDocType($r->getDocType() . "-1");
                    $new_row->setReversalDoc($r->getId());

                    // set opposite flow.
                    $new_row->setFlow(\Inventory\Model\Constants::WH_TRANSACTION_IN);
                    $new_row->setQuantity($c->getQuantity());
                    $new_row->setDocQuantity($c->getQuantity());
                    $new_row->setLocalCurrency($c->getLocalCurrency());
                    $new_row->setExchangeRate($c->getExchangeRate());

                    $new_row->setVendorUnitPrice($c->getDocUnitPrice());
                    $new_row->setDocUnitPrice($c->getDocUnitPrice());

                    $new_row->setToken(Rand::getString(22, \Application\Model\Constants::CHAR_LIST, true));
                    $new_row->setCreatedBy($u);
                    $new_row->setCreatedOn($r->getCreatedOn());

                    $this->contextService->getDoctrineEM()->persist($new_row);
                }
            }

            $r->setReversalReason($reversalReason);
            $r->setReversalDate(new \DateTime($reversalDate));

            // marked old row as reversed.
            $r->setTrxDate(new \DateTime($reversalDate));
            $r->setRemarks($reversalReason);
            $r->setIsReversed(1);
            $r->setReversalReason($reversalReason);
            $r->setReversalDate(new \DateTime($reversalDate));

            $this->contextService->getDoctrineEM()->persist($r);
        }
        ;

        // Need to flush the the transaction.
        $this->contextService->getDoctrineEM()->flush();

        /**
         * STEP 2: Create FIFO Layer for duplicated entity.
         * =====================
         */
        $this->createFIFOLayer($newEntity, null, $u, False);

        /**
         * STEP 2-5: DO SPECIFIC TASK FOR EACH TYPE OF TRANSACTION, if need
         * =====================
         */
        $this->reverse($entity, $u, $reversalDate, $reversalReason, $isFlush = TRUE);

        /**
         * STEP 3: Create Journal Entry for duplicate entity
         * =====================
         */

        $this->createJournalEntryForGR($newEntity, null, "reversal", $u, $isFlush);

        // change old header
        $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_REVERSED);
        $entity->setIsReversed(1);
        $entity->setReversalReason($reversalReason);
        $entity->setReversalDate(new \DateTime($reversalDate));
        $entity->setRevisionNo($entity->getRevisionNo() + 1);
        // $entity->setLastchangeBy($u);
        $entity->setLastchangeOn($changeOn);
        $this->contextService->getDoctrineEM()->persist($entity);

        if ($isFlush == true) {
            $this->contextService->getDoctrineEM()->flush();
        }
    }

    /**
     *
     * @param \Application\Entity\NmtInventoryMv $entity
     * @param \Application\Entity\MlaUsers $u
     * @param array $row
     * @param bool $isFlush
     */
    protected function createFIFOLayer($entity, $rows = null, $u, $isFlush = FALSE)
    {
        if ($rows == null) {

            $criteria = array(
                'movement' => $entity
            );

            $sort = array();

            $rows = $this->contextService->getDoctrineEM()
                ->getRepository('Application\Entity\NmtInventoryTrx')
                ->findBy($criteria, $sort);

            if (count($rows) == 0) {
                throw new \Exception("WH Transaction is empty");
            }
        }

        foreach ($rows as $r) {

            /** @var \Application\Entity\NmtInventoryTrx $r */
            if ($r->getQuantity() == 0) {
                continue;
            }

            // created FIFO if needed
            if ($r->getItem() != null) {

                if ($r->getItem()->getIsStocked() == 1) {

                    /**
                     *
                     * @todo: Create FIFO Layer
                     * @todo: recalculate price for inventory unit.
                     */
                    $fifoLayer = new \Application\Entity\NmtInventoryFifoLayer();

                    $fifoLayer->setInventoryTrx($r); // New
                    $fifoLayer->setIsClosed(0);
                    $fifoLayer->setItem($r->getItem());
                    $fifoLayer->setQuantity($r->getQuantity());

                    // set WH
                    $fifoLayer->setWarehouse($r->getWh());

                    // will be changed uppon inventory transaction.
                    $fifoLayer->setOnhandQuantity($r->getQuantity());
                    $fifoLayer->setDocUnitPrice($r->getVendorUnitPrice());
                    $fifoLayer->setLocalCurrency($entity->getLocalCurrency());
                    $fifoLayer->setDocCurrency($entity->getDocCurrency());

                    $fifoLayer->setExchangeRate($entity->getExchangeRate());
                    $fifoLayer->setPostingDate($r->getTrxDate());

                    $fifoLayer->setSourceClass(get_class($r));
                    $fifoLayer->setSourceId($r->getID());
                    $fifoLayer->setSourceToken($r->getToken());

                    $fifoLayer->setToken(Rand::getString(22, \Application\Model\Constants::CHAR_LIST, true));
                    $fifoLayer->setCreatedBy($u);
                    $fifoLayer->setCreatedOn($r->getCreatedOn());
                    $this->contextService->getDoctrineEM()->persist($fifoLayer);
                }
            }
        }

        if ($isFlush == true) {
            $this->contextService->getDoctrineEM()->flush();
        }
    }

    /**
     *
     * @param \Application\Entity\NmtInventoryTrx $trx
     * @param \Application\Entity\MlaUsers $u
     * @param array $row
     * @param bool $isFlush
     */
    protected function createFIFOLayerByLine($trx, $u, $isFlush = FALSE)
    {
        if (! $trx instanceof \Application\Entity\NmtInventoryTrx) {
            throw new \Exception("Invalid Agrument. Inventory transaction not given");
        }

        /** @var \Application\Entity\NmtInventoryTrx $trx ; */
        if ($trx->getQuantity() == 0) {
            continue;
        }

        // created FIFO if needed
        if ($trx->getItem() != null) {

            if ($trx->getItem()->getIsStocked() == 1) {

                /**
                 *
                 * @todo: Create FIFO Layer
                 * @todo: recalculate price for inventory unit.
                 */
                $fifoLayer = new \Application\Entity\NmtInventoryFifoLayer();

                $fifoLayer->setInventoryTrx($trx); // New
                $fifoLayer->setIsClosed(0);
                $fifoLayer->setItem($trx->getItem());
                $fifoLayer->setQuantity($trx->getQuantity());

                // set WH
                $fifoLayer->setWarehouse($trx->getWh());

                // will be changed uppon inventory transaction.
                $fifoLayer->setOnhandQuantity($trx->getQuantity());
                $fifoLayer->setDocUnitPrice($trx->getVendorUnitPrice());
                $fifoLayer->setLocalCurrency($trx->getLocalCurrency());
                $fifoLayer->setDocCurrency($trx->getDocCurrency());

                $fifoLayer->setExchangeRate($trx->getExchangeRate());
                $fifoLayer->setPostingDate($trx->getTrxDate());

                $fifoLayer->setSourceClass(get_class($trx));
                $fifoLayer->setSourceId($trx->getID());
                $fifoLayer->setSourceToken($trx->getToken());

                $fifoLayer->setToken(Rand::getString(15, \Application\Model\Constants::CHAR_LIST, true));
                $fifoLayer->setCreatedBy($u);
                $fifoLayer->setCreatedOn($trx->getCreatedOn());
                $this->contextService->getDoctrineEM()->persist($fifoLayer);
            }
        }

        if ($isFlush == true) {
            $this->contextService->getDoctrineEM()->flush();
        }
    }

    /**
     *
     * @param \Application\Entity\NmtInventoryMv $entity
     * @param array $rows
     * @param string $docType
     * @param \Application\Entity\MlaUsers $u
     * @param bool $isFlush
     */
    protected function createJournalEntryForGR($entity, $rows = null, $docType, $u, $isFlush)
    {
        if ($rows == null) {

            $criteria = array(
                'movement' => $entity
            );

            $sort = array();

            $rows = $this->contextService->getDoctrineEM()
                ->getRepository('Application\Entity\NmtInventoryTrx')
                ->findBy($criteria, $sort);

            if (count($rows) == 0) {
                throw new \Exception("WH Transaction is empty");
            }
        }

        // Create JE
        $je = new \Application\Entity\FinJe();
        $je->setCurrency($entity->getCurrency());
        $je->setLocalCurrency($entity->getCurrency());
        $je->setExchangeRate($entity->getExchangeRate());

        $je->setPostingDate($entity->getMovementDate());
        $je->setDocumentDate($entity->getMovementDate());
        $je->setPostingPeriod($entity->getPostingPeriod());

        $je->getDocType($docType);
        $je->setCreatedBy($u);
        $je->setCreatedOn($entity->getCreatedOn());
        $je->setSysNumber($this->contextService->getControllerPlugin()
            ->getDocNumber($je));

        $je->setSourceClass(get_class($entity));
        $je->setSourceId($entity->getId());
        $je->setSourceToken($entity->getToken());

        if ($entity->getSysNumber() == \Application\Model\Constants::SYS_NUMBER_UNASSIGNED) {
            $entity->setSysNumber($this->contextService->getControllerPlugin()
                ->getDocNumber($entity));
        }

        $this->contextService->getDoctrineEM()->persist($je);

        /**
         * debit: inventory
         * credit: expenses account.
         */

        $n = 0;
        foreach ($rows as $r) {

            /** @var \Application\Entity\NmtInventoryTrx $r */

            if ($r->getQuantity() == 0) {
                continue;
            }

            $n ++;

            // Debit on inventory
            $je_row = new \Application\Entity\FinJeRow();
            $je_row->setJe($je);

            /**
             *
             * @todo: using account of item group or given.
             */
            $criteria = array(
                'id' => 3
            );
            $gl_account = $this->contextService->getDoctrineEM()
                ->getRepository('Application\Entity\FinAccount')
                ->findOneBy($criteria);

            $je_row->setGlAccount($gl_account);
            $je_row->setPostingKey(\Finance\Model\Constants::POSTING_KEY_DEBIT);
            $je_row->setPostingCode(\Finance\Model\Constants::POSTING_KEY_DEBIT);

            /**
             * Decimal doctrine.
             * Values retrieved from the database are always converted to PHP's string type or null if no data is present.
             */
            $q = (float) $r->getDocQuantity();
            $up = (float) $r->getDocUnitPrice();

            $je_row->setDocAmount($q * $up);
            $je_row->setLocalAmount((float) $r->getDocQuantity() * (float) $r->getDocUnitPrice() * $entity->getExchangeRate());
            $je_row->setCreatedBy($u);
            $je_row->setCreatedOn($entity->getCreatedOn());

            $je_row->setSysNumber($je->getSysNumber() . "-" . $n);
            $je_row->setJeMemo("WH GR " . $entity->getSysNumber() . (float) $r->getDocUnitPrice());

            $this->contextService->getDoctrineEM()->persist($je_row);
            $n ++;

            // Credit on expense
            $je_row = new \Application\Entity\FinJeRow();

            $je_row->setJe($je);
            $je_row->setPostingKey(\Finance\Model\Constants::POSTING_KEY_CREDIT);
            $je_row->setPostingCode(\Finance\Model\Constants::POSTING_KEY_CREDIT);

            $criteria = array(
                'id' => 6
            );
            $gl_account = $this->contextService->getDoctrineEM()
                ->getRepository('Application\Entity\FinAccount')
                ->findOneBy($criteria);
            $je_row->setGlAccount($gl_account);

            $je_row->setDocAmount($r->getDocQuantity() * $r->getDocUnitPrice());
            $je_row->setLocalAmount($r->getDocQuantity() * $r->getDocUnitPrice() * $r->getExchangeRate());

            $je_row->setSysNumber($je->getSysNumber() . "-" . $n);
            $je_row->setJeMemo("WH GR " . $entity->getSysNumber());

            $je_row->setCreatedBy($u);
            $je_row->setCreatedOn($entity->getCreatedOn());

            $this->contextService->getDoctrineEM()->persist($je_row);
        }

        if ($isFlush == TRUE) {
            $this->contextService->getDoctrineEM()->flush();
        }
    }

    /**
     * Templete Method
     *
     * @param \Application\Entity\NmtInventoryMv $entity
     * @param \Application\Entity\MlaUsers $u
     * @param bool $isFlush
     */
    public function runGIPosting($entity, $u, $isFlush = TRUE)
    {
        if (! $entity instanceof \Application\Entity\NmtInventoryMv) {
            throw new \Exception("Movement is found");
        }
        $criteria = array(
            'movement' => $entity
        );

        $sort = array();

        $rows = $this->contextService->getDoctrineEM()
            ->getRepository('Application\Entity\NmtInventoryTrx')
            ->findBy($criteria, $sort);

        if (count($rows) == 0) {
            throw new \Exception("Movement is empty");
        }

        /**
         * STEP 1: Calculate COGS and create consumption of FIFO Layer.
         * =====================
         */
        $fifoLayerService = new FIFOLayerService();
        $fifoLayerService->setDoctrineEM($this->contextService->getDoctrineEM());
        $fifoLayerService->setControllerPlugin($this->contextService->getControllerPlugin());

        foreach ($rows as $r) {

            /** @var \Application\Entity\NmtInventoryTrx $r */
            if ($r->getQuantity() == 0) {
                continue;
            }

            // update transaction row
            $r->setTrxDate($entity->getMovementDate());
            $r->setDocStatus(\Application\Model\Constants::DOC_STATUS_POSTED);
            $r->setDocType($entity->getDocStatus());
            $r->setTransactionType($entity->getMovementType());

            // calculate COGS
            $cogs = $fifoLayerService->calculateCOGS($r, $r->getItem(), $entity->getWarehouse(), $r->getQuantity(), $u);
            $r->setCogsLocal($cogs);
            $this->contextService->getDoctrineEM()->persist($r);
        }

        /**
         * STEP 3: DO SPECIFIC TASK FOR EACH TYPE OF TRANSACTION, if need
         * =====================
         */
        $this->doPosting($entity, $u, false);

        /**
         * STEP 4: CREATE JOURNAL ENTRY.
         * Can be overwritten
         * =====================
         */
        $this->createJournalEntryForGI($entity, $rows, $u, false);

        if ($entity->getSysNumber() == \Application\Model\Constants::SYS_NUMBER_UNASSIGNED) {
            $entity->setSysNumber($this->contextService->getControllerPlugin()
                ->getDocNumber($entity));
        }

        /**
         * STEP 5: UPDATE HEADER
         * =====================
         */
        $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_POSTED);
        $entity->setIsDraft(0);
        $entity->setIsPosted(1);
        $this->contextService->getDoctrineEM()->persist($entity);

        if ($isFlush == TRUE) {
            $this->contextService->getDoctrineEM()->flush();
        }
    }

    /**
     *
     * @param \Application\Entity\NmtInventoryMv $entity
     * @param array $rows
     * @param \Application\Entity\MlaUsers $u
     * @param bool $isFlush
     */
    protected function createJournalEntryForGI($entity, $rows, $u, $isFlush)
    {
        if (count($rows) == 0) {
            throw new \Exception("WH Transaction is empty");
        }

        // Create JE
        $je = new \Application\Entity\FinJe();
        $je->setCurrency($entity->getCurrency());
        $je->setLocalCurrency($entity->getCurrency());
        $je->setExchangeRate($entity->getExchangeRate());

        $je->setPostingDate($entity->getMovementDate());
        $je->setDocumentDate($entity->getMovementDate());
        $je->setPostingPeriod($entity->getPostingPeriod());

        $je->getDocType("JE");
        $je->setCreatedBy($u);
        $je->setCreatedOn($entity->getCreatedOn());
        $je->setSysNumber($this->contextService->getControllerPlugin()
            ->getDocNumber($je));

        $je->setSourceClass(get_class($entity));
        $je->setSourceId($entity->getId());
        $je->setSourceToken($entity->getToken());

        if ($entity->getSysNumber() == \Application\Model\Constants::SYS_NUMBER_UNASSIGNED) {
            $entity->setSysNumber($this->contextService->getControllerPlugin()
                ->getDocNumber($entity));
        }

        $this->contextService->getDoctrineEM()->persist($je);

        $n = 0;
        foreach ($rows as $r) {

            /** @var \Application\Entity\NmtInventoryTrx $r */

            if ($r->getQuantity() == 0) {
                continue;
            }

            $n ++;

            // generate JE voucher.
            // Create JE Row - DEBIT

            $je_row = new \Application\Entity\FinJeRow();
            $je_row->setJe($je);

            $je_row->setPostingKey(\Finance\Model\Constants::POSTING_KEY_DEBIT);
            $je_row->setPostingCode(\Finance\Model\Constants::POSTING_KEY_DEBIT);

            // Debit on Cost Account
            $criteria = array(
                'id' => 6
            );
            $gl_account = $this->contextService->getDoctrineEM()
                ->getRepository('Application\Entity\FinAccount')
                ->findOneBy($criteria);
            $je_row->setGlAccount($gl_account);
            $je_row->setDocAmount((float) $r->getCogsLocal());
            $je_row->setLocalAmount((float) $r->getCogsLocal());

            $je_row->setSysNumber($je->getSysNumber() . "-" . $n);
            $je_row->setJeMemo("WH GI " . $entity->getSysNumber());

            $je_row->setCreatedBy($u);
            $je_row->setCreatedOn($entity->getCreatedOn());

            $this->contextService->getDoctrineEM()->persist($je_row);

            // Create JE Row - Credit
            $n ++;

            $je_row = new \Application\Entity\FinJeRow();
            $je_row->setJe($je);

            // credit on inventory
            /**
             *
             * @todo: using account of item group or given.
             */
            $criteria = array(
                'id' => 3
            );
            $gl_account = $this->contextService->getDoctrineEM()
                ->getRepository('Application\Entity\FinAccount')
                ->findOneBy($criteria);
            $je_row->setGlAccount($gl_account);
            $je_row->setPostingKey(\Finance\Model\Constants::POSTING_KEY_CREDIT);
            $je_row->setPostingCode(\Finance\Model\Constants::POSTING_KEY_CREDIT);

            // cogs in local
            $je_row->setDocAmount((float) $r->getCogsLocal());
            $je_row->setLocalAmount((float) $r->getCogsLocal());

            $je_row->setSysNumber($je->getSysNumber() . "-" . $n);
            $je_row->setJeMemo("WH GI " . $entity->getSysNumber());

            $je_row->setCreatedBy($u);
            $je_row->setCreatedOn($entity->getCreatedOn());

            $this->contextService->getDoctrineEM()->persist($je_row);
        }

        if ($isFlush == TRUE) {
            $this->contextService->getDoctrineEM()->flush();
        }
    }

    abstract public function check($trx, $item, $u);

    /**
     *
     * @param \Application\Entity\NmtInventoryTrx $entity
     * @param array $data
     * @param \Application\Entity\MlaUsers $u
     * @param boolean $isNew
     * @param boolean $isPosting
     */
    abstract public function validateRow($entity, $data, $u, $isNew, $isPosting);

    /**
     *
     * @param \Application\Entity\NmtInventoryMv $entity
     * @param \Application\Entity\MlaUsers $u
     * @param bool $isFlush
     */
    abstract public function doPosting($entity, $u, $isFlush = false);

    /**
     *
     * @param \Application\Entity\NmtInventoryMv $entity
     * @param \Application\Entity\MlaUsers $u
     * @param \DateTime $reversalDate
     * @param string $reversalReason
     * @param bool $isFlush
     */
    abstract public function reverse($entity, $u, $reversalDate, $reversalReason, $isFlush = TRUE);

    /**
     *
     * @param array $rows
     * @param \Application\Entity\MlaUsers $u
     * @param boolean $isFlush
     * @param string $movementDate
     * @param object $wareHouse
     * @param string $trigger
     */
    abstract public function createMovement($rows, $u, $isFlush = false, $movementDate = null, $wareHouse = null, $trigger = null);

    /**
     *
     * @return \Application\Service\AbstractService
     */
    public function getContextService()
    {
        return $this->contextService;
    }

    /**
     *
     * @param \Application\Service\AbstractService $contextService
     */
    public function setContextService(\Application\Service\AbstractService $contextService)
    {
        $this->contextService = $contextService;
    }
}