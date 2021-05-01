<?php
namespace Inventory\Domain\Warehouse\Factory;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Company\BaseCompany;
use Application\Domain\Company\Repository\CompanyCmdRepositoryInterface;
use Application\Domain\Service\Contracts\SharedServiceInterface;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Application\Domain\Util\Translator;
use Inventory\Domain\Event\Warehouse\WhCreated;
use Inventory\Domain\Event\Warehouse\WhUpdated;
use Inventory\Domain\Item\GenericItem;
use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Warehouse\BaseWarehouse;
use Inventory\Domain\Warehouse\BaseWarehouseSnapshot;
use Inventory\Domain\Warehouse\GenericWarehouse;
use Inventory\Domain\Warehouse\WarehouseSnapshot;
use Inventory\Domain\Warehouse\WarehouseSnapshotAssembler;
use Inventory\Domain\Warehouse\Contracts\DefaultLocation;
use Inventory\Domain\Warehouse\Location\GenericLocation;
use Inventory\Domain\Warehouse\Location\LocationSnapshot;
use Inventory\Domain\Warehouse\Validator\ValidatorFactory;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class WarehouseFactory
{

    /**
     *
     * @param WarehouseSnapshot $snapshot
     * @throws InvalidArgumentException
     * @return \Inventory\Domain\Warehouse\GenericWarehouse
     */
    public static function contructFromDB(WarehouseSnapshot $snapshot)
    {
        if (! $snapshot instanceof WarehouseSnapshot) {
            throw new InvalidArgumentException("WarehouseSnapshot not found!");
        }

        $instance = new GenericWarehouse();
        GenericObjectAssembler::updateAllFieldsFrom($instance, $snapshot);
        return $instance;
    }

    /**
     *
     * @param BaseCompany $companyEntity
     * @param BaseWarehouseSnapshot $snapshot
     * @param CommandOptions $options
     * @param SharedServiceInterface $sharedService
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @return \Inventory\Domain\Warehouse\GenericWarehouse
     */
    public static function createFrom(BaseCompany $companyEntity, BaseWarehouseSnapshot $snapshot, CommandOptions $options, SharedServiceInterface $sharedService)
    {
        Assert::notNull($companyEntity, "Company not found");
        Assert::notNull($snapshot, "BaseChartSnapshot not found");
        Assert::notNull($sharedService, "SharedService service not found");

        $createdDate = new \DateTime();
        $userId = $options->getUserId();
        $snapshot->init($userId, date_format($createdDate, 'Y-m-d H:i:s'));
        $snapshot->setCompany($companyEntity->getId()); // Important

        $wh = new GenericWarehouse();
        GenericObjectAssembler::updateAllFieldsFrom($wh, $snapshot);

        $collection = $companyEntity->getLazyWarehouseCollection();

        if ($collection->isExits($wh)) {
            throw new \InvalidArgumentException(\sprintf("Warehouse (%s) exits already!", $wh->getWhCode()));
        }

        // root location

        $location = new LocationSnapshot();
        $location->setUuid(Uuid::uuid4()->toString());
        $location->setIsActive(1);
        $location->setLocationName(DefaultLocation::ROOT_LOCATION);
        $location->setLocationCode(DefaultLocation::ROOT_LOCATION);
        $location->setCreatedBy($userId);
        $location->setCreatedOn(date_format($createdDate, 'Y-m-d H:i:s'));
        $wh->addLocation(GenericLocation::createFromSnapshot($wh, $location));

        // recycle location
        $location = new LocationSnapshot();
        $location->setUuid(Uuid::uuid4()->toString());
        $location->setLocationName(DefaultLocation::RECYCLE_LOCATION);
        $location->setLocationCode(DefaultLocation::RECYCLE_LOCATION);
        $location->setIsActive(1);
        $location->setCreatedBy($userId);
        $location->setCreatedOn(date_format($createdDate, 'Y-m-d H:i:s'));
        $wh->addLocation(GenericLocation::createFromSnapshot($wh, $location));

        // scrap location
        $location = new LocationSnapshot();
        $location->setUuid(Uuid::uuid4()->toString());
        $location->setLocationName(DefaultLocation::SCRAP_LOCATION);
        $location->setLocationCode(DefaultLocation::SCRAP_LOCATION);
        $location->setIsActive(1);
        $location->setCreatedBy($userId);
        $location->setCreatedOn(date_format($createdDate, 'Y-m-d H:i:s'));
        $wh->addLocation(GenericLocation::createFromSnapshot($wh, $location));

        $validationService = ValidatorFactory::create($sharedService, ValidatorFactory::CREATE_NEW_WH);

        // create default location.
        $wh->validate($validationService);

        if ($wh->hasErrors()) {
            throw new \RuntimeException($wh->getNotification()->errorMessage());
        }

        $wh->clearEvents();

        /**
         *
         * @var WarehouseSnapshot $rootSnapshot ;
         * @var CompanyCmdRepositoryInterface $rep ;
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rootSnapshot = $rep->storeWholeWarehouse($companyEntity, $wh);

        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetToken($rootSnapshot->getToken());
        $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new WhCreated($target, $defaultParams, $params);
        $wh->addEvent($event);
        return $wh;
    }

    /**
     *
     * @param BaseWarehouse $rootEntity
     * @param WarehouseSnapshot $snapshot
     * @param CommandOptions $options
     * @param array $params
     * @param SharedService $sharedService
     * @throws InvalidArgumentException
     * @throws \RuntimeException
     * @return \Inventory\Domain\Warehouse\BaseWarehouse
     */
    public static function updateFrom(BaseCompany $companyEntity, BaseWarehouse $rootEntity, BaseWarehouseSnapshot $snapshot, CommandOptions $options, $params, SharedServiceInterface $sharedService)
    {
        Assert::notNull($companyEntity, "Company not found");
        Assert::notNull($rootEntity, "BaseWarehouse not found");
        Assert::notNull($snapshot, "WarehouseSnapshot not found");
        Assert::notNull($sharedService, "SharedService service not found");

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $snapshot->markAsUpdate($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        if ($rootEntity->getIsDefault()) {
            throw new \InvalidArgumentException(Translator::translate("Default Warehouse can not be changed!"));
        }

        $newRootEntity = clone ($rootEntity);

        /**
         *
         * @var GenericItem $item
         */
        WarehouseSnapshotAssembler::updateDefaultExcludedFieldsFrom($newRootEntity, $snapshot);

        // check when code changed,

        if (! $newRootEntity->equals($rootEntity)) {
            $collection = $companyEntity->getLazyWarehouseCollection();

            if ($collection->isExits($newRootEntity)) {
                throw new \InvalidArgumentException(\sprintf("Warehouse code (%s) exits already!", $newRootEntity->getWhCode()));
            }
        }

        $validationService = ValidatorFactory::create($sharedService, ValidatorFactory::EDIT_WH);
        $newRootEntity->validateWarehouse($validationService->getWarehouseValidators());

        if ($newRootEntity->hasErrors()) {
            throw new \RuntimeException($newRootEntity->getNotification()->errorMessage());
        }

        $newRootEntity->clearEvents();

        /**
         *
         * @var CompanyCmdRepositoryInterface $rep ;
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rootSnapshot = $rep->storeWarehouse($companyEntity, $newRootEntity);

        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetToken($rootSnapshot->getToken());
        $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());

        $event = new WhUpdated($target, $defaultParams, $params);
        $rootEntity->addEvent($event);

        return $rootEntity;
    }
}