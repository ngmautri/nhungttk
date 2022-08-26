<?php
namespace Application\Domain\Attachment\Factory;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Attachment\AttachmentSnapshot;
use Application\Domain\Attachment\BaseAttachmentSnapshot;
use Application\Domain\Attachment\GenericAttachment;
use Application\Domain\Attachment\Repository\AttachmentCmdRepositoryInterface;
use Application\Domain\Attachment\Validator\AttachmentValidatorFactory;
use Application\Domain\Company\BaseCompany;
use Application\Domain\Company\Brand\BaseBrand;
use Application\Domain\Company\Brand\BaseBrandSnapshot;
use Application\Domain\Company\Brand\BrandSnapshot;
use Application\Domain\Company\Brand\Repository\BrandCmdRepositoryInterface;
use Application\Domain\Company\Brand\Validator\BrandValidatorFactory;
use Application\Domain\Company\ItemAttribute\AttributeGroupSnapshot;
use Application\Domain\Company\ItemAttribute\AttributeGroupSnapshotAssembler;
use Application\Domain\Company\Repository\CompanyCmdRepositoryInterface;
use Application\Domain\Event\Attachment\AttachmentHeaderCreated;
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
class AttachmentFactory
{

    public static function contructFromDB(AttachmentSnapshot $snapshot)
    {
        if (! $snapshot instanceof AttachmentSnapshot) {
            throw new InvalidArgumentException("AttachmentSnapshot not found!");
        }

        $instance = new GenericAttachment();
        GenericObjectAssembler::updateAllFieldsFrom($instance, $snapshot);
        return $instance;
    }

    public static function createFrom(BaseAttachmentSnapshot $snapshot, CommandOptions $options, SharedServiceInterface $sharedService)
    {
        Assert::notNull($snapshot, "AttachmentSnapshot not found");
        Assert::notNull($sharedService, "SharedService service not found");

        $rootEntity = new GenericAttachment();
        $snapshot->init($options);
        GenericObjectAssembler::updateAllFieldsFrom($rootEntity, $snapshot);

        $validationService = AttachmentValidatorFactory::forCreatingAttachment($sharedService);

        // create default location.
        $rootEntity->validateAttachment($validationService);

        if ($rootEntity->hasErrors()) {
            throw new \InvalidArgumentException($rootEntity->getNotification()->errorMessage());
        }

        $rootEntity->clearEvents();

        /**
         *
         * @var AttachmentSnapshot $localSnapshot ;
         * @var AttachmentCmdRepositoryInterface $rep ;
         *     
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rootSnapshot = $rep->storeAttachmentHeader($rootEntity);

        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetToken($rootSnapshot->getUuid());
        // $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new AttachmentHeaderCreated($target, $defaultParams, $params);
        $rootEntity->addEvent($event);
        return $rootEntity;
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