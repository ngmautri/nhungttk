<?php
namespace Application\Domain\Company\Brand\Factory;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Company\BaseCompany;
use Application\Domain\Company\Brand\BaseBrand;
use Application\Domain\Company\Brand\BaseBrandSnapshot;
use Application\Domain\Company\Brand\BrandSnapshot;
use Application\Domain\Company\Brand\GenericBrand;
use Application\Domain\Company\Brand\Repository\BrandCmdRepositoryInterface;
use Application\Domain\Company\Brand\Validator\BrandValidatorFactory;
use Application\Domain\Company\ItemAttribute\AttributeGroupSnapshot;
use Application\Domain\Company\ItemAttribute\AttributeGroupSnapshotAssembler;
use Application\Domain\Company\Repository\CompanyCmdRepositoryInterface;
use Application\Domain\Event\Company\Brand\BrandCreated;
use Application\Domain\Event\Company\Brand\BrandRemoved;
use Application\Domain\Event\Company\Brand\BrandUpdated;
use Application\Domain\Service\Contracts\SharedServiceInterface;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Webmozart\Assert\Assert;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BrandFactory
{

    /**
     *
     * @param BrandSnapshot $snapshot
     * @throws InvalidArgumentException
     * @return \Application\Domain\Company\Brand\GenericBrand
     */
    public static function contructFromDB(BrandSnapshot $snapshot)
    {
        if (! $snapshot instanceof BrandSnapshot) {
            throw new InvalidArgumentException("AttributeGroupSnapshot not found!");
        }

        $instance = new GenericBrand();
        GenericObjectAssembler::updateAllFieldsFrom($instance, $snapshot);
        return $instance;
    }

    /**
     *
     * @param BaseCompany $companyEntity
     * @param BrandSnapshot $snapshot
     * @param CommandOptions $options
     * @param SharedServiceInterface $sharedService
     * @throws \InvalidArgumentException
     * @return \Application\Domain\Company\Brand\GenericBrand
     */
    public static function createFrom(BaseCompany $companyEntity, BaseBrandSnapshot $snapshot, CommandOptions $options, SharedServiceInterface $sharedService)
    {
        Assert::notNull($companyEntity, "Company not found");
        Assert::notNull($snapshot, "BaseBrandSnapshot not found");
        Assert::notNull($sharedService, "SharedService service not found");

        $localEntity = new GenericBrand();
        $snapshot->init($options);
        GenericObjectAssembler::updateAllFieldsFrom($localEntity, $snapshot);

        $collection = $companyEntity->getLazyBrandCollection();

        if ($collection->isExits($localEntity)) {
            throw new \InvalidArgumentException(\sprintf("Brand (%s) exits already!", $localEntity->getBrandName()));
        }

        $validationService = BrandValidatorFactory::forCreatingBrand($sharedService);

        // create default location.
        $localEntity->validateBrand($validationService);

        if ($localEntity->hasErrors()) {
            throw new \InvalidArgumentException($localEntity->getNotification()->errorMessage());
        }

        $localEntity->clearEvents();

        /**
         *
         * @var BrandSnapshot $localSnapshot ;
         * @var CompanyCmdRepositoryInterface $rep ;
         * @var BrandCmdRepositoryInterface $rep1 ;
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rep1 = $rep->getBrandCmdRepository();
        $localSnapshot = $rep1->storeBrand($companyEntity, $localEntity);

        $target = $localSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($localSnapshot->getId());
        $defaultParams->setTargetToken($localSnapshot->getUuid());
        $defaultParams->setTargetRrevisionNo($localSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new BrandCreated($target, $defaultParams, $params);
        $localEntity->addEvent($event);
        return $localEntity;
    }

    public static function updateFrom(BaseCompany $companyEntity, BaseBrand $entity, BaseBrandSnapshot $snapshot, CommandOptions $options, $params, SharedServiceInterface $sharedService)
    {
        Assert::notNull($entity, "BaseBrand not found.");
        Assert::notNull($snapshot, "BaseBrandSnapshot not found!");
        Assert::notNull($options, "Cmd options not found!");
        Assert::notNull($options, "SharedService service not found!");

        AttributeGroupSnapshotAssembler::updateDefaultExcludedFieldsFrom($entity, $snapshot);

        $snapshot->update($options);
        $validationService = BrandValidatorFactory::forCreatingBrand($sharedService);

        $entity->validateBrand($validationService);

        if ($entity->hasErrors()) {
            throw new \RuntimeException($entity->getNotification()->errorMessage());
        }

        $entity->clearEvents();
        /**
         *
         * @var AttributeGroupSnapshot $rootSnapshot ;
         * @var CompanyCmdRepositoryInterface $rep ;
         * @var BrandCmdRepositoryInterface $rep1 ;
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rep1 = $rep->getBrandCmdRepository();
        $rootSnapshot = $rep1->storeBrand($companyEntity, $entity);

        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());

        $event = new BrandUpdated($target, $defaultParams, $params);
        $entity->addEvent($event);

        return $entity;
    }

    public static function remove(BaseCompany $companyEntity, BaseBrand $entity, CommandOptions $options, SharedServiceInterface $sharedService)
    {
        Assert::notNull($entity, "BaseBrand not found.");
        Assert::notNull($options, "Cmd options not found!");
        Assert::notNull($options, "SharedService service not found!");

        /**
         *
         * @var BrandSnapshot $localSnapshot ;
         * @var CompanyCmdRepositoryInterface $rep ;
         * @var BrandCmdRepositoryInterface $rep1 ;
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rep1 = $rep->getBrandCmdRepository();
        $rep1->removeBrand($companyEntity, $entity);

        $params = [
            "rowId" => $entity->getId(),
            "rowToken" => $entity->getToken()
        ];

        $target = $entity->makeSnapshot();
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($entity->getId());
        $defaultParams->setTargetToken($entity->getToken());
        $defaultParams->setTargetRrevisionNo($entity->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());

        $event = new BrandRemoved($target, $defaultParams, $params);
        $entity->addEvent($event);

        return $entity;
    }
}