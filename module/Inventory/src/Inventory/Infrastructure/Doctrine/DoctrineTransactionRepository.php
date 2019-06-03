<?php
namespace Inventory\Infrastructure\Doctrine;

use Doctrine\ORM\EntityManager;
use Inventory\Application\DTO\Warehouse\Transaction\TransactionRowDTOAssembler;
use Inventory\Domain\Exception\InvalidArgumentException;
use Inventory\Domain\Item\Factory\AbstractItemFactory;
use Inventory\Domain\Warehouse\Transaction\GenericTransaction;
use Inventory\Domain\Warehouse\Transaction\TransactionRepositoryInterface;
use Inventory\Domain\Warehouse\Transaction\TransactionRow;
use Ramsey;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DoctrineTransactionRepository implements TransactionRepositoryInterface
{

    /**
     *
     * @var EntityManager
     */
    private $doctrineEM;

    /**
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        if ($em == null) {
            throw new InvalidArgumentException("Doctrine Entity manager not found!");
        }
        $this->doctrineEM = $em;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Transaction\TransactionRepositoryInterface::getById()
     */
    public function getById($id)
    {
        $criteria = array(
            "id" => $id
        );

        /**
         *
         * @var \Application\Entity\NmtInventoryItem $entity ;
         */
        $entity = $this->doctrineEM->getRepository("\Application\Entity\NmtInventoryItem")->findOneBy($criteria);
        if ($entity == null)
            return null;

        $itemSnapshot = $this->createItemSnapshot($entity);

        $factory = AbstractItemFactory::getItemFacotory($itemSnapshot->itemTypeId);

        $item = $factory->createItem();
        $item->makeItemFrom($itemSnapshot);
        return $item;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Transaction\TransactionRepositoryInterface::getByUUID()
     */
    public function getByUUID($uuid)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Transaction\TransactionRepositoryInterface::store()
     */
    public function store(GenericTransaction $transactionAggregate, $generateSysNumber = True)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Transaction\TransactionRepositoryInterface::findAll()
     */
    public function findAll()
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Transaction\TransactionRepositoryInterface::storeRow()
     */
    public function storeRow(TransactionRow $row)
    {
        if ($row == null)
            throw new InvalidArgumentException("Inventory transaction row is empty");

        // create snapshot
        $snapshot = $row->makeSnapshot();

        /**
         *
         * @var ItemSnapshot $item ;
         */
        if ($snapshot == null)
            throw new InvalidArgumentException("Inventory transaction  can be created");

        /**
         *
         * @var \Application\Entity\NmtInventoryTrx $entity ;
         */
        if ($row->getId() > 0) {
            $entity = $this->doctrineEM->find("\Application\Entity\NmtInventoryTrx", $row->getId());

            if ($entity == null) {
                throw new InvalidArgumentException("Transaction can't be retrieved.");
            }

            $entity->setLastChangeOn($item->lastChangeOn);
            if ($entity->getToken() == null) {
                $entity->setToken($entity->getUuid());
            }
        } else {
            $entity = new \Application\Entity\NmtInventoryTrx();
            $entity->setToken(Ramsey\Uuid\Uuid::uuid4()->toString());

            if ($item->createdBy > 0) {

                /**
                 *
                 * @var \Application\Entity\MlaUsers $u ;
                 */
                $u = $this->doctrineEM->find('Application\Entity\MlaUsers', $item->createdBy);
                if ($u !== null)
                    $entity->setCreatedBy($u);
            }
            $entity->setCreatedOn(new \DateTime());
        }

        $entity->setId($snapshot->id);
        $entity->setToken($snapshot->token);
        $entity->setChecksum($snapshot->checksum);
        $entity->setTrxDate($snapshot->trxDate);
        $entity->setTrxTypeId($snapshot->trxTypeId);
        $entity->setFlow($snapshot->flow);
        $entity->setQuantity($snapshot->quantity);
        $entity->setRemarks($snapshot->remarks);
        $entity->setCreatedOn($snapshot->createdOn);
        $entity->setIsLocked($snapshot->isLocked);
        $entity->setIsDraft($snapshot->isDraft);
        $entity->setIsActive($snapshot->isActive);
        $entity->setLastChangeOn($snapshot->lastChangeOn);
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
        $entity->setChangeOn($snapshot->changeOn);
        $entity->setChangeBy($snapshot->changeBy);
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
        $entity->setCreatedBy($snapshot->createdBy);
        $entity->setLastChangeBy($snapshot->lastChangeBy);
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
        $entity->setInvoiceRow($snapshot->invoiceRow);

        // Need check one more time.
        $dto = TransactionRowDTOAssembler::createDTOFrom($entity);

        $factory = AbstractItemFactory::getItemFacotory($dto->itemTypeId);

        // will throw exception if false.
        $item = $factory->createItemFromDTO($dto);

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
    {}

    /**
     *
     * @param \Application\Entity\NmtInventoryItem $entity
     *
     */
    private function createsSnapshot($entity)
    {}
}
