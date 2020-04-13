<?php
namespace Procure\Domain;

use Application\Domain\Shared\SnapshotAssembler;
use Procure\Domain\Shared\ProcureDocStatus;

/**
 * Generic Row
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GenericRow extends AbstractRow
{

    protected function calculate()
    {
        $convertedPurchaseQuantity = $this->getDocQuantity();
        $convertedPurchaseUnitPrice = $this->getDocUnitPrice();

        $conversionFactor = $this->getConversionFactor();
        $standardCF = $this->getConversionFactor();

        $prRowConvertFactor = $this->getPrRowConvertFactor();

        if ($this->getPrRow() > 0) {
            $convertedPurchaseQuantity = $convertedPurchaseQuantity * $conversionFactor;
            $convertedPurchaseUnitPrice = $convertedPurchaseUnitPrice / $conversionFactor;
            $standardCF = $standardCF * $prRowConvertFactor;
        }

        // quantity /unit price is converted purchase quantity to clear PR

        // $entity->setQuantity($convertedPurchaseQuantity);
        // $entity->setUnitPrice($convertedPurchaseUnitPrice);

        $convertedStandardQuantity = $this->getDocQuantity();
        $convertedStandardUnitPrice = $this->getDocUnitPrice();

        if ($this->getItem() > 0) {
            $convertedStandardQuantity = $convertedStandardQuantity * $standardCF;
            $convertedStandardUnitPrice = $convertedStandardUnitPrice / $standardCF;
        }

        // calculate standard quantity
        $this->setConvertedPurchaseQuantity($convertedPurchaseQuantity);
        $this->setConvertedPurchaseUnitPrice($convertedPurchaseUnitPrice);

        $this->setConvertedStandardQuantity($convertedStandardQuantity);
        $this->setConvertedStandardUnitPrice($convertedStandardUnitPrice);
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
        $this->setIsDraft(0);
        $this->setDocStatus(ProcureDocStatus::DOC_STATUS_POSTED);
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
        $this->setIsDraft(0);
        $this->setDocStatus(ProcureDocStatus::DOC_STATUS_REVERSED);
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
