<?php
namespace Procure\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Procure\Application\DTO\Ap\Output\APDocRowOutputStrategy;
use Procure\Domain\APInvoice\APDocQueryRepositoryInterface;
use Procure\Domain\APInvoice\APInvoice;
use Procure\Application\DTO\Ap\Output\APDocRowInArray;
use Procure\Application\DTO\Ap\Output\APDocRowInExcel;
use Procure\Application\DTO\Ap\Output\ApDocRowInOpenOffice;
use Procure\Domain\APInvoice\APDocRow;
use Procure\Domain\APInvoice\APDocSnapshot;
use Procure\Domain\APInvoice\APDocRowSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DoctrineAPDocQueryRepository extends AbstractDoctrineRepository implements APDocQueryRepositoryInterface
{

    public function getHeaderById($id, $token = null)
    {}

    public function getById($id, $outputStrategy = null)
    {
        if ($id == null)
            return null;

        $criteria = array(
            "id" => $id
        );

        /**
         *
         * @var \Application\Entity\FinVendorInvoice $entity ;
         */
        $entity = $this->doctrineEM->getRepository("\Application\Entity\FinVendorInvoice")->findOneBy($criteria);

        /**
         *
         * @var APInvoiceSnapshot $snapshot ;
         */
        $snapshot = $this->createSnapshot($entity);
        if ($snapshot == null)
            return null;

        $aggregate = new APInvoice();
        if ($aggregate == null)
            return null;

        $aggregate->makeFromSnapshot($snapshot);

        $criteria = array(
            'invoice' => $entity
        );
        $sort = array();

        $rows = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoiceRow')->findBy($criteria, $sort);

        if (count($rows) == 0)
            return $aggregate;

        switch ($outputStrategy) {
            case APDocRowOutputStrategy::OUTPUT_IN_ARRAY:
                $factory = new APDocRowInArray();
                break;
            case APDocRowOutputStrategy::OUTPUT_IN_EXCEL:
                $factory = new APDocRowInExcel();
                break;
            case APDocRowOutputStrategy::OUTPUT_IN_OPEN_OFFICE:
                $factory = new ApDocRowInOpenOffice();
                break;

            case APDocRowOutputStrategy::OUTPUT_IN_HMTL_TABLE:
                break;

            default:
                $factory = null;
                break;
        }

        foreach ($rows as $r) {

            /** @var \Application\Entity\FinVendorInvoiceRow $r */
            if ($r->getQuantity() == 0) {
                continue;
            }
            $snapshot = $this->createRowSnapshot($r);
            $line = new APDocRow();
            $line->makeFromSnapshot($snapshot);
            $aggregate->addRow($line);

            /**
             *
             * @var APInvoiceRowOutputStrategy $factory ;
             */
            if (! $factory == null)
                $factory->createOutput($r);
        }

        if (! $factory == null)
            $aggregate->setRowsOutput($factory->getOutput());

        return $aggregate;
    }

    public function getByUUID($uuid)
    {}

    public function findAll()
    {}

    /**
     *
     * @param \Application\Entity\FinVendorInvoice $entity
     *            ;
     *            
     */
    private function createSnapshot($entity)
    {
        if ($entity == null)
            return null;

        $snapshot = new APDocSnapshot();

        // mapping referrence

        // $snapshot->createdBy= $entity->getCreatedBy();
        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
        }

        // $snapshot->lastchangeBy= $entity->getLastchangeBy();
        if ($entity->getLastChangeBy() !== null) {
            $snapshot->lastChangeBy = $entity->getLastChangeBy()->getId();
        }

        // $snapshot->company= $entity->getCompany();
        if ($entity->getCompany() !== null) {
            $snapshot->company = $entity->getCompany()->getId();
        }

        // $snapshot->vendor= $entity->getVendor();
        if ($entity->getVendor() !== null) {
            $snapshot->vendor = $entity->getVendor()->getId();
        }

        // $snapshot->procureGr= $entity->getProcureGr();
        if ($entity->getProcureGr() !== null) {
            $snapshot->procureGr = $entity->getProcureGr()->getId();
        }

        // $snapshot->localCurrency= $entity->getLocalCurrency();
        if ($entity->getLocalCurrency() !== null) {
            $snapshot->localCurrency = $entity->getLocalCurrency()->getId();
        }

        // $snapshot->docCurrency= $entity->getDocCurrency();
        if ($entity->getDocCurrency() !== null) {
            $snapshot->docCurrency = $entity->getDocCurrency()->getId();
        }

        // $snapshot->postingPeriod= $entity->getPostingPeriod();
        if ($entity->getPostingPeriod() !== null) {
            $snapshot->postingPeriod = $entity->getPostingPeriod()->getId();
        }

        // $snapshot->warehouse = $entity->getWarehouse();
        if ($entity->getWarehouse() !== null) {
            $snapshot->warehouse = $entity->getWarehouse()->getId();
        }

        // $snapshot->incoterm2 = $entity->getIncoterm2();
        if ($entity->getIncoterm2() !== null) {
            $snapshot->incoterm2 = $entity->getIncoterm2()->getId();
        }

        // $snapshot->pmtTerm = $entity->getPmtTerm();
        if ($entity->getPmtTerm() !== null) {
            $snapshot->pmtTerm = $entity->getPmtTerm()->getId();
        }

        // $snapshot->currency = $entity->getCurrency();
        if ($entity->getCurrency() !== null) {
            $snapshot->currency = $entity->getCurrency()->getId();
        }

        // $snapshot->po = $entity->getPo();
        if ($entity->getPo() !== null) {
            $snapshot->po = $entity->getPo()->getId();
        }

        // $snapshot->paymentMethod = $entity->getPaymentMethod();
        if ($entity->getPaymentMethod() !== null) {
            $snapshot->paymentMethod = $entity->getPaymentMethod()->getId();
        }

        // $snapshot->inventoryGr = $entity->getInventoryGr();
        if ($entity->getInventoryGr() !== null) {
            $snapshot->inventoryGr = $entity->getInventoryGr()->getId();
        }
        // Mapping Date
        // =====================

        // $snapshot->invoiceDate = $entity->getInvoiceDate();
        if (! $entity->getInvoiceDate() == null) {
            $snapshot->invoiceDate = $entity->getInvoiceDate()->format("Y-m-d");
        }

        // $snapshot->postingDate = $entity->getPostingDate();
        if (! $entity->getPostingDate() == null) {
            $snapshot->postingDate = $entity->getPostingDate()->format("Y-m-d");
        }

        // $snapshot->createdOn = $entity->getCreatedOn();
        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d");
        }

        // $snapshot->lastchangeOn = $entity->getLastchangeOn();
        if (! $entity->getLastchangeOn() == null) {
            $snapshot->lastchangeOn = $entity->getLastchangeOn()->format("Y-m-d");
        }

        // $snapshot->grDate = $entity->getGrDate();
        if (! $entity->getGrDate() == null) {
            $snapshot->grDate = $entity->getGrDate()->format("Y-m-d");
        }

        // $snapshot->reversalDate = $entity->getReversalDate();
        if (! $entity->getReversalDate() == null) {
            $snapshot->reversalDate = $entity->getReversalDate()->format("Y-m-d");
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
    */
    private function createRowSnapshot($entity)
    {
        if ($entity == null)
            return null;

        $snapshot = new APDocRowSnapshot();

        // Mapping Reference
        // =====================

        // $snapshot->invoice= $entity->getInvoice();
        if ($entity->getInvoice() !== null) {
            $snapshot->invoice = $entity->getInvoice()->getId();
        }

        // $snapshot->glAccount= $entity->getGlAccount();
        if ($entity->getGlAccount() !== null) {
            $snapshot->glAccount = $entity->getGlAccount()->getId();
        }

        // $snapshot->costCenter= $entity->getCostCenter();
        if ($entity->getCostCenter() !== null) {
            $snapshot->costCenter = $entity->getCostCenter()->getId();
        }

        // $snapshot->docUom = $entity->getDocUom();
        if ($entity->getDocUom() !== null) {
            $snapshot->docUom = $entity->getDocUom()->getId();
        }

        // $snapshot->prRow = $entity->getPrRow();
        if ($entity->getPrRow() !== null) {
            $snapshot->prRow = $entity->getPrRow()->getId();
        }

        // $snapshot->createdBy = $entity->getCreatedBy();
        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
        }

        // $snapshot->warehouse = $entity->getWarehouse();
        if ($entity->getWarehouse() !== null) {
            $snapshot->warehouse = $entity->getWarehouse()->getId();
        }

        // $snapshot->lastchangeBy = $entity->getLastchangeBy();
        if ($entity->getLastchangeBy() !== null) {
            $snapshot->lastchangeBy = $entity->getLastchangeBy()->getId();
        }

        // $snapshot->poRow = $entity->getPoRow();
        if ($entity->getPoRow() !== null) {
            $snapshot->poRow = $entity->getPoRow()->getId();
        }

        // $snapshot->item = $entity->getItem();
        if ($entity->getItem() !== null) {
            $snapshot->item = $entity->getItem()->getId();
        }

        // $snapshot->grRow = $entity->getGrRow();
        if ($entity->getGrRow() !== null) {
            $snapshot->grRow = $entity->getGrRow()->getId();
        }

        // Mapping Date
        // =====================

        $snapshot->createdOn = $entity->getCreatedOn();
        $snapshot->lastchangeOn = $entity->getLastchangeOn();
        $snapshot->reversalDate = $entity->getReversalDate();

        if (! $entity->getReversalDate() == null) {
            $snapshot->reversalDate = $entity->getReversalDate()->format("Y-m-d");
        }

        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d");
        }

        if (! $entity->getLastChangeOn() == null) {
            $snapshot->lastChangeOn = $entity->getLastChangeOn()->format("Y-m-d");
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
