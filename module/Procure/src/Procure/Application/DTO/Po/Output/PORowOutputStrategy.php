<?php
namespace Procure\Application\DTO\Po\Output;

use Application\Entity\FinVendorInvoiceRow;
use Procure\Application\DTO\Ap\APDocRowDTO;

/**
 * PR Row Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class PORowOutputStrategy
{

    const OUTPUT_IN_ARRAY = "1";

    const OUTPUT_IN_EXCEL = "2";

    const OUTPUT_IN_HMTL_TABLE = "3";

    const OUTPUT_IN_OPEN_OFFICE = "4";

    protected $output;

    /**
     *
     * @return mixed
     */
    public function getOutput()
    {
        if ($this->output == null)
            $this->output = array();

        return $this->output;
    }

    /**
     *
     * @param mixed $output
     */
    protected function setOutput($output)
    {
        $this->output = $output;
    }

    abstract public function createOutput($result);

    abstract public function createRowOutputFromSnapshot($result);

    /**
     *
     * @param FinVendorInvoiceRow $entity
     * @return NULL|\Procure\Application\DTO\Ap\APDocRowDTO
     */
    public function createDTOFrom(FinVendorInvoiceRow $entity = null)
    {
        if ($entity == null) {
            return null;
        }

        $snapshot = new APDocRowDTO();

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
