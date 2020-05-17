<?php
namespace Procure\Domain;

use Procure\Domain\Shared\ProcureDocStatus;
use Ramsey\Uuid\Uuid;
use InvalidArgumentException;

/**
 * Row Snapshot
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class RowSnapshot extends BaseRowSnapshot
{

    public function convertTo(RowSnapshot $targetObj)
    {
        if (! $targetObj instanceof RowSnapshot) {
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
    public function initSnapshot($createdBy, $createdDate)
    {
        $this->createdOn = $createdDate;
        $this->createdBy = $createdBy;

        $this->quantity = $this->docQuantity;
        $this->revisionNo = 0;

        if ($this->token == null) {
            $this->token = Uuid::uuid4()->toString();
            $this->uuid = $this->getToken();
        }

        $this->docStatus = ProcureDocStatus::DOC_STATUS_DRAFT;
        $this->isActive = 1;
        $this->isDraft = 1;
        $this->unitPrice = $this->getDocUnitPrice();
        $this->unit = $this->getDocUnit();
    }

    /**
     *
     * @param int $createdBy
     * @param string $createdDate
     */
    public function updateSnapshot($createdBy, $createdDate)
    {
        $this->createdOn = $createdDate;
        $this->createdBy = $createdBy;

        $this->quantity = $this->docQuantity;
        $this->revisionNo = $this->getRevisionNo() + 1;

        if ($this->token == null) {
            $this->token = Uuid::uuid4()->toString();
            $this->uuid = $this->getToken();
        }
        $this->unitPrice = $this->getDocUnitPrice();
        $this->unit = $this->getDocUnit();
    }
}
