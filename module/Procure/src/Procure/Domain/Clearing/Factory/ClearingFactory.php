<?php
namespace Procure\Domain\Clearing\Factory;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Procure\Domain\AccountPayable\APDoc;
use Procure\Domain\AccountPayable\APFromPO;
use Procure\Domain\AccountPayable\APReversal;
use Procure\Domain\AccountPayable\APSnapshot;
use Procure\Domain\AccountPayable\APSnapshotAssembler;
use Procure\Domain\AccountPayable\GenericAP;
use Procure\Domain\AccountPayable\Repository\APCmdRepositoryInterface;
use Procure\Domain\AccountPayable\Validator\ValidatorFactory;
use Procure\Domain\Contracts\ProcureDocType;
use Procure\Domain\Event\Ap\ApHeaderCreated;
use Procure\Domain\Event\Ap\ApHeaderUpdated;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Shared\ProcureDocStatus;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ClearingFactory
{

    public static function createEmptyObject($docType)
    {
        $instance = self::createDoc($docType);
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
        GenericObjectAssembler::updateAllFieldsFrom($instance, $snapshot);

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
        Assert::notNull($snapshot, "AP snapshot not found");
        Assert::notNull($options, "Command options not found");

        $docType = $snapshot->getDocType();
        $instance = self::createDoc($docType);

        $snapshot->initDoc($options);

        $fxRate = $sharedService->getFxService()->checkAndReturnFX($snapshot->getDocCurrency(), $snapshot->getLocalCurrency(), $snapshot->getExchangeRate());
        $snapshot->setExchangeRate($fxRate);

        APSnapshotAssembler::updateEntityAllFieldsFrom($instance, $snapshot);

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

    public static function updateFrom(GenericAP $rootEntity, APSnapshot $snapshot, CommandOptions $options, $params, SharedService $sharedService)
    {
        Assert::notNull($rootEntity, sprintf("Root entity not found!"));
        Assert::notEq($rootEntity->getDocStatus(), ProcureDocStatus::POSTED, sprintf("AP is already posted! %s", $rootEntity->getId()));
        Assert::notNull($snapshot, "AP snapshot not found");
        Assert::notNull($options, "Command options not found");

        $fxRate = $sharedService->getFxService()->checkAndReturnFX($snapshot->getDocCurrency(), $snapshot->getLocalCurrency(), $snapshot->getExchangeRate());
        $snapshot->setExchangeRate($fxRate);

        // SnapshotAssembler::makeFromSnapshot($instance, $snapshot);
        APSnapshotAssembler::updateEntityExcludedDefaultFieldsFrom($rootEntity, $snapshot);

        $validationService = ValidatorFactory::createForHeader($sharedService);
        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $rootEntity->markDocAsChanged($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));
        $rootEntity->validateHeader($validationService->getHeaderValidators());

        if ($rootEntity->hasErrors()) {
            throw new \RuntimeException(sprintf("%s-%s", $rootEntity->getNotification()->errorMessage(), __FUNCTION__));
        }

        $rootEntity->clearEvents();

        /**
         *
         * @var APSnapshot $rootSnapshot
         * @var APCmdRepositoryInterface $rep ;
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rootSnapshot = $rep->storeHeader($rootEntity);

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
        $rootEntity->addEvent($event);

        return $rootEntity;
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
        Assert::notNull($doc);

        return $doc;
    }
}