<?php
namespace Procure\Domain;

use Application\Domain\Shared\Command\CommandOptions;
use Inventory\Domain\Item\ItemSnapshot;
use Inventory\Domain\Item\Repository\ItemQueryRepositoryInterface;
use Inventory\Domain\Service\Contracts\SharedQueryServiceInterface as InventoryQueryService;
use Procure\Domain\PurchaseRequest\PRSnapshot;
use Procure\Domain\PurchaseRequest\Repository\PrQueryRepositoryInterface;
use Procure\Domain\Service\Contracts\SharedQueryServiceInterface as ProcureQueryService;
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

    /**
     *
     * @param ProcureQueryService $queryService
     * @throws \InvalidArgumentException
     */
    public function updateProcureRef(ProcureQueryService $queryService)
    {
        if ($queryService instanceof ProcureQueryService) {
            throw new \InvalidArgumentException();
        }

        if ($this->getPrRow() != null && $this->getPr() == null) {

            /**
             *
             * @var PrQueryRepositoryInterface $rep
             */
            $rep = $queryService->getPRQueryRepository();
            if ($rep != null) {
                $prSnapshot = $rep->getHeaderSnapshotByRowId($this->getPrRow());
                if ($prSnapshot instanceof PRSnapshot) {
                    // alway using warehouse of PR request.
                    $this->warehouse = $prSnapshot->getWarehouse();
                }
            }
        }

        if ($this->getPoRow() != null && $this->getPo() == null) {

            /**
             *
             * @var PrQueryRepositoryInterface $rep
             */
            $rep = $queryService->getPOQueryRepository();
            if ($rep != null) {
                $this->po = $rep->getHeaderIdByRowId($this->getPoRow());
            }
        }
    }

    /**
     *
     * @param InventoryQueryService $queryService
     * @throws \InvalidArgumentException
     */
    public function updateInventoryRef(InventoryQueryService $queryService)
    {
        if ($queryService instanceof InventoryQueryService) {
            throw new \InvalidArgumentException();
        }

        if ($this->getItem() != null) {

            /**
             *
             * @var ItemQueryRepositoryInterface $rep
             */
            $rep = $queryService->getItemQueryRepository();
            if ($rep != null) {
                $itemSnapshot = $rep->getItemSnapshotById($this->getItem());
                if ($itemSnapshot instanceof ItemSnapshot) {
                    $this->isFixedAsset = $itemSnapshot->getIsFixedAsset();
                    $this->isInventoryItem = $itemSnapshot->getIsStocked();
                }
            }
        }
    }

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

        $this->docStatus = ProcureDocStatus::DRAFT;
        $this->isActive = 1;
        $this->isDraft = 1;
        $this->unitPrice = $this->getDocUnitPrice();
        $this->unit = $this->getDocUnit();
    }

    /**
     *
     * @param CommandOptions $options
     */
    public function initRow(CommandOptions $options)
    {
        $createdDate = new \DateTime();
        $this->createdOn = date_format($createdDate, 'Y-m-d H:i:s');
        $this->createdBy = $options->getUserId();

        $this->quantity = $this->docQuantity;
        $this->revisionNo = 0;

        if ($this->token == null) {
            $this->token = Uuid::uuid4()->toString();
            $this->uuid = $this->getToken();
        }

        $this->docStatus = ProcureDocStatus::DRAFT;
        $this->isActive = 1;
        $this->isDraft = 1;
        $this->unitPrice = $this->getDocUnitPrice();
        $this->unit = $this->getDocUnit();
    }

    public function markAsChange($createdBy, $createdDate)
    {
        $this->lastchangeOns = $createdDate;
        $this->lastchangeBy = $createdBy;

        $this->quantity = $this->docQuantity;
        $this->revisionNo = $this->getRevisionNo() + 1;

        if ($this->token == null) {
            $this->token = Uuid::uuid4()->toString();
            $this->uuid = $this->getToken();
        }
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
