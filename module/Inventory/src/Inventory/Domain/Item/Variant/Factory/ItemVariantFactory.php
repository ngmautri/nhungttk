<?php
namespace Inventory\Domain\Item\Variant\Factory;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Company\ItemAttribute\AttributeGroupSnapshot;
use Application\Domain\Company\ItemAttribute\AttributeGroupSnapshotAssembler;
use Application\Domain\Company\ItemAttribute\BaseAttributeGroup;
use Application\Domain\Company\ItemAttribute\BaseAttributeGroupSnapshot;
use Application\Domain\Company\ItemAttribute\GenericAttributeGroup;
use Application\Domain\Company\ItemAttribute\Validator\ItemAttributeValidatorFactory;
use Application\Domain\Company\Repository\CompanyCmdRepositoryInterface;
use Application\Domain\Event\Company\ItemAttribute\AttributeGroupCreated;
use Application\Domain\Event\Company\ItemAttribute\AttributeGroupUpdated;
use Application\Domain\Service\Contracts\SharedServiceInterface;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Inventory\Domain\Item\GenericItem;
use Inventory\Domain\Item\Repository\ItemCmdRepositoryInterface;
use Inventory\Domain\Item\Variant\GenericVariant;
use Inventory\Domain\Item\Variant\VariantAttributeSnapshot;
use Inventory\Domain\Item\Variant\VariantSnapshot;
use Inventory\Domain\Item\Variant\Validator\VariantValidatorFactory;
use Inventory\Domain\Service\SharedService;
use Webmozart\Assert\Assert;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemVariantFactory
{

    public static function contructFromDB(VariantSnapshot $snapshot)
    {
        if (! $snapshot instanceof VariantSnapshot) {
            throw new InvalidArgumentException("VariantSnapshot not found!");
        }

        $instance = new GenericVariant();
        GenericObjectAssembler::updateAllFieldsFrom($instance, $snapshot);
        $instance->createVOFromVariantCode();

        return $instance;
    }

    /**
     *
     * @param GenericItem $entity
     * @param array $attributes
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @return \Inventory\Domain\Item\Variant\GenericVariant
     */
    public static function generateVariantFrom(GenericItem $entity, $attributes, CommandOptions $options, SharedService $sharedService)
    {
        Assert::notNull($entity, "Item not found");
        Assert::notNull($attributes, "Attributes not given");
        Assert::notNull($sharedService, "SharedService service not found");

        $variantSnapshot = new VariantSnapshot();
        $variantSnapshot->item = $entity->getId();

        // create variant
        $variant = new GenericVariant();
        GenericObjectAssembler::updateAllFieldsFrom($variant, $variantSnapshot);
        $variant->createVariantCodeVO($entity->getId(), $attributes);

        // update current Variant Attribute
        // $variant->getLazyAttributeCollection();

        // create attribute
        foreach ($attributes as $a) {
            $snapshot = new VariantAttributeSnapshot();
            $snapshot->setAttribute($a);
            $snapshot->setVariant($variant->getId()); // might be no needed
            $variant->createAttributeFrom($snapshot, $options, $sharedService, FALSE);
        }
        // \var_dump($variant);
        return $variant;
    }

    public static function createFrom(GenericItem $entity, $attributes, CommandOptions $options, SharedServiceInterface $sharedService)
    {
        Assert::notNull($entity, "Item not found");
        Assert::notNull($attributes, "Attribute not found");
        Assert::notNull($sharedService, "SharedService service not found");

        $localEntity = new GenericAttributeGroup();
        $snapshot->init($options);
        GenericObjectAssembler::updateAllFieldsFrom($localEntity, $snapshot);

        $variantCollection = $entity->getLazyVariantCollection();

        if ($variantCollection->isExits($localEntity)) {
            throw new \InvalidArgumentException(\sprintf("Variant (%s) exits already!", $localEntity->getGroupName()));
        }

        $validationService = VariantValidatorFactory::forCreatingVariant($sharedService);

        // create default location.
        $localEntity->validateAttributeGroup($validationService);

        if ($localEntity->hasErrors()) {
            throw new \InvalidArgumentException($localEntity->getNotification()->errorMessage());
        }

        $localEntity->clearEvents();

        /**
         *
         * @var AttributeGroupSnapshot $localSnapshot ;
         * @var ItemCmdRepositoryInterface $rep ;
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $localSnapshot = $rep->storeWholeVariant($entity, $localEntity, true);

        $target = $localSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($localSnapshot->getId());
        $defaultParams->setTargetToken($localSnapshot->getToken());
        $defaultParams->setTargetRrevisionNo($localSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new AttributeGroupCreated($target, $defaultParams, $params);
        $localEntity->addEvent($event);
        return $localEntity;
    }

    /**
     *
     * @param BaseAttributeGroup $entity
     * @param BaseAttributeGroupSnapshot $snapshot
     * @param CommandOptions $options
     * @param array $params
     * @param SharedServiceInterface $sharedService
     * @throws \RuntimeException
     * @return \Application\Domain\Company\ItemAttribute\BaseAttributeGroup
     */
    public static function updateFrom(BaseAttributeGroup $entity, BaseAttributeGroupSnapshot $snapshot, CommandOptions $options, $params, SharedServiceInterface $sharedService)
    {
        Assert::notNull($entity, "BaseAttributeGroupSnapshot not found.");
        Assert::notNull($snapshot, "BaseAttributeGroupSnapshot not found!");
        Assert::notNull($options, "Cmd options not found!");
        Assert::notNull($options, "SharedService service not found!");

        AttributeGroupSnapshotAssembler::updateDefaultExcludedFieldsFrom($entity, $snapshot);

        $snapshot->update($options);
        $validationService = ItemAttributeValidatorFactory::forCreatingAttributeGroup($sharedService);

        $entity->validateAttributeGroup($validationService);

        if ($entity->hasErrors()) {
            throw new \RuntimeException($entity->getNotification()->errorMessage());
        }

        $entity->clearEvents();
        /**
         *
         * @var AttributeGroupSnapshot $rootSnapshot ;
         * @var CompanyCmdRepositoryInterface $rep ;
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rootSnapshot = $rep->store($entity, true);

        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetToken($rootSnapshot->getToken());
        $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());

        $event = new AttributeGroupUpdated($target, $defaultParams, $params);
        $entity->addEvent($event);

        return $entity;
    }
}