<?php
namespace Inventory\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Inventory\Application\DTO\Warehouse\Transaction\Output\TransactionRowInArray;
use Inventory\Application\DTO\Warehouse\Transaction\Output\TransactionRowInExcel;
use Inventory\Application\DTO\Warehouse\Transaction\Output\TransactionRowInOpenOffice;
use Inventory\Application\DTO\Warehouse\Transaction\Output\TransactionRowOutputStrategy;
use Inventory\Domain\Warehouse\Transaction\TransactionQueryRepositoryInterface;
use Inventory\Domain\Warehouse\Transaction\TransactionRow;
use Inventory\Domain\Warehouse\Transaction\Factory\TransactionFactory;
use Inventory\Domain\Warehouse\Transaction\TransactionSnapshot;
use Inventory\Domain\Warehouse\Transaction\TransactionRowSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DoctrineTransactionQueryRepository extends AbstractDoctrineRepository implements TransactionQueryRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Transaction\TransactionRepositoryInterface::getHeaderById()
     */
    public function getHeaderById($trxId, $token = null)
    {
        if ($trxId == null)
            return null;

        $criteria = array(
            "id" => $trxId,
            "token" => $token
        );

        /**
         *
         * @var \Application\Entity\NmtInventoryMv $entity ;
         */
        $entity = $this->doctrineEM->getRepository("\Application\Entity\NmtInventoryMv")->findOneBy($criteria);

        /**
         *
         * @var TransactionSnapshot $snapshot ;
         */
        $snapshot = $this->createSnapshot($entity);

        if ($snapshot == null)
            return null;

        $trx = TransactionFactory::createTransaction($snapshot->movementType);

        if ($trx == null)
            return null;

        $trx->makeFromSnapshot($snapshot);
        return $trx;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Transaction\TransactionRepositoryInterface::getById()
     */
    public function getById($id, $outputStrategy = null)
    {
        if ($id == null)
            return null;

        $criteria = array(
            "id" => $id
        );

        /**
         *
         * @var \Application\Entity\NmtInventoryMv $entity ;
         */
        $entity = $this->doctrineEM->getRepository("\Application\Entity\NmtInventoryMv")->findOneBy($criteria);

        /**
         *
         * @var TransactionSnapshot $snapshot ;
         */
        $snapshot = $this->createSnapshot($entity);
        if ($snapshot == null)
            return null;

        $trx = TransactionFactory::createTransaction($snapshot->movementType);
        if ($trx == null)
            return null;

        $trx->makeFromSnapshot($snapshot);

        $criteria = array(
            'movement' => $entity
        );
        $sort = array();

        $rows = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryTrx')->findBy($criteria, $sort);

        if (count($rows) == 0)
            return $trx;

        switch ($outputStrategy) {
            case TransactionRowOutputStrategy::OUTPUT_IN_ARRAY:
                $factory = new TransactionRowInArray();
                break;
            case TransactionRowOutputStrategy::OUTPUT_IN_EXCEL:
                $factory = new TransactionRowInExcel();
                break;
            case TransactionRowOutputStrategy::OUTPUT_IN_OPEN_OFFICE:
                $factory = new TransactionRowInOpenOffice();
                break;

            case TransactionRowOutputStrategy::OUTPUT_IN_HMTL_TABLE:
                break;

            default:
                $factory = null;
                break;
        }

        foreach ($rows as $r) {

            /** @var \Application\Entity\NmtInventoryTrx $r */
            if ($r->getQuantity() == 0) {
                continue;
            }
            $snapshot = $this->createRowSnapshot($r);
            $transactionRow = new TransactionRow();
            $transactionRow->makeFromSnapshot($snapshot);
            $trx->addRow($transactionRow);

            if (! $factory == null) {
                $factory->createOutput($r);
            }
        }

        if (! $factory == null) {
            $trx->setTranstionRowsOutput($factory->getOutput());
        }
        return $trx;
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
     * @see \Inventory\Domain\Warehouse\Transaction\TransactionRepositoryInterface::findAll()
     */
    public function findAll()
    {}

    /**
     *
     * @param \Application\Entity\NmtInventoryMv $entity
     *
     */
    private function createSnapshot($entity)
    {
        if ($entity == null)
            return null;

        $snapshot = new TransactionSnapshot();

        // mapping referrence

        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
        }

        if ($entity->getLastChangeBy() !== null) {
            $snapshot->lastChangeBy = $entity->getLastChangeBy()->getId();
        }

        if ($entity->getCompany() !== null) {
            $snapshot->company = $entity->getCompany()->getId();
        }

        if ($entity->getPostingPeriod() !== null) {
            $snapshot->postingPeriod = $entity->getPostingPeriod()->getId();
        }

        if ($entity->getLocalCurrency() !== null) {
            $snapshot->localCurrency = $entity->getLocalCurrency()->getId();
        }

        if ($entity->getDocCurrency() !== null) {
            $snapshot->docCurrency = $entity->getDocCurrency()->getId();
        }

        if ($entity->getCurrency() !== null) {
            $snapshot->currency = $entity->getCurrency()->getId();
        }

        if ($entity->getWarehouse() !== null) {
            $snapshot->warehouse = $entity->getWarehouse()->getId();
        }

        if ($entity->getTargetWarehouse() !== null) {
            $snapshot->targetWarehouse = $entity->getTargetWarehouse()->getId();
        }

        if ($entity->getSourceLocation() !== null) {
            $snapshot->sourceLocation = $entity->getSourceLocation()->getId();
        }

        if ($entity->getTartgetLocation() !== null) {
            $snapshot->tartgetLocation = $entity->getTartgetLocation()->getId();
        }

        // $snapshot->postingPeriod;
        // $snapshot->localCurrency;
        // $snapshot->warehouse;
        // $snapshot->targetWarehouse;
        // $snapshot->sourceLocation;
        // $snapshot->tartgetLocation;

        // Mapping Date
        // =====================

        if (! $entity->getMovementDate() == null) {
            $snapshot->movementDate = $entity->getMovementDate()->format("Y-m-d");
        }

        if (! $entity->getPostingDate() == null) {
            $snapshot->postingDate = $entity->getPostingDate()->format("Y-m-d");
        }

        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();

        foreach ($itemProperites as $property) {

            $property->setAccessible(true);
            $propertyName = $property->getName();

            if (! is_object($property->getValue($entity))) {

                if (property_exists($snapshot, $propertyName)) {
                    $snapshot->$propertyName = $property->getValue($entity);
                }
            }
        }

        return $snapshot;
    }

    /**
     *
     * @param \Application\Entity\NmtInventoryTrx $entity
     *
     */
    private function createRowSnapshot($entity)
    {
        if ($entity == null)
            return null;

        $snapshot = new TransactionRowSnapshot();

        // mapping referrence

        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
        }

        if ($entity->getLastChangeBy() !== null) {
            $snapshot->lastChangeBy = $entity->getLastChangeBy()->getId();
        }

        if ($entity->getCostCenter() !== null) {
            $snapshot->costCenter = $entity->getCostCenter()->getId();
        }

        if ($entity->getPostingPeriod() !== null) {
            $snapshot->postingPeriod = $entity->getPostingPeriod()->getId();
        }

        if ($entity->getLocalCurrency() !== null) {
            $snapshot->localCurrency = $entity->getLocalCurrency()->getId();
        }

        if ($entity->getCurrency() !== null) {
            $snapshot->currency = $entity->getCurrency()->getId();
        }

        if ($entity->getWh() !== null) {
            $snapshot->wh = $entity->getWh()->getId();
        }

        if ($entity->getWhLocation() !== null) {
            $snapshot->whLocation = $entity->getWhLocation()->getId();
        }

        if ($entity->getGr() !== null) {
            $snapshot->gr = $entity->getGr()->getId();
        }

        if ($entity->getInvoiceRow() !== null) {
            $snapshot->invoiceRow = $entity->getInvoiceRow()->getId();
        }

        if ($entity->getIssueFor() !== null) {
            $snapshot->issueFor = $entity->getIssueFor()->getId();
        }

        if ($entity->getItem() !== null) {
            $snapshot->item = $entity->getItem()->getId();
        }

        if ($entity->getMovement() !== null) {
            $snapshot->movement = $entity->getMovement()->getId();
        }

        if ($entity->getPo() !== null) {
            $snapshot->po = $entity->getPo()->getId();
        }

        if ($entity->getPoRow() !== null) {
            $snapshot->poRow = $entity->getPoRow()->getId();
        }

        if ($entity->getPr() !== null) {
            $snapshot->pr = $entity->getPr()->getId();
        }

        if ($entity->getPrRow() !== null) {
            $snapshot->getPrRow = $entity->getPrRow()->getId();
        }

        if ($entity->getProject() !== null) {
            $snapshot->pr = $entity->getProject()->getId();
        }

        if ($entity->getVendor() !== null) {
            $snapshot->vendor = $entity->getVendor()->getId();
        }

        if ($entity->getVendorInvoice() !== null) {
            $snapshot->vendorInvoice = $entity->getVendorInvoice()->getId();
        }

        if ($entity->getGrRow() !== null) {
            $snapshot->grRow = $entity->getGrRow()->getId();
        }

        if ($entity->getDocCurrency() !== null) {
            $snapshot->docCurrency = $entity->getDocCurrency()->getId();
        }

        if ($entity->getProject() !== null) {
            $snapshot->project = $entity->getProject()->getId();
        }

        if ($entity->getDocUom() !== null) {
            $snapshot->docUom = $entity->getDocUom()->getId();
        }

        if ($entity->getPrRow() !== null) {
            $snapshot->prRow = $entity->getPrRow()->getId();
        }

        if ($entity->getPmtMethod() !== null) {
            $snapshot->pmtMethod = $entity->getPmtMethod()->getId();
        }
        // $snapshot->createdBy= $entity->getCreatedBy();
        // $snapshot->lastChangeBy= $entity->getLastChangeBy();
        // $snapshot->item= $entity->getItem();
        // $snapshot->pr= $entity->getPr();
        // $snapshot->po= $entity->getPo();
        // $snapshot->vendorInvoice= $entity->getVendorInvoice();
        // $snapshot->poRow= $entity->getPoRow();
        // $snapshot->grRow= $entity->getGrRow();
        // $snapshot->inventoryGi= $entity->getInventoryGi();
        // $snapshot->inventoryGr= $entity->getInventoryGr();
        // $snapshot->inventoryTransfer= $entity->getInventoryTransfer();
        // $snapshot->wh= $entity->getWh();
        // $snapshot->gr= $entity->getGr();
        // $snapshot->movement= $entity->getMovement();
        // $snapshot->issueFor= $entity->getIssueFor();
        // $snapshot->docCurrency= $entity->getDocCurrency();
        // $snapshot->localCurrency= $entity->getLocalCurrency();
        // $snapshot->project= $entity->getProject();
        // $snapshot->costCenter= $entity->getCostCenter();
        // $snapshot->docUom= $entity->getDocUom();

        // $snapshot->postingPeriod= $entity->getPostingPeriod();
        // $snapshot->whLocation= $entity->getWhLocation();
        // $snapshot->prRow= $entity->getPrRow();
        // $snapshot->vendor= $entity->getVendor();
        // $snapshot->currency= $entity->getCurrency();
        // $snapshot->pmtMethod= $entity->getPmtMethod();
        // $snapshot->invoiceRow= $entity->getInvoiceRow();

        // Mapping Date
        // =====================

        if (! $entity->getTrxDate() == null) {
            $snapshot->trxDate = $entity->getTrxDate()->format("Y-m-d");
        }

        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d");
        }

        if (! $entity->getLastChangeOn() == null) {
            $snapshot->lastChangeOn = $entity->getLastChangeOn()->format("Y-m-d");
        }

        if (! $entity->getChangeOn() == null) {
            $snapshot->changeOn = $entity->getChangeOn()->format("Y-m-d");
        }

        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();

        foreach ($itemProperites as $property) {

            $property->setAccessible(true);
            $propertyName = $property->getName();

            if (! is_object($property->getValue($entity))) {

                if (property_exists($snapshot, $propertyName)) {
                    $snapshot->$propertyName = $property->getValue($entity);
                }
            }
        }

        return $snapshot;
    }
}
