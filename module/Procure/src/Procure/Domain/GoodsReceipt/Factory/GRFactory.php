<?php
namespace Procure\Domain\GoodsReceipt\Factory;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\Constants;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Procure\Domain\Event\Gr\GrHeaderCreated;
use Procure\Domain\Exception\OperationFailedException;
use Procure\Domain\GoodsReceipt\GRDoc;
use Procure\Domain\GoodsReceipt\GRRow;
use Procure\Domain\GoodsReceipt\GRSnapshot;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\PurchaseOrder\PODocStatus;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Service\Contracts\ValidationServiceInterface;
use Procure\Domain\Shared\ProcureDocStatus;
use Ramsey\Uuid\Uuid;
use Procure\Domain\Contracts\ProcureDocType;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRFactory
{

    /**
     * To get from Storage.
     *
     * @param GrSnapshot $snapshot
     * @return NULL|\Procure\Domain\GoodsReceipt\GRDoc
     */
    public static function constructFromDB(GrSnapshot $snapshot)
    {
        if (! $snapshot instanceof GrSnapshot) {
            return null;
        }
        if ($snapshot->uuid == null) {
            $snapshot->uuid = Uuid::uuid4()->toString();
            $snapshot->token = $snapshot->uuid;
        }

        $instance = GRDoc::getInstance();
        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);
        return $instance;
    }

    public static function createFrom(GRSnapshot $snapshot, CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService)
    {
        $instance = GRDoc::getInstance();
        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);

        $fxRate = $sharedService->getFxService()->checkAndReturnFX($snapshot->getDocCurrency(), $snapshot->getLocalCurrency(), $snapshot->getExchangeRate());
        $instance->setExchangeRate($fxRate);

        $instance->validateHeader($validationService->getHeaderValidators());

        if ($instance->hasErrors()) {
            throw new \RuntimeException($instance->getNotification()->errorMessage());
        }

        $createdDate = new \Datetime();
        $instance->setCreatedOn(date_format($createdDate, 'Y-m-d H:i:s'));
        $instance->setDocStatus(ProcureDocStatus::DOC_STATUS_DRAFT);
        $instance->setDocType(ProcureDocType::GR);
        $instance->setIsActive(1);
        $instance->setSysNumber(Constants::SYS_NUMBER_UNASSIGNED);
        $instance->setRevisionNo(1);
        $instance->setDocVersion(1);
        $instance->setUuid(Uuid::uuid4()->toString());
        $instance->setToken($instance->getUuid());

        $instance->recordedEvents = array();

        /**
         *
         * @var GRSnapshot $rootSnapshot
         */
        $rootSnapshot = $postingService->getCmdRepository()->storeHeader($instance, false);

        if ($rootSnapshot == null) {
            throw new \RuntimeException(sprintf("Error orcured when creating PO #%s", $instance->getId()));
        }

        $instance->id = $rootSnapshot->getId();

        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetToken($rootSnapshot->getToken());
        $defaultParams->setTargetDocVersion($rootSnapshot->getDocVersion());
        $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new GrHeaderCreated($target, $defaultParams, $params);
        $instance->addEvent($event);

        return $instance;
    }

    /**
     *
     * @param PODoc $po
     */
    public static function createFromPO(PODoc $po)
    {
        if (! $po instanceof PODoc) {
            throw new \InvalidArgumentException("PO Entity is required");
        }

        if ($po->getDocStatus() !== PODocStatus::DOC_STATUS_POSTED) {
            throw new \RuntimeException("PO document is not posted!");
        }

        $rows = $po->getDocRows();

        if ($po->getDocRows() == null) {
            throw new \InvalidArgumentException("PO Entity  is empty!");
        }

        $gr = GRDoc::createFromPo($po);
        echo "\n" . $gr->getVendorName();

        foreach ($rows as $r) {
            $grRow = GrRow::createFromPoRow($r);
            echo sprintf("\n %s, PoRowId %s, %s", $grRow->getItemName(), $grRow->getPoRow(), $grRow->getPrRow());
        }

        return $gr;
    }
}