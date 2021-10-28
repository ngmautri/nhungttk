<?php
namespace Procure\Domain\PurchaseOrder\Factory;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Procure\Domain\Contracts\ProcureDocType;
use Procure\Domain\Event\Po\PoHeaderCreated;
use Procure\Domain\Event\Po\PoHeaderUpdated;
use Procure\Domain\PurchaseOrder\GenericPO;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\PurchaseOrder\POSnapshot;
use Procure\Domain\PurchaseOrder\POSnapshotAssembler;
use Procure\Domain\PurchaseOrder\Repository\POCmdRepositoryInterface;
use Procure\Domain\PurchaseOrder\Validator\ValidatorFactory;
use Procure\Domain\Service\SharedService;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class POFactory
{

    public static function constructFromDB(POSnapshot $snapshot)
    {
        if (! $snapshot instanceof POSnapshot) {
            return null;
        }
        if ($snapshot->uuid == null) {
            $snapshot->uuid = Uuid::uuid4()->toString();
            $snapshot->token = $snapshot->uuid;
        }

        $instance = self::createDocFromDB();
        GenericObjectAssembler::updateAllFieldsFrom($instance, $snapshot);

        return $instance;
    }

    /**
     *
     * @param POSnapshot $snapshot
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @throws \RuntimeException
     * @return \Procure\Domain\PurchaseOrder\PODoc
     */
    public static function createFrom(POSnapshot $snapshot, CommandOptions $options, SharedService $sharedService)
    {
        Assert::notNull($snapshot, "PO snapshot not found");
        Assert::notNull($options, "command options not found");
        $validationService = ValidatorFactory::create($sharedService);
        Assert::notNull($validationService, "Validation can not created!");

        $snapshot->initDoc($options);
        $snapshot->docType = ProcureDocType::PO;
        $fxRate = $sharedService->getFxService()->checkAndReturnFX($snapshot->getDocCurrency(), $snapshot->getLocalCurrency(), $snapshot->getExchangeRate());
        $snapshot->setExchangeRate($fxRate);

        $instance = self::createDoc();
        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);

        $instance->validateHeader($validationService->getHeaderValidators());

        if ($instance->hasErrors()) {
            throw new \RuntimeException($instance->getNotification()->errorMessage());
        }

        $instance->clearEvents();

        /**
         *
         * @var POSnapshot $rootSnapshot
         * @var POCmdRepositoryInterface $rep ;
         */

        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rootSnapshot = $rep->storeHeader($instance, false);
        Assert::notNull($rootSnapshot, sprintf("Error occured when creating PO", $instance->getId()));

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

        $event = new PoHeaderCreated($target, $defaultParams, $params);
        $instance->addEvent($event);
        return $instance;
    }

    /**
     *
     * @param GenericPO $rootEntity
     * @param POSnapshot $snapshot
     * @param CommandOptions $options
     * @param array $params
     * @param SharedService $sharedService
     * @throws \RuntimeException
     * @return \Procure\Domain\PurchaseOrder\GenericPO
     */
    public static function updateFrom(GenericPO $rootEntity, POSnapshot $snapshot, CommandOptions $options, $params, SharedService $sharedService)
    {
        Assert::notNull($rootEntity, sprintf("Root entity not found!"));
        Assert::notNull($snapshot, "PO snapshot not found");
        Assert::notNull($options, "command options not found");
        $validationService = ValidatorFactory::create($sharedService);
        Assert::notNull($validationService, "Validation can not created!");

        $createdDate = new \Datetime();
        $snapshot->markAsChange($options->getUserId(), date_format($createdDate, 'Y-m-d H:i:s'));
        $snapshot->docType = ProcureDocType::PO;

        $fxRate = $sharedService->getFxService()->checkAndReturnFX($snapshot->getDocCurrency(), $snapshot->getLocalCurrency(), $snapshot->getExchangeRate());
        $snapshot->setExchangeRate($fxRate);

        POSnapshotAssembler::updateDefaultExcludedFieldsFrom($rootEntity, $snapshot);

        $rootEntity->validateHeader($validationService->getHeaderValidators());

        if ($rootEntity->hasErrors()) {
            throw new \RuntimeException($rootEntity->getNotification()->errorMessage());
        }

        $rootEntity->clearEvents();

        /**
         *
         * @var POSnapshot $rootSnapshot
         * @var POCmdRepositoryInterface $rep ;
         */

        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rootSnapshot = $rep->storeHeader($rootEntity, false);
        Assert::notNull($rootSnapshot, sprintf("Error occured when creating PO", $rootEntity->getId()));

        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetToken($rootSnapshot->getToken());
        $defaultParams->setTargetDocVersion($rootSnapshot->getDocVersion());
        $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new PoHeaderUpdated($target, $defaultParams, $params);
        $rootEntity->addEvent($event);

        $rootEntity->updateIdentityFrom($snapshot);
        return $rootEntity;
    }

    private static function createDoc()
    {
        $doc = PODoc::getInstance();
        return $doc;
    }

    private static function createDocFromDB()
    {
        $doc = PODoc::getInstanceFromDB();
        return $doc;
    }
}