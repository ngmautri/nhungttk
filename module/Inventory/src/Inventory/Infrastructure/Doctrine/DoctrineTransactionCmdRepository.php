<?php
namespace Inventory\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Inventory\Domain\Exception\InvalidArgumentException;
use Inventory\Domain\Warehouse\Transaction\GenericTransaction;
use Inventory\Domain\Warehouse\Transaction\TransactionCmdRepositoryInterface;
use Inventory\Domain\Warehouse\Transaction\TransactionRow;
use Inventory\Domain\Warehouse\Transaction\TransactionRowSnapshot;
use Ramsey;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DoctrineTransactionCmdRepository extends AbstractDoctrineRepository implements TransactionCmdRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Transaction\TransactionCmdRepositoryInterface::store()
     */
    public function store(GenericTransaction $transactionAggregate, $generateSysNumber = false, $isPosting = false)
    {
        // save header.
        $trxId = $this->storeHeader($transactionAggregate, $generateSysNumber, $isPosting);

        $n = 0;
        foreach ($transactionAggregate->getRows() as $row) {
            /**
             *
             * @var TransactionRow $row
             */

            $n ++;
            $snapshot = $row->makeSnapshot();
            $snapshot->movement = $trxId;
            $snapshot->sysNumber = $n;
            $row->makeFromSnapshot($snapshot);

            $this->createRow($trxId, $row, $isPosting);
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Transaction\TransactionCmdRepositoryInterface::createRow()
     */
    public function createRow($trxId, TransactionRow $row, $isPosting = false)
    {
        if ($row == null)
            throw new InvalidArgumentException("Inventory transaction row is empty");

        if ($trxId == null)
            throw new InvalidArgumentException("Inventory transaction Id is empty");

        // create snapshot
        $snapshot = $row->makeSnapshot();

        /**
         *
         * @var TransactionRowSnapshot $snapshot ;
         */
        if ($snapshot == null)
            throw new InvalidArgumentException("Transaction snapshot can not be created");

        $entity = new \Application\Entity\NmtInventoryTrx();
        $entity->setToken(Ramsey\Uuid\Uuid::uuid4()->toString());

        /**
         *
         * @var \Application\Entity\NmtInventoryMv $obj ;
         */
        $obj = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryMv')->find($trxId);

        $entity->setMovement($obj);
        $entity->setMvUuid($obj->getUuid());

        $snapshot->sysNumber = $obj->getSysNumber() . '-' . $snapshot->sysNumber;

        if ($snapshot->createdBy > 0) {

            /**
             *
             * @var \Application\Entity\MlaUsers $u ;
             */
            $u = $this->doctrineEM->find('Application\Entity\MlaUsers', $snapshot->createdBy);
            if ($u !== null)
                $entity->setCreatedBy($u);
        }

        $this->mapRowEntity($snapshot, $entity);

        $entity->setCreatedOn(new \DateTime());
        if ($isPosting) {
            $entity->setLastChangeOn(new \DateTime());
            $entity->setDocStatus(\Application\Domain\Shared\Constants::DOC_STATUS_POSTED);
            $entity->setIsDraft(0);
            $entity->setIsPosted(1);
        }

        // $entity->setId($snapshot->id);
        // $entity->setToken($snapshot->token);
        // $entity->setChecksum($snapshot->checksum);

        $this->doctrineEM->persist($entity);
        $this->doctrineEM->flush();
        return $entity->getId();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Transaction\TransactionRepositoryInterface::storeRow()
     */
    public function storeRow(GenericTransaction $trx, TransactionRow $row, $isPosting = false)
    {
        if ($row == null)
            throw new InvalidArgumentException("Inventory transaction row is empty");

        if ($trx == null)
            throw new InvalidArgumentException("Inventory transaction is empty");

        // create snapshot
        $snapshot = $row->makeSnapshot();

        /**
         *
         * @var ItemSnapshot $item ;
         */
        if ($snapshot == null)
            throw new InvalidArgumentException("Transaction snapshot can not be created");

        /**
         *
         * @var \Application\Entity\NmtInventoryTrx $entity ;
         */
        if ($row->getId() > 0) {
            $entity = $this->doctrineEM->find("\Application\Entity\NmtInventoryTrx", $row->getId());

            if ($entity == null)
                throw new InvalidArgumentException("Transaction row can't be retrieved.");

            $entity->setLastChangeOn(new \Datetime());
            if ($entity->getToken() == null) {
                $entity->setToken($entity->getUuid());
            }
        } else {

            $entity = new \Application\Entity\NmtInventoryTrx();
            $entity->setToken(Ramsey\Uuid\Uuid::uuid4()->toString());

            /**
             *
             * @var \Application\Entity\NmtInventoryMv $obj ;
             */
            $obj = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryMv')->find($trx->getId());
            $entity->setMovement($obj);
            $entity->setMvUuid($trx->getUuid());

            if ($snapshot->createdBy > 0) {

                /**
                 *
                 * @var \Application\Entity\MlaUsers $u ;
                 */
                $u = $this->doctrineEM->find('Application\Entity\MlaUsers', $snapshot->createdBy);
                if ($u !== null)
                    $entity->setCreatedBy($u);
            }
            $entity->setCreatedOn(new \DateTime());
        }

        if ($isPosting) {
            $entity->setLastChangeOn(new \DateTime());
            $entity->setDocStatus(\Application\Domain\Shared\Constants::DOC_STATUS_POSTED);
            $entity->setIsDraft(0);
            $entity->setIsPosted(1);
        }

        $this->mapRowEntity($snapshot, $entity);

        // $entity->setId($snapshot->id);
        // $entity->setToken($snapshot->token);
        // $entity->setChecksum($snapshot->checksum);

        $this->doctrineEM->persist($entity);
        $this->doctrineEM->flush();
        return $entity->getId();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Transaction\TransactionRepositoryInterface::post()
     */
    public function post(GenericTransaction $trx, $generateSysNumber = True)
    {
        if ($trx == null)
            throw new InvalidArgumentException("Transaction not retrieved.");

        /**
         *
         * @var \Application\Entity\NmtInventoryMv $entity ;
         */
        $entity = $this->doctrineEM->find("\Application\Entity\NmtInventoryMv", $trx->getId());

        if ($entity == null)
            throw new InvalidArgumentException("Transction Entity not retrieved.");

        if ($generateSysNumber) {
            $entity->setSysNumber($this->generateSysNumber($entity));
        }

        $entity->setLastChangeOn(new \DateTime());
        $entity->setDocStatus(\Application\Domain\Shared\Constants::DOC_STATUS_POSTED);
        $entity->setIsDraft(0);
        $entity->setIsPosted(1);

        $rows = $trx->getTransactionRows();
        $n = 0;
        foreach ($rows as $row) {

            /** @var TransactionRow $row ; */

            /** @var \Application\Entity\NmtInventoryTrx $r ; */
            $r = $this->doctrineEM->find("\Application\Entity\NmtInventoryTrx", $row->getId());

            if ($r == null) {
                continue;
            }

            $n ++;

            // update transaction row
            $r->setTrxDate($entity->getMovementDate());
            $r->setDocStatus($entity->getDocStatus());
            $r->setDocType($entity->getMovementType());
            $r->setTransactionType($entity->getMovementType());
            $r->setCogsLocal($row->getCogsLocal());
            $r->setSysNumber($entity->getSysNumber() . '-' . $n);
            $this->doctrineEM->persist($r);
        }

        $this->doctrineEM->persist($entity);
        $this->doctrineEM->flush();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Transaction\TransactionRepositoryInterface::storeHeader()
     */
    public function storeHeader(GenericTransaction $trxAggregate, $generateSysNumber = false, $isPosting = false)
    {
        if ($trxAggregate == null)
            throw new InvalidArgumentException("Transaction is empty");

        // create snapshot
        $snapshot = $trxAggregate->makeSnapshot();
        /**
         *
         * @var TransactionSnapshot $snapshot ;
         */
        if ($snapshot == null)
            throw new InvalidArgumentException("Transction snapshot not created!");
        /**
         *
         * @var \Application\Entity\NmtInventoryMv $entity ;
         */
        if ($trxAggregate->getId() > 0) {
            $entity = $this->doctrineEM->find("\Application\Entity\NmtInventoryMv", $trxAggregate->getId());
            if ($entity == null)
                throw new InvalidArgumentException("Transction not retrieved.");

            $entity->setLastChangeOn(new \DateTime());
            if ($entity->getToken() == null) {
                $entity->setToken($entity->getUuid());
            }
        } else {
            $entity = new \Application\Entity\NmtInventoryMv();
            $entity->setUuid(Ramsey\Uuid\Uuid::uuid4()->toString());
            $entity->setToken($entity->getUuid());

            if ($snapshot->createdBy > 0) {
                /**
                 *
                 * @var \Application\Entity\MlaUsers $u ;
                 */
                $u = $this->doctrineEM->find('Application\Entity\MlaUsers', $snapshot->createdBy);
                if ($u !== null) {
                    $entity->setCreatedBy($u);
                    $entity->setCompany($u->getCompany());
                }
            }
            $entity->setCreatedOn(new \DateTime());
        }

     

        if ($snapshot->warehouse > 0) {

            /**
             *
             * @var \Application\Entity\NmtInventoryWarehouse $wh ;
             */
            $wh = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->find($snapshot->warehouse);
            $entity->setWarehouse($wh);
        }

        if ($snapshot->postingPeriod > 0) {

            /**
             *
             * @var \Application\Entity\NmtFinPostingPeriod $postingPeriod ;
             */
            $postingPeriod = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod')->find($snapshot->postingPeriod);
            $entity->setPostingPeriod($postingPeriod);
        }

        if ($snapshot->docCurrency > 0) {

            /**
             *
             * @var \Application\Entity\NmtApplicationCurrency $docCurrency ;
             */
            $docCurrency = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->find($snapshot->docCurrency);
            $entity->setDocCurrency($docCurrency);
        }

        if ($snapshot->localCurrency > 0) {

            /**
             *
             * @var \Application\Entity\NmtApplicationCurrency $localCurrency ;
             */
            $localCurrency = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->find($snapshot->localCurrency);
            $entity->setLocalCurrency($localCurrency);
        }

        if ($snapshot->targetWarehouse > 0) {

            /**
             *
             * @var \Application\Entity\NmtInventoryWarehouse $targetWH ;
             */
            $targetWH = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->find($snapshot->targetWarehouse);
            $entity->setTargetWarehouse($targetWH);
        }

        if ($snapshot->sourceLocation > 0) {

            /**
             *
             * @var \Application\Entity\NmtInventoryWarehouseLocation $sourceLocation ;
             */
            $sourceLocation = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouseLocation')->find($snapshot->sourceLocation);
            $entity->setSourceLocation($sourceLocation);
        }

        if ($snapshot->tartgetLocation > 0) {

            /**
             *
             * @var \Application\Entity\NmtInventoryWarehouseLocation $targetLocation ;
             */
            $targetLocation = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouseLocation')->find($snapshot->tartgetLocation);
            $entity->setTartgetLocation($targetLocation);
        }

        // $entity->setId($snapshot->id);
        // $entity->setToken($snapshot->token);

        $entity->setCurrencyIso3($snapshot->currencyIso3);
        $entity->setExchangeRate($snapshot->exchangeRate);
        $entity->setRemarks($snapshot->remarks);

        // $entity->setCreatedOn($snapshot->createdOn);
        $entity->setCurrentState($snapshot->currentState);
        $entity->setIsActive($snapshot->isActive);
        $entity->setTrxType($snapshot->trxType);

        // $entity->setLastchangeBy($snapshot->lastchangeBy);
        // $entity->setLastchangeOn($snapshot->lastchangeOn);

        $entity->setSapDoc($snapshot->sapDoc);

        $entity->setContractNo($snapshot->contractNo);

        if ($snapshot->contractDate !== null) {
            $entity->setContractDate(new \DateTime($snapshot->contractDate));
        }

        $entity->setQuotationNo($snapshot->quotationNo);

        if ($snapshot->quotationDate !== null) {
            $entity->setQuotationDate(new \DateTime($snapshot->quotationDate));
        }

        $entity->setRevisionNo($snapshot->revisionNo);
        $entity->setDeliveryMode($snapshot->deliveryMode);

        $entity->setIncoterm($snapshot->incoterm);
        $entity->setIncotermPlace($snapshot->incotermPlace);

        $entity->setPaymentTerm($snapshot->paymentTerm);
        $entity->setPaymentMethod($snapshot->paymentMethod);

        $entity->setDocStatus($snapshot->docStatus);
        $entity->setIsDraft($snapshot->isDraft);
        $entity->setWorkflowStatus($snapshot->workflowStatus);
        $entity->setTransactionStatus($snapshot->transactionStatus);

        $entity->setMovementType($snapshot->movementType);

        if ($snapshot->movementDate !== null) {
            $entity->setMovementDate(new \DateTime($snapshot->movementDate));
            $entity->setPostingDate(new \DateTime($snapshot->movementDate));
        }

        $entity->setJournalMemo($snapshot->journalMemo);
        $entity->setMovementFlow($snapshot->movementFlow);
        $entity->setMovementTypeMemo($snapshot->movementTypeMemo);
        $entity->setIsPosted($snapshot->isPosted);
        $entity->setIsReversed($snapshot->isReversed);
        // $entity->setReversalDate($snapshot->reversalDate);
        $entity->setReversalDoc($snapshot->reversalDoc);
        $entity->setReversalReason($snapshot->reversalReason);
        $entity->setIsReversable($snapshot->isReversable);
        $entity->setDocType($snapshot->docType);
        $entity->setIsTransferTransaction($snapshot->isTransferTransaction);
        $entity->setReversalBlocked($snapshot->reversalBlocked);

        // $entity->setUuid($snapshot->uuid);
        // $entity->setCreatedBy($snapshot->createdBy);
        // $entity->setCompany($snapshot->company);
        // $entity->setWarehouse($snapshot->warehouse);
        // $entity->setPostingPeriod($snapshot->postingPeriod);
        // $entity->setCurrency($snapshot->currency);
        // $entity->setDocCurrency($snapshot->docCurrency);
        // $entity->setLocalCurrency($snapshot->localCurrency);
        // $entity->setTargetWarehouse($snapshot->targetWarehouse);
        // $entity->setSourceLocation($snapshot->sourceLocation);
        // $entity->setTartgetLocation($snapshot->tartgetLocation);
        
        if ($isPosting) {
            $entity->setLastChangeOn(new \DateTime());
            $entity->setDocStatus(\Application\Domain\Shared\Constants::DOC_STATUS_POSTED);
            $entity->setIsDraft(0);
            $entity->setIsPosted(1);
        }
        
        if ($generateSysNumber) {
            $entity->setSysNumber($this->generateSysNumber($entity));
        }

        $this->doctrineEM->persist($entity);
        $this->doctrineEM->flush();
        return $entity->getId();
    }

    /**
     *
     * @param TransactionRowSnapshot $snapshot
     * @param \Application\Entity\NmtInventoryTrx $entity
     * @return NULL|\Application\Entity\NmtInventoryTrx
     */
    private function mapRowEntity(TransactionRowSnapshot $snapshot, \Application\Entity\NmtInventoryTrx $entity)
    {
        if ($snapshot == null || $entity == null)
            return null;

        if ($snapshot->createdBy > 0) {

            /**
             *
             * @var \Application\Entity\MlaUsers $obj ;
             */
            $obj = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->find($snapshot->createdBy);
            $entity->setCreatedBy($obj);
        }

        if ($snapshot->item > 0) {

            /**
             *
             * @var \Application\Entity\NmtInventoryItem $obj ;
             */
            $obj = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->find($snapshot->item);
            $entity->setItem($obj);
        }

        if ($snapshot->pr > 0) {

            /**
             *
             * @var \Application\Entity\NmtProcurePr $obj ;
             */
            $obj = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePr')->find($snapshot->pr);
            $entity->setPr($obj);
        }

        if ($snapshot->po > 0) {

            /**
             *
             * @var \Application\Entity\NmtProcurePo $obj ;
             */
            $obj = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo')->find($snapshot->po);
            $entity->setPo($obj);
        }

        if ($snapshot->vendorInvoice > 0) {

            /**
             *
             * @var \Application\Entity\FinVendorInvoice $obj ;
             */
            $obj = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->find($snapshot->vendorInvoice);
            $entity->setVendorInvoice($obj);
        }

        if ($snapshot->poRow > 0) {

            /**
             *
             * @var \Application\Entity\NmtProcurePoRow $obj ;
             */
            $obj = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePoRow')->find($snapshot->poRow);
            $entity->setPoRow($obj);
        }

        if ($snapshot->grRow > 0) {

            /**
             *
             * @var \Application\Entity\NmtProcureGrRow $obj ;
             */
            $obj = $this->doctrineEM->getRepository('Application\Entity\NmtProcureGrRow')->find($snapshot->grRow);
            $entity->setGrRow($obj);
        }

        if ($snapshot->wh > 0) {

            /**
             *
             * @var \Application\Entity\NmtInventoryWarehouse$obj ;
             */
            $obj = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->find($snapshot->wh);
            $entity->setWh($obj);
        }

        if ($snapshot->issueFor > 0) {

            /**
             *
             * @var \Application\Entity\NmtInventoryItem $obj ;
             */
            $obj = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->find($snapshot->issueFor);
            $entity->setIssueFor($obj);
        }

        if ($snapshot->docCurrency > 0) {

            /**
             *
             * @var \Application\Entity\NmtApplicationCurrency $obj ;
             */
            $obj = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->find($snapshot->docCurrency);
            $entity->setDocCurrency($obj);
        }

        if ($snapshot->localCurrency > 0) {

            /**
             *
             * @var \Application\Entity\NmtApplicationCurrency $obj ;
             */
            $obj = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->find($snapshot->localCurrency);
            $entity->setLocalCurrency($obj);
        }

        if ($snapshot->project > 0) {

            /**
             *
             * @var \Application\Entity\NmtPmProject $obj ;
             */
            $obj = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->find($snapshot->project);
            $entity->setProject($obj);
        }

        if ($snapshot->costCenter > 0) {

            /**
             *
             * @var \Application\Entity\FinCostCenter $obj ;
             */
            $obj = $this->doctrineEM->getRepository('Application\Entity\FinCostCenter')->find($snapshot->costCenter);
            $entity->setCostCenter($obj);
        }

        if ($snapshot->docUom > 0) {

            /**
             *
             * @var \Application\Entity\NmtApplicationUom $obj ;
             */
            $obj = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationUom')->find($snapshot->docUom);
            $entity->setDocUom($obj);
        }

        if ($snapshot->postingPeriod > 0) {

            /**
             *
             * @var \Application\Entity\NmtFinPostingPeriod $obj ;
             */
            $obj = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod')->find($snapshot->postingPeriod);
            $entity->setPostingPeriod($obj);
        }

        if ($snapshot->whLocation > 0) {

            /**
             *
             * @var \Application\Entity\NmtInventoryWarehouseLocation $obj ;
             */
            $obj = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouseLocation')->find($snapshot->whLocation);
            $entity->setWhLocation($obj);
        }

        if ($snapshot->prRow > 0) {

            /**
             *
             * @var \Application\Entity\NmtProcurePrRow $obj ;
             */
            $obj = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouseLocation')->find($snapshot->prRow);
            $entity->setPrRow($obj);
        }

        if ($snapshot->vendor > 0) {

            /**
             *
             * @var \Application\Entity\NmtVendor $obj ;
             */
            $obj = $this->doctrineEM->getRepository('Application\Entity\NmtVendor')->find($snapshot->vendor);
            $entity->setVendor($obj);
        }

        if ($snapshot->currency > 0) {

            /**
             *
             * @var \Application\Entity\NmtApplicationCurrency $obj ;
             */
            $obj = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->find($snapshot->currency);
            $entity->setCurrency($obj);
        }

        if ($snapshot->pmtMethod > 0) {

            /**
             *
             * @var \Application\Entity\NmtApplicationPmtMethod $obj ;
             */
            $obj = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->find($snapshot->pmtMethod);
            $entity->setPmtMethod($obj);
        }

        if ($snapshot->invoiceRow > 0) {

            /**
             *
             * @var \Application\Entity\FinVendorInvoiceRow $obj ;
             */
            $obj = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoiceRow')->find($snapshot->invoiceRow);
            $entity->setInvoiceRow($obj);
        }

        // / Date

        if ($snapshot->createdOn !== null) {
            $entity->setCreatedOn(new \DateTime($snapshot->createdOn));
        }

        if ($snapshot->trxDate !== null) {
            $entity->setTrxDate(new \DateTime($snapshot->trxDate));
        }

        if ($snapshot->lastChangeOn !== null) {
            $entity->setLastChangeOn(new \DateTime($snapshot->lastChangeOn));
        }

        if ($snapshot->changeOn !== null) {
            $entity->setChangeOn(new \DateTime($snapshot->changeOn));
        }

        // $entity->setToken($snapshot->token);
        // $entity->setChecksum($snapshot->checksum);

        // $entity->setTrxDate($snapshot->trxDate);
        // $entity->setCreatedOn($snapshot->createdOn);
        // $entity->setLastChangeOn($snapshot->lastChangeOn);
        // $entity->setChangeOn($snapshot->changeOn);

        $entity->setTrxTypeId($snapshot->trxTypeId);
        $entity->setFlow($snapshot->flow);
        $entity->setQuantity($snapshot->quantity);
        $entity->setRemarks($snapshot->remarks);

        $entity->setIsLocked($snapshot->isLocked);
        $entity->setIsDraft($snapshot->isDraft);
        $entity->setIsActive($snapshot->isActive);

        $entity->setIsPreferredVendor($snapshot->isPreferredVendor);
        $entity->setVendorItemUnit($snapshot->vendorItemUnit);
        $entity->setVendorItemCode($snapshot->vendorItemCode);
        $entity->setConversionFactor($snapshot->conversionFactor);
        $entity->setConversionText($snapshot->conversionText);
        $entity->setVendorUnitPrice($snapshot->vendorUnitPrice);
        $entity->setPmtTermId($snapshot->pmtTermId);
        $entity->setDeliveryTermId($snapshot->deliveryTermId);
        $entity->setLeadTime($snapshot->leadTime);
        $entity->setTaxRate($snapshot->taxRate);
        $entity->setCurrentState($snapshot->currentState);
        $entity->setCurrentStatus($snapshot->currentStatus);
        $entity->setTargetId($snapshot->targetId);
        $entity->setTargetClass($snapshot->targetClass);
        $entity->setSourceId($snapshot->sourceId);
        $entity->setSourceClass($snapshot->sourceClass);
        $entity->setDocStatus($snapshot->docStatus);
        $entity->setSysNumber($snapshot->sysNumber);

        // $entity->setChangeBy($snapshot->changeBy);

        $entity->setRevisionNumber($snapshot->revisionNumber);
        $entity->setIsPosted($snapshot->isPosted);
        $entity->setActualQuantity($snapshot->actualQuantity);
        $entity->setTransactionStatus($snapshot->transactionStatus);
        $entity->setStockRemarks($snapshot->stockRemarks);
        $entity->setTransactionType($snapshot->transactionType);
        $entity->setItemSerialId($snapshot->itemSerialId);
        $entity->setItemBatchId($snapshot->itemBatchId);
        $entity->setCogsLocal($snapshot->cogsLocal);
        $entity->setCogsDoc($snapshot->cogsDoc);
        $entity->setExchangeRate($snapshot->exchangeRate);
        $entity->setConvertedStandardQuantity($snapshot->convertedStandardQuantity);
        $entity->setConvertedStandardUnitPrice($snapshot->convertedStandardUnitPrice);
        $entity->setConvertedStockQuantity($snapshot->convertedStockQuantity);
        $entity->setConvertedStockUnitPrice($snapshot->convertedStockUnitPrice);
        $entity->setConvertedPurchaseQuantity($snapshot->convertedPurchaseQuantity);
        $entity->setDocQuantity($snapshot->docQuantity);
        $entity->setDocUnitPrice($snapshot->docUnitPrice);
        $entity->setDocUnit($snapshot->docUnit);
        $entity->setIsReversed($snapshot->isReversed);
        $entity->setReversalDate($snapshot->reversalDate);
        $entity->setReversalDoc($snapshot->reversalDoc);
        $entity->setReversalReason($snapshot->reversalReason);
        $entity->setIsReversable($snapshot->isReversable);
        $entity->setDocType($snapshot->docType);
        $entity->setLocalUnitPrice($snapshot->localUnitPrice);
        $entity->setReversalBlocked($snapshot->reversalBlocked);
        $entity->setMvUuid($snapshot->mvUuid);

        //$entity->setCreatedBy($snapshot->createdBy);
        //$entity->setLastChangeBy($snapshot->lastChangeBy);

        /* 
         $entity->setItem($snapshot->item);
        $entity->setPr($snapshot->pr);
        $entity->setPo($snapshot->po);
        $entity->setVendorInvoice($snapshot->vendorInvoice);
        $entity->setPoRow($snapshot->poRow);
        $entity->setGrRow($snapshot->grRow);
        $entity->setInventoryGi($snapshot->inventoryGi);
        $entity->setInventoryGr($snapshot->inventoryGr);
        $entity->setInventoryTransfer($snapshot->inventoryTransfer);
        $entity->setWh($snapshot->wh);
        $entity->setGr($snapshot->gr);
        $entity->setMovement($snapshot->movement);
        $entity->setIssueFor($snapshot->issueFor);
        $entity->setDocCurrency($snapshot->docCurrency);
        $entity->setLocalCurrency($snapshot->localCurrency);
        $entity->setProject($snapshot->project);
        $entity->setCostCenter($snapshot->costCenter);
        $entity->setDocUom($snapshot->docUom);
        $entity->setPostingPeriod($snapshot->postingPeriod);
        $entity->setWhLocation($snapshot->whLocation);
        $entity->setPrRow($snapshot->prRow);
        $entity->setVendor($snapshot->vendor);
        $entity->setCurrency($snapshot->currency);
        $entity->setPmtMethod($snapshot->pmtMethod);
        $entity->setInvoiceRow($snapshot->invoiceRow); */

        return $entity;
    }
}
