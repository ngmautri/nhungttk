<?php
namespace Procure\Domain\GoodsReceipt\Factory;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\Constants;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Inventory\Domain\Transaction\GenericTrx;
use Procure\Domain\AccountPayable\APDoc;
use Procure\Domain\AccountPayable\GenericAP;
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
use Procure\Domain\GoodsReceipt\Repository\GrCmdRepositoryInterface;
use Procure\Domain\GoodsReceipt\Validator\ValidatorFactory;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Shared\ProcureDocStatus;
use Ramsey\Uuid\Uuid;

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
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @return NULL|\Procure\Domain\GoodsReceipt\GenericGR
     */
    public static function createFrom(GRSnapshot $snapshot, CommandOptions $options, SharedService $sharedService)
    {
        if (! $snapshot instanceof GRSnapshot) {
            throw new \InvalidArgumentException("GRSnapshot not found!");
        }

        $docType = $snapshot->getDocType();
        $instance = self::createDoc($docType);

        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);

        $fxRate = $sharedService->getFxService()->checkAndReturnFX($snapshot->getDocCurrency(), $snapshot->getLocalCurrency(), $snapshot->getExchangeRate());
        $instance->setExchangeRate($fxRate);

        // Important
        $instance->specify();

        $validationService = ValidatorFactory::create($docType, $sharedService);
        $instance->validateHeader($validationService->getHeaderValidators());

        if ($instance->hasErrors()) {
            throw new \RuntimeException($instance->getNotification()->errorMessage());
        }

        $createdDate = new \Datetime();
        $instance->setCreatedOn(date_format($createdDate, 'Y-m-d H:i:s'));
        $instance->setDocStatus(ProcureDocStatus::DRAFT);
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
         * @var GrCmdRepositoryInterface $rep
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rootSnapshot = $rep->storeHeader($instance);

        if ($rootSnapshot == null) {
            throw new \RuntimeException(sprintf("Error orcured when creating Goods Receipt #%s", $instance->getId()));
        }

        $instance->updateIdentityFrom($snapshot);

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

    public static function updateFrom(GrSnapshot $snapshot, CommandOptions $options, $params, SharedService $sharedService)
    {
        $docType = $snapshot->getDocType();
        $instance = self::createDoc($docType);

        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);

        $fxRate = $sharedService->getFxService()->checkAndReturnFX($snapshot->getDocCurrency(), $snapshot->getLocalCurrency(), $snapshot->getExchangeRate());
        $instance->setExchangeRate($fxRate);

        // Important
        $instance->specify();

        $validationService = ValidatorFactory::create($docType, $sharedService);
        $instance->validateHeader($validationService->getHeaderValidators());

        if ($instance->hasErrors()) {
            throw new \RuntimeException($instance->getNotification()->errorMessage());
        }

        $createdDate = new \Datetime();
        $instance->setLastchangeOn(date_format($createdDate, 'Y-m-d H:i:s'));

        $instance->recordedEvents = array();

        /**
         *
         * @var GRSnapshot $rootSnapshot
         */

        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rootSnapshot = $rep->storeHeader($instance);

        if ($rootSnapshot == null) {
            throw new \RuntimeException(sprintf("Error orcured when creating GR #%s", $instance->getId()));
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

        $event = new GrHeaderUpdated($target, $defaultParams, $params);

        $instance->addEvent($event);
        return $instance;
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
        return $doc;
    }
}