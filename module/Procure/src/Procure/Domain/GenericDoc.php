<?php
namespace Procure\Domain;

use Application\Domain\Shared\SnapshotAssembler;
use Procure\Domain\Shared\Constants;
use Procure\Domain\Shared\ProcureDocStatus;
use Ramsey\Uuid\Uuid;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GenericDoc extends BaseDoc
{

    public function updateIdentityFrom($snapshot)
    {
        if (! $snapshot instanceof DocSnapshot) {
            return;
        }

        $this->setId($snapshot->getId());
        $this->setRevisionNo($snapshot->getRevisionNo());
        $this->setDocVersion($snapshot->getDocVersion());
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
        if ($id == null || $token == null || count($this->getDocRows()) == 0) {
            return null;
        }

        $rows = $this->getDocRows();

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
            if (property_exists($targetObj, $propName) && ! in_array($propName, $exculdedProps)) {
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
        $this->setIsPosted(0);
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