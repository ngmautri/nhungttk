<?php
namespace Procure\Domain;

use Application\Domain\Shared\SnapshotAssembler;
use function Procure\Domain\BaseDoc\getDocRows as count;
use function Procure\Domain\BaseDoc\getRowIdArray as in_array;
use Procure\Domain\Shared\Constants;
use Procure\Domain\Shared\ProcureDocStatus;
use Ramsey\Uuid\Uuid;
use Closure;
use InvalidArgumentException;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GenericDoc extends BaseDoc
{

    /**
     *
     * @param GenericRow $row
     */
    protected function calculateRowQuantity(GenericRow $row)
    {
        if ($row->hasErrors()) {
            return;
        }

        try {
            // $this->convertedDocQuantity = $this->getDocQuantity() * $this->getConversionFactor();
            // $this->convertedDocUnitPrice = $this->getDocUnitPrice() / $this->getConvertedDocQuantity();

            // actuallly converted doc quantity /price.
            $row->set = $row->getDocQuantity() * $row->getConversionFactor();
            $row->unitPrice = $row->getDocUnitPrice() / $row->getConversionFactor();

            $netAmount = $row->getDocUnitPrice() * $row->getDocQuantity();

            $discountAmount = 0;
            if ($row->getDiscountRate() > 0) {
                $discountAmount = $netAmount * ($row->getDiscountRate() / 100);
                $this->setDiscountAmount($discountAmount);
                $netAmount = $netAmount - $discountAmount;
            }

            $taxAmount = $netAmount * $row->getTaxRate() / 100;
            $grosAmount = $netAmount + $taxAmount;

            $row->setNetAmount($netAmount);
            $row->setTaxAmount($taxAmount);
            $row->setGrossAmount($grosAmount);

            $convertedPurchaseQuantity = $row->getDocQuantity();
            $convertedPurchaseUnitPrice = $row->getDocUnitPrice();

            $conversionFactor = $row->getConversionFactor();

            $standardCF = 1;

            if ($row->getStandardConvertFactor() > 0) {
                $standardCF = $this->getStandardConvertFactor();
            }

            $prRowConvertFactor = $row->getPrRowConvertFactor();

            if ($row->getPrRow() > 0) {
                $convertedPurchaseQuantity = $convertedPurchaseQuantity * $conversionFactor;
                $convertedPurchaseUnitPrice = $convertedPurchaseUnitPrice / $conversionFactor;
                $standardCF = $standardCF * $prRowConvertFactor;
            }

            // quantity /unit price is converted purchase quantity to clear PR

            // $entity->setQuantity($convertedPurchaseQuantity);
            // $entity->setUnitPrice($convertedPurchaseUnitPrice);

            $convertedStandardQuantity = $row->getQuantity();
            $convertedStandardUnitPrice = $row->getUnitPrice();

            if ($row->getItem() > 0) {
                $convertedStandardQuantity = $convertedStandardQuantity * $standardCF;
                $convertedStandardUnitPrice = $convertedStandardUnitPrice / $standardCF;
            }

            // calculate standard quantity
            $row->setConvertedPurchaseQuantity($convertedPurchaseQuantity);
            $this->setConvertedPurchaseUnitPrice($convertedPurchaseUnitPrice);

            $row->setConvertedStandardQuantity($convertedStandardQuantity);
            $row->setConvertedStandardUnitPrice($convertedStandardUnitPrice);
        } catch (\Exception $e) {
            $row->addError($e->getMessage());
        }
    }

    public function updateIdentityFrom($snapshot)
    {
        if (! $snapshot instanceof DocSnapshot) {
            return;
        }

        $this->setId($snapshot->getId());
        $this->setRevisionNo($snapshot->getRevisionNo());
        $this->setDocVersion($snapshot->getDocVersion());
        if ($this->getSysNumber() == Constants::SYS_NUMBER_UNASSIGNED) {
            $this->setSysNumber($snapshot->getSysNumber());
        }
    }

    /**
     *
     * @param Closure $sort
     */
    public function sortRowsBy(callable $sort)
    {
        if (! $sort instanceof \Closure) {
            return;
        }

        $docRows = $this->getDocRows();

        if ($docRows == null) {
            return;
        }

        if ($this->getDocRowsCount() == 1) {
            return;
        }

        \usort($docRows, $sort);

        $this->setDocRows($docRows);
    }

    /**
     *
     * @return NULL|\Procure\Domain\GenericRow[][]|array[]
     */
    public function splitRowsByWarehouse()
    {
        if ($this->getDocRowsCount() == 0) {
            return null;
        }

        $this->sortRowsByWarehouse(); // sort first.
        $docRows = $this->getDocRows();

        $results = [];

        /**
         *
         * @var GenericRow $fistRow
         */

        $fistRow = $docRows[0];
        $wh = $fistRow->getWarehouse();
        $rowsOfWarehouse = [];
        $n = 0;
        foreach ($docRows as $row) {

            /**
             *
             * @var GenericRow $row
             */
            $n ++;

            if ($row->getWarehouse() == $wh) {
                $rowsOfWarehouse[] = $row;
            } else {
                $results[$wh] = $rowsOfWarehouse;

                $wh = $row->getWarehouse();
                $rowsOfWarehouse = [];
                $rowsOfWarehouse[] = $row;
            }

            if ($n == $this->getDocRowsCount()) {
                $results[$wh] = $rowsOfWarehouse;
            }
        }

        return $results;
    }

    public function sortRowsByWarehouse()
    {
        $sort = function ($row1, $row2) {

            if (! $row1 instanceof GenericRow) {
                throw new \RuntimeException("Invalid procure row");
            }

            if (! $row2 instanceof GenericRow) {
                throw new \RuntimeException("Invalid procure row");
            }

            $wh1 = $row1->getWarehouse();
            $wh2 = $row2->getWarehouse();

            if ($wh1 == $wh2) {
                return 0;
            }
            return ($wh1 < $wh2) ? - 1 : 1;
        };

        $this->sortRowsBy($sort);
    }

    /**
     *
     * @param int $id
     * @return NULL|\Procure\Domain\AbstractRow
     */
    public function getRowbyId($id)
    {
        if ($id == null || $this->getDocRows() == null) {
            return null;
        }
        $rows = $this->getDocRows();

        foreach ($rows as $r) {

            /**
             *
             * @var AbstractRow $r ;
             */
            if ($r->getId() == $id) {
                return $r;
            }
        }

        return null;
    }

    /**
     *
     * @param int $id
     * @param string $token
     * @return NULL|\Procure\Domain\AbstractRow
     */
    public function getRowbyTokenId($id, $token)
    {
        $rows = $this->getDocRows();

        if ($id == null || $token == null || $rows == null) {
            return null;
        }

        foreach ($rows as $r) {

            /**
             *
             * @var AbstractRow $r ;
             */

            if ($r->getId() == $id && $r->getToken() == $token) {
                return $r;
            }
        }

        return null;
    }

    /**
     *
     * @param AbstractRow $row
     * @throws InvalidArgumentException
     */
    public function addRow(AbstractRow $row)
    {
        if (! $row instanceof AbstractRow) {
            throw new InvalidArgumentException("input not invalid! AbstractRow");
        }
        $rows = $this->getDocRows();
        $rows[] = $row;
        $this->docRows = $rows;
    }

    /**
     *
     * @param int $id
     * @return boolean
     */
    public function hasRowId($id)
    {
        if ($this->getRowIdArray() == null) {
            return false;
        }

        return in_array($id, $this->getRowIdArray());
    }

    /**
     *
     * @param AbstractDoc $targetObj
     * @throws InvalidArgumentException
     * @return \Procure\Domain\AbstractDoc
     */
    public function convertTo(AbstractDoc $targetObj)
    {
        if (! $targetObj instanceof AbstractDoc) {
            throw new InvalidArgumentException("Convertion input invalid!");
        }

        // Converting
        // ==========================
        $exculdedProps = [
            "id",
            "uuid",
            "token",
            "docRows",
            "rowIdArray",
            "instance",
            "sysNumber",
            "createdBy",
            "lastchangeBy",
            "docNumber",
            "docDate",
            "reversalDoc"
        ];

        $sourceObj = $this;
        $reflectionClass = new \ReflectionClass(get_class($sourceObj));
        $props = $reflectionClass->getProperties();

        foreach ($props as $prop) {

            $prop->setAccessible(true);
            $propName = $prop->getName();

            if (\in_array($propName, $exculdedProps)) {
                continue;
            }

            if (property_exists($targetObj, $propName)) {
                $targetObj->$propName = $prop->getValue($sourceObj);
            }
        }
        return $targetObj;
    }

    public function convertAllTo(AbstractDoc $targetObj)
    {
        if (! $targetObj instanceof AbstractDoc) {
            throw new InvalidArgumentException("Convertion input invalid!");
        }

        // Converting
        // ==========================

        $exculdedProps = [
            "rowIdArray",
            "instance"
        ];

        $sourceObj = $this;
        $reflectionClass = new \ReflectionClass(get_class($sourceObj));
        $props = $reflectionClass->getProperties();

        foreach ($props as $prop) {

            $prop->setAccessible(true);
            $propName = $prop->getName();

            if (\in_array($propName, $exculdedProps)) {
                continue;
            }

            if (property_exists($targetObj, $propName)) {
                $targetObj->$propName = $prop->getValue($sourceObj);
            }
        }
        return $targetObj;
    }

    protected function refresh()
    {}

    /**
     *
     * @param int $createdBy
     * @param string $createdDate
     */
    protected function initDoc($createdBy, $createdDate)
    {
        $this->setCreatedOn($createdDate);
        $this->setCreatedBy($createdBy);
        $this->setDocStatus(ProcureDocStatus::DRAFT);

        $this->setIsActive(1);
        $this->setIsDraft(1);
        $this->setIsPosted(0);

        $this->setSysNumber(Constants::SYS_NUMBER_UNASSIGNED);
        $this->setRevisionNo(0);
        $this->setDocVersion(0);
        $this->setUuid(Uuid::uuid4()->toString());
        $this->setToken($this->getUuid());
    }

    /**
     *
     * @param int $postedBy
     * @param string $postedDate
     */
    protected function markAsPosted($postedBy, $postedDate)
    {
        $this->setLastchangeOn($postedDate);
        $this->setLastchangeBy($postedBy);

        $this->setIsPosted(1);
        $this->setIsDraft(0);
        $this->setIsActive(1);
        $this->setDocStatus(ProcureDocStatus::POSTED);
    }

    /**
     *
     * @param int $postedBy
     * @param string $postedDate
     */
    protected function markAsReversed($postedBy, $postedDate)
    {
        $this->setLastchangeOn($postedDate);
        $this->setReversalDate($postedDate);
        $this->setIsReversed(1);
        $this->setIsDraft(0);
        $this->setIsPosted(0);
        $this->setIsActive(1);
        $this->setDocStatus(ProcureDocStatus::REVERSED);
        $this->setLastchangeBy($postedBy);
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
        return SnapshotAssembler::createSnapshotFrom($this, new DocSnapshot());
    }
}