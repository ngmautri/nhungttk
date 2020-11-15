<?php
namespace Procure\Domain\AccountPayable\Factory;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Procure\Domain\AccountPayable\APDoc;
use Procure\Domain\AccountPayable\APReversal;
use Procure\Domain\AccountPayable\APSnapshot;
use Procure\Domain\AccountPayable\Validator\ValidatorFactory;
use Procure\Domain\Contracts\ProcureDocType;
use Procure\Domain\Event\Ap\ApHeaderCreated;
use Procure\Domain\Event\Ap\ApHeaderUpdated;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\Service\SharedService;
use Ramsey\Uuid\Uuid;
use Procure\Domain\AccountPayable\APFromPO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class APFactory
{

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
     * @param APSnapshot $snapshot
     * @return NULL|NULL|\Procure\Domain\AccountPayable\APReversal
     */
    public static function constructFromDB(APSnapshot $snapshot)
    {
        if (! $snapshot instanceof APSnapshot) {
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
     * @param APSnapshot $snapshot
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @return NULL|NULL|\Procure\Domain\AccountPayable\GenericAP
     */
    public static function createFrom(APSnapshot $snapshot, CommandOptions $options, SharedService $sharedService)
    {
        if (! $snapshot instanceof APSnapshot) {
            return null;
        }

        if ($options == null) {
            throw new \InvalidArgumentException("Options is null");
        }

        $docType = $snapshot->getDocType();
        $instance = self::createDoc($docType);

        if ($instance == null) {
            throw new \RuntimeException(\sprintf("Could not created document %s", $docType));
        }

        $fxRate = $sharedService->getFxService()->checkAndReturnFX($snapshot->getDocCurrency(), $snapshot->getLocalCurrency(), $snapshot->getExchangeRate());
        $snapshot->setExchangeRate($fxRate);

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $snapshot->init($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);

        $validationService = ValidatorFactory::createForHeader($sharedService);
        $instance->validateHeader($validationService->getHeaderValidators());

        if ($instance->hasErrors()) {
            throw new \RuntimeException($instance->getNotification()->errorMessage());
        }

        $instance->clearEvents();

        /**
         *
         * @var APSnapshot $rootSnapshot
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rootSnapshot = $rep->storeHeader($instance, false);

        if ($rootSnapshot == null) {
            throw new \RuntimeException(sprintf("Error orcured when creating AP #%s", $instance->getId()));
        }

        $instance->updateIdentityFrom($rootSnapshot);

        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetToken($rootSnapshot->getToken());
        $defaultParams->setTargetDocVersion($rootSnapshot->getDocVersion());
        $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new ApHeaderCreated($target, $defaultParams, $params);
        $instance->addEvent($event);

        return $instance;
    }

    /**
     *
     * @param APSnapshot $snapshot
     * @param CommandOptions $options
     * @param array $params
     * @param SharedService $sharedService
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @return NULL|NULL|\Procure\Domain\AccountPayable\APReversal
     */
    public static function updateFrom(APSnapshot $snapshot, CommandOptions $options, $params, SharedService $sharedService)
    {
        if (! $snapshot instanceof APSnapshot) {
            return null;
        }

        if ($options == null) {
            throw new \InvalidArgumentException("Opptions is null");
        }

        $docType = $snapshot->getDocType();
        $instance = self::createDoc($docType);
        $fxRate = $sharedService->getFxService()->checkAndReturnFX($snapshot->getDocCurrency(), $snapshot->getLocalCurrency(), $snapshot->getExchangeRate());
        $snapshot->setExchangeRate($fxRate);

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $snapshot->markAsChange($createdBy, $createdDate);

        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);
        $validationService = ValidatorFactory::createForHeader($sharedService);

        $instance->validateHeader($validationService->getHeaderValidators());

        if ($instance->hasErrors()) {
            throw new \RuntimeException(sprintf("%s-%s", $instance->getNotification()->errorMessage(), __FUNCTION__));
        }

        $instance->clearEvents();

        /**
         *
         * @var APSnapshot $rootSnapshot
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rootSnapshot = $rep->storeHeader($instance, false);

        if ($rootSnapshot == null) {
            throw new \RuntimeException(sprintf("%s-%s", "Error orcured when creating AP!", __FUNCTION__));
        }

        $instance->updateIdentityFrom($rootSnapshot);

        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetToken($rootSnapshot->getToken());
        $defaultParams->setTargetDocVersion($rootSnapshot->getDocVersion());
        $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new ApHeaderUpdated($target, $defaultParams, $params);
        $instance->addEvent($event);

        return $instance;
    }

    /**
     *
     * @param APDoc $sourceObj
     * @param APSnapshot $snapshot
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @return \Procure\Domain\AccountPayable\APDoc
     */
    public static function createAndPostReversal(APDoc $sourceObj, APSnapshot $snapshot, CommandOptions $options, SharedService $sharedService)
    {
        return APReversal::createAndPostReversal($sourceObj, $snapshot, $options, $sharedService);
    }

    /**
     *
     * @param PODoc $sourceObj
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @return \Procure\Domain\AccountPayable\APDoc
     */
    public static function createFromPo(PODoc $sourceObj, CommandOptions $options, SharedService $sharedService)
    {
        return APFromPO::createFromPo($sourceObj, $options, $sharedService);
    }

    /**
     *
     * @param string $docType
     * @return NULL|\Procure\Domain\AccountPayable\GenericAP
     */
    private static function createDoc($docType)
    {
        $doc = null;

        switch ($docType) {

            // ============
            case ProcureDocType::INVOICE:
                $doc = APDoc::getInstance();
                break;
            case ProcureDocType::INVOICE_REVERSAL:
                $doc = APReversal::getInstance();
                break;
            case ProcureDocType::INVOICE_FROM_PO:
                $doc = APFromPO::getInstance();
                break;
        }
        return $doc;
    }
}