<?php
namespace Procure\Domain;

use Application\Domain\Shared\SnapshotAssembler;
use Procure\Domain\Shared\Constants;
use Procure\Domain\Shared\ProcureDocStatus;
use Ramsey\Uuid\Uuid;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GenericDoc extends AbstractDoc
{

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
        $this->setDocStatus(ProcureDocStatus::DOC_STATUS_DRAFT);

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
        $this->setDocStatus(ProcureDocStatus::DOC_STATUS_POSTED);
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
        $this->setIsActive(1);
        $this->setDocStatus(ProcureDocStatus::DOC_STATUS_REVERSED);
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