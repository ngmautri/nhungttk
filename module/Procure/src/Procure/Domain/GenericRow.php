<?php
namespace Procure\Domain;

use Application\Domain\Shared\SnapshotAssembler;
use Procure\Domain\AccountPayable\APSnapshot;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\Shared\ProcureDocStatus;

/**
 * Generic Row
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GenericRow extends BaseRow
{

    /**
     *
     * @param GenericRow $targetObj
     * @throws InvalidArgumentException
     * @return \Procure\Domain\GenericRow
     */
    public function convertTo(GenericRow $targetObj)
    {
        if (! $targetObj instanceof GenericRow) {
            throw new InvalidArgumentException("Convertion input invalid!");
        }

        // Converting
        // ==========================
        $exculdedProps = [
            "id",
            "uuid",
            "token",
            "instance",
            "sysNumber",
            "createdBy",
            "lastchangeBy",
            "docId",
            "docToken",
            "revisionNo",
            "docVersion"
        ];
        $sourceObj = $this;
        $reflectionClass = new \ReflectionClass(get_class($sourceObj));
        $props = $reflectionClass->getProperties();

        foreach ($props as $prop) {
            $prop->setAccessible(true);

            $propName = $prop->getName();
            if (property_exists($targetObj, $propName) && ! in_array($propName, $exculdedProps)) {
                $targetObj->$propName = $prop->getValue($sourceObj);
            }
        }
        return $targetObj;
    }

    /**
     *
     * @param int $createdBy
     * @param string $createdDate
     */
    protected function initRow($createdBy, $createdDate)
    {
        $this->setCreatedOn($createdDate);
        $this->setCreatedBy($createdBy);
        $this->setDocStatus(ProcureDocStatus::DRAFT);

        $this->setIsActive(1);
        $this->setIsDraft(1);
        $this->setIsPosted(0);

        $this->setRevisionNo(0);
        $this->setDocVersion(0);
        $this->setUuid(\Ramsey\Uuid\Uuid::uuid4()->toString());
        $this->setToken($this->getUuid());
    }

    protected function updateRow($createdBy, $createdDate)
    {
        $this->setCreatedOn($createdDate);
        $this->setCreatedBy($createdBy);
        $this->setDocStatus(ProcureDocStatus::DRAFT);

        $this->setIsActive(1);
        $this->setIsDraft(1);
        $this->setIsPosted(0);
    }

    /**
     *
     * @return void|\Procure\Domain\GenericRow
     */
    public function calculate()
    {
        if ($this->hasErrors()) {
            return;
        }

        try {
            // $this->convertedDocQuantity = $this->getDocQuantity() * $this->getConversionFactor();
            // $this->convertedDocUnitPrice = $this->getDocUnitPrice() / $this->getConvertedDocQuantity();

            // actuallly converted doc quantity /price.
            $this->quantity = $this->getDocQuantity() * $this->getConversionFactor();
            $this->unitPrice = $this->getDocUnitPrice() / $this->getConversionFactor();

            $netAmount = $this->getDocUnitPrice() * $this->getDocQuantity();

            $discountAmount = 0;
            if ($this->getDiscountRate() > 0) {
                $discountAmount = $netAmount * ($this->getDiscountRate() / 100);
                $this->setDiscountAmount($discountAmount);
                $netAmount = $netAmount - $discountAmount;
            }

            $taxAmount = $netAmount * $this->getTaxRate() / 100;
            $grosAmount = $netAmount + $taxAmount;

            $this->setNetAmount($netAmount);
            $this->setTaxAmount($taxAmount);
            $this->setGrossAmount($grosAmount);

            $convertedPurchaseQuantity = $this->getDocQuantity();
            $convertedPurchaseUnitPrice = $this->getDocUnitPrice();

            $conversionFactor = $this->getConversionFactor();

            $standardCF = 1;

            if ($this->getStandardConvertFactor() > 0) {
                $standardCF = $this->getStandardConvertFactor();
            }

            $prRowConvertFactor = $this->getPrRowConvertFactor();

            if ($this->getPrRow() > 0) {
                $convertedPurchaseQuantity = $convertedPurchaseQuantity * $conversionFactor;
                $convertedPurchaseUnitPrice = $convertedPurchaseUnitPrice / $conversionFactor;
                $standardCF = $standardCF * $prRowConvertFactor;
            }

            // quantity /unit price is converted purchase quantity to clear PR

            // $entity->setQuantity($convertedPurchaseQuantity);
            // $entity->setUnitPrice($convertedPurchaseUnitPrice);

            $convertedStandardQuantity = $this->getQuantity();
            $convertedStandardUnitPrice = $this->getUnitPrice();

            if ($this->getItem() > 0) {
                $convertedStandardQuantity = $convertedStandardQuantity * $standardCF;
                $convertedStandardUnitPrice = $convertedStandardUnitPrice / $standardCF;
            }

            // calculate standard quantity
            $this->setConvertedPurchaseQuantity($convertedPurchaseQuantity);
            $this->setConvertedPurchaseUnitPrice($convertedPurchaseUnitPrice);

            $this->setConvertedStandardQuantity($convertedStandardQuantity);
            $this->setConvertedStandardUnitPrice($convertedStandardUnitPrice);
        } catch (\Exception $e) {
            $this->addError($e->getMessage());
        }
    }

    /**
     * this should be called when validated.
     *
     * @return \Procure\Domain\PurchaseOrder\PORow
     */
    public function refresh()
    {
        if ($this->hasErrors()) {
            return;
        }

        $netAmount = $this->getDocUnitPrice() * $this->getDocQuantity();
        $taxAmount = $netAmount * $this->getTaxRate();
        $grosAmount = $netAmount + $taxAmount;

        $this->netAmount = $netAmount;
        $this->taxAmount = $taxAmount;
        $this->grossAmount = $grosAmount;
        return $this;
    }

    /**
     *
     * @param int $postedBy
     * @param string $postedDate
     */
    public function markAsPosted($postedBy, $postedDate)
    {
        $this->calculate();
        $this->setLastchangeOn($postedDate);
        $this->setLastchangeBy($postedBy);
        $this->setIsPosted(1);
        $this->setIsActive(1);
        $this->setIsDraft(0);
        $this->setIsReversed(0);
        $this->setDocStatus(ProcureDocStatus::POSTED);
    }

    /**
     *
     * @param int $postedBy
     * @param \DateTime $postedDate
     */
    public function markAsReversed($postedBy, $postedDate)
    {
        $this->calculate();

        $this->setLastchangeBy($postedBy);
        $this->setLastchangeOn($postedDate);
        $this->setIsReversed(1);
        $this->setIsActive(1);
        $this->setIsDraft(0);
        $this->setIsPosted(0);
        $this->setDocStatus(ProcureDocStatus::REVERSED);
    }

    public function refreshRowsFromNewHeaderSnapshot(APSnapshot $snapshot)
    {
        $this->setWarehouse($snapshot->getWarehouse());
    }

    public static function printProps()
    {
        $entity = new self();
        $reflectionClass = new \ReflectionClass($entity);
        $props = $reflectionClass->getProperties();
        foreach ($props as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print sprintf("\n public $%s;", $propertyName);
        }
    }

    /**
     *
     * @return NULL|object
     */
    public function makeSnapshot()
    {
        return SnapshotAssembler::createSnapshotFrom($this, new RowSnapshot());
    }
}
