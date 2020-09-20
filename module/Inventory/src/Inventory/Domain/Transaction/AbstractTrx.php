<?php
namespace Inventory\Domain\Transaction;

use Application\Domain\Shared\Constants;
use Procure\Domain\GenericDoc;
use Procure\Domain\Shared\ProcureDocStatus;
use Ramsey\Uuid\Uuid;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class AbstractTrx extends GenericDoc
{

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
}