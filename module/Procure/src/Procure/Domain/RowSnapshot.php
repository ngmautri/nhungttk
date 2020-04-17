<?php
namespace Procure\Domain;

use Procure\Domain\Shared\ProcureDocStatus;
use Ramsey\Uuid\Uuid;

/**
 * Row Snapshot
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class RowSnapshot extends BaseRowSnapshot
{

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
