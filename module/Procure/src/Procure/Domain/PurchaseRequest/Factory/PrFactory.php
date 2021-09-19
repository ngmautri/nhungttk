<?php
namespace Procure\Domain\PurchaseRequest\Factory;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Procure\Domain\Contracts\ProcureDocStatus;
use Procure\Domain\Event\Pr\PrHeaderCreated;
use Procure\Domain\Event\Pr\PrHeaderUpdated;
use Procure\Domain\PurchaseRequest\GenericPR;
use Procure\Domain\PurchaseRequest\PRDoc;
use Procure\Domain\PurchaseRequest\PRSnapshot;
use Procure\Domain\PurchaseRequest\PRSnapshotAssembler;
use Procure\Domain\PurchaseRequest\Repository\PrCmdRepositoryInterface;
use Procure\Domain\PurchaseRequest\Validator\ValidatorFactory;
use Procure\Domain\Service\SharedService;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrFactory
{

    public static function createEmptyObject($docType)
    {
        $instance = self::createDoc($docType);
        $instance->specify(); // important
        return $instance;
    }

    /**
     *
     * @param PRSnapshot $snapshot
     * @return NULL|\Procure\Domain\PurchaseRequest\PRDoc
     */
    public static function constructFromDB(PRSnapshot $snapshot)
    {
        if (! $snapshot instanceof PRSnapshot) {
            return null;
        }
        if ($snapshot->uuid == null) {
            $snapshot->uuid = Uuid::uuid4()->toString();
            $snapshot->token = $snapshot->uuid;
        }

        $instance = self::createDoc();
        GenericObjectAssembler::updateAllFieldsFrom($instance, $snapshot);

        return $instance;
    }

    /**
     *
     * @param PRSnapshot $snapshot
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @throws \RuntimeException
     * @return \Procure\Domain\PurchaseRequest\PRDoc
     */
    public static function createFrom(PRSnapshot $snapshot, CommandOptions $options, SharedService $sharedService)
    {
        Assert::notNull($snapshot, "PR snapshot not found");
        Assert::notNull($options, "command options not found");
        Assert::notNull($sharedService, "SharedService service not found");

        $validationService = ValidatorFactory::create($sharedService);

        $snapshot->initDoc($options);

        $instance = self::createDoc();
        GenericObjectAssembler::updateAllFieldsFrom($instance, $snapshot);
        $instance->specify(); // Important

        $instance->validateHeader($validationService->getHeaderValidators());

        if ($instance->hasErrors()) {
            throw new \RuntimeException($instance->getNotification()->errorMessage());
        }

        $instance->clearEvents();

        /**
         *
         * @var PRSnapshot $rootSnapshot
         * @var PrCmdRepositoryInterface $rep ;
         */

        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rootSnapshot = $rep->storeHeader($instance);

        if ($rootSnapshot == null) {
            throw new \RuntimeException(sprintf("Error orcured when creating PR #%s", $instance->getId()));
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

        $event = new PrHeaderCreated($target, $defaultParams, $params);
        $instance->addEvent($event);

        return $instance;
    }

    /**
     *
     * @param GenericPR $rootEntity
     * @param PRSnapshot $snapshot
     * @param CommandOptions $options
     * @param array $params
     * @param SharedService $sharedService
     * @throws \RuntimeException
     * @return \Procure\Domain\PurchaseRequest\GenericPR
     */
    public static function updateFrom(GenericPR $rootEntity, PRSnapshot $snapshot, CommandOptions $options, $params, SharedService $sharedService)
    {
        Assert::notNull($rootEntity, sprintf("Root entity not found!"));
        Assert::notEq($rootEntity->getDocStatus(), ProcureDocStatus::POSTED, sprintf("PR is already posted! %s", $rootEntity->getId()));
        Assert::notNull($snapshot, "PR snapshot not found");
        Assert::notNull($options, "Command options not found");
        $validationService = ValidatorFactory::create($sharedService);

        $snapshot->prName = $snapshot->prNumber;

        PRSnapshotAssembler::updateDefaultExcludedFieldsFrom($rootEntity, $snapshot);

        $rootEntity->validateHeader($validationService->getHeaderValidators());

        if ($rootEntity->hasErrors()) {
            throw new \RuntimeException(sprintf("%s-%s", $rootEntity->getNotification()->errorMessage(), __FUNCTION__));
        }

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $rootEntity->markDocAsChanged($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        $rootEntity->clearEvents();

        /**
         *
         * @var PRSnapshot $rootSnapshot
         * @var PrCmdRepositoryInterface $rep ;
         */

        $rep = $sharedService->getPostingService()->getCmdRepository();

        $rootSnapshot = $rep->storeHeader($rootEntity, false);
        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetToken($rootSnapshot->getToken());
        $defaultParams->setTargetDocVersion($rootSnapshot->getDocVersion());
        $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new PrHeaderUpdated($target, $defaultParams, $params);
        $rootEntity->addEvent($event);
        return $rootEntity;
    }

    private static function createDoc()
    {
        $doc = PRDoc::getInstance();
        return $doc;
    }
}