<?php
namespace Procure\Domain\GoodsReceipt\Factory;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Inventory\Domain\Transaction\GenericTrx;
use Procure\Domain\AccountPayable\APDoc;
use Procure\Domain\AccountPayable\APFromPO;
use Procure\Domain\AccountPayable\GenericAP;
use Procure\Domain\Contracts\ProcureDocStatus;
use Procure\Domain\Contracts\ProcureDocType;
use Procure\Domain\Event\Gr\GrHeaderCreated;
use Procure\Domain\Event\Gr\GrHeaderUpdated;
use Procure\Domain\GoodsReceipt\GRDoc;
use Procure\Domain\GoodsReceipt\GRFromAP;
use Procure\Domain\GoodsReceipt\GRReturn;
use Procure\Domain\GoodsReceipt\GRReturnFromWHReturn;
use Procure\Domain\GoodsReceipt\GRReversal;
use Procure\Domain\GoodsReceipt\GRReversalFromAPReserval;
use Procure\Domain\GoodsReceipt\GRSnapshot;
use Procure\Domain\GoodsReceipt\GRSnapshotAssembler;
use Procure\Domain\GoodsReceipt\GenericGR;
use Procure\Domain\GoodsReceipt\Repository\GrCmdRepositoryInterface;
use Procure\Domain\GoodsReceipt\Validator\ValidatorFactory;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\Service\SharedService;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

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

        $docType = $snapshot->getDocType();
        $instance = self::createDoc($docType);
        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);

        $instance->specify(); // important
        return $instance;
    }

    /**
     *
     * @param string $docType
     * @throws \RuntimeException
     * @return NULL|\Procure\Domain\GoodsReceipt\GenericGR
     */
    public static function createEmptyObject($docType)
    {
        $instance = self::createDoc($docType);

        if ($instance == null) {
            throw new \RuntimeException(\sprintf("Could not created document %s", $docType));
        }

        $instance->specify(); // important
        return $instance;
    }

    /**
     *
     * @param GRSnapshot $snapshot
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @throws \RuntimeException
     * @return NULL|\Procure\Domain\GoodsReceipt\GenericGR
     */
    public static function createFrom(GRSnapshot $snapshot, CommandOptions $options, SharedService $sharedService)
    {
        Assert::notNull($snapshot, "GRSnapshot not found");
        Assert::notNull($options, "Command options not found");

        $snapshot->initDoc($options);
        $fxRate = $sharedService->getFxService()->checkAndReturnFX($snapshot->getDocCurrency(), $snapshot->getLocalCurrency(), $snapshot->getExchangeRate());
        $snapshot->setExchangeRate($fxRate);

        $docType = $snapshot->getDocType();
        $instance = self::createDoc($docType);

        GRSnapshotAssembler::updateEntityAllFieldsFrom($instance, $snapshot);

        // Important
        $instance->specify();

        $validationService = ValidatorFactory::create($docType, $sharedService);
        $instance->validateHeader($validationService->getHeaderValidators());

        if ($instance->hasErrors()) {
            throw new \RuntimeException($instance->getNotification()->errorMessage());
        }

        $instance->clearEvents();
        $instance->clearNotification();
        /**
         *
         * @var GRSnapshot $rootSnapshot
         * @var GrCmdRepositoryInterface $rep
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rootSnapshot = $rep->storeHeader($instance);

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

        $instance->updateIdentityFrom($snapshot);
        return $instance;
    }

    public static function updateFrom(GenericGR $rootEntity, GrSnapshot $snapshot, CommandOptions $options, $params, SharedService $sharedService)
    {
        Assert::notNull($rootEntity, sprintf("Root entity not found!"));
        Assert::notEq($rootEntity->getDocStatus(), ProcureDocStatus::POSTED, sprintf("GR is already posted! %s", $rootEntity->getId()));
        Assert::notNull($snapshot, "GR snapshot not found");
        Assert::notNull($options, "Command options not found");

        $fxRate = $sharedService->getFxService()->checkAndReturnFX($snapshot->getDocCurrency(), $snapshot->getLocalCurrency(), $snapshot->getExchangeRate());
        $snapshot->setExchangeRate($fxRate);

        $docType = $snapshot->getDocType();
        $instance = self::createDoc($docType);

        // SnapshotAssembler::makeFromSnapshot($instance, $snapshot);
        GRSnapshotAssembler::updateEntityExcludedDefaultFieldsFrom($rootEntity, $snapshot);

        // Important
        $instance->specify();

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $rootEntity->markDocAsChanged($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        $validationService = ValidatorFactory::create($docType, $sharedService);
        $instance->validateHeader($validationService->getHeaderValidators());

        if ($instance->hasErrors()) {
            throw new \RuntimeException($instance->getNotification()->errorMessage());
        }

        $instance->clearEvents();
        $instance->clearNotification();

        /**
         *
         * @var GRSnapshot $rootSnapshot
         */

        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rootSnapshot = $rep->storeHeader($instance);

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

        $event = new GrHeaderUpdated($target, $defaultParams, $params);

        $instance->addEvent($event);
        return $instance;
    }

    public static function createFromPo(PODoc $sourceObj, CommandOptions $options, SharedService $sharedService)
    {
        return APFromPO::createFromPo($sourceObj, $options, $sharedService);
    }

    /**
     *
     * @param GenericAP $sourceObj
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @return \Procure\Domain\GoodsReceipt\GRFromAP
     */
    public static function postCopyFromAP(GenericAP $sourceObj, CommandOptions $options, SharedService $sharedService)
    {
        return GRFromAP::postCopyFromAP($sourceObj, $options, $sharedService);
    }

    /**
     *
     * @param GenericAP $sourceObj
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @throws \RuntimeException
     * @return \Procure\Domain\GoodsReceipt\GRFromAP[]
     */
    public static function postCopyFromAPByWarehouse(GenericAP $sourceObj, CommandOptions $options, SharedService $sharedService)
    {
        $results = [];

        $docs = $sourceObj->generateDocumentByWarehouse();

        if ($docs == null) {
            throw new \RuntimeException(\sprintf("Can not create PO Goods Receipt from %s", $sourceObj->getId()));
        }

        foreach ($docs as $doc) {

            $gr = GRFromAP::postCopyFromAP($doc, $options, $sharedService);
            $results[] = $gr;
        }

        Assert::notNull($results, 'Cant not create PO GR by Warehouse!');
        return $results;
    }

    /**
     *
     * @param GenericAP $sourceObj
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @throws \RuntimeException
     * @return \Procure\Domain\GoodsReceipt\GRDoc[]
     */
    public static function postCopyFromAPReversalByWarehouse(GenericAP $sourceObj, CommandOptions $options, SharedService $sharedService)
    {
        $results = [];

        $docs = $sourceObj->generateDocumentByWarehouse();
        if ($docs == null) {
            throw new \RuntimeException(\sprintf("Can not create PO Goods Receipt Reversal from %s", $sourceObj->getId()));
        }

        foreach ($docs as $doc) {
            $gr = GRReversalFromAPReserval::postCopyFromAPReversal($doc, $options, $sharedService);
            $results[] = $gr;
        }

        Assert::notNull($results, 'Cant not create PO GR by Warehouse!');
        return $results;
    }

    /**
     *
     * @param APDoc $sourceObj
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @return \Procure\Domain\GoodsReceipt\GRDoc
     */
    public static function postCopyFromAPRerveral(APDoc $sourceObj, CommandOptions $options, SharedService $sharedService)
    {
        return GRReversalFromAPReserval::postCopyFromAPReversal($sourceObj, $options, $sharedService);
    }

    /**
     *
     * @param GenericTrx $sourceObj
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @return \Procure\Domain\GoodsReceipt\GRDoc
     */
    public static function postCopyFromWHReturn(GenericTrx $sourceObj, CommandOptions $options, SharedService $sharedService)
    {
        return GRReturnFromWHReturn::postCopyFromWHReturn($sourceObj, $options, $sharedService);
    }

    /**
     *
     * @param String $docType
     * @return NULL|\Procure\Domain\GoodsReceipt\GenericGR
     */
    private static function createDoc($docType)
    {
        $doc = null;

        switch ($docType) {

            // ============
            case ProcureDocType::GR:
                $doc = GRDoc::getInstance();
                break;
            case ProcureDocType::GR_FROM_INVOICE:
                $doc = GRFromAP::getInstance();
                break;

            case ProcureDocType::GR_REVERSAL:
                $doc = GRReversal::getInstance();
                break;

            case ProcureDocType::GR_REVERSAL_FROM_AP_RESERVAL:
                $doc = GRReversalFromAPReserval::getInstance();
                break;

            case ProcureDocType::GOODS_RETURN:
                $doc = GRReturn::getInstance();
                break;

            case ProcureDocType::GOODS_RETURN_FROM_WH_RETURN:
                $doc = GRReturnFromWHReturn::getInstance();
                break;
        }

        Assert::notNull($doc, 'Cant not create PO GR!');

        return $doc;
    }
}