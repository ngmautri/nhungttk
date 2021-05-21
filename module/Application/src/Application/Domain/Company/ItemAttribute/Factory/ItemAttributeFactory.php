<?php
namespace Application\Domain\Company\ItemAttribute\Factory;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Company\BaseCompany;
use Application\Domain\Company\CompanySnapshot;
use Application\Domain\Company\AccountChart\BaseChart;
use Application\Domain\Company\AccountChart\ChartSnapshot;
use Application\Domain\Company\AccountChart\ChartSnapshotAssembler;
use Application\Domain\Company\AccountChart\Validator\ChartValidatorFactory;
use Application\Domain\Company\ItemAttribute\AttributeGroupSnapshot;
use Application\Domain\Company\ItemAttribute\BaseAttributeGroupSnapshot;
use Application\Domain\Company\ItemAttribute\GenericAttributeGroup;
use Application\Domain\Company\Repository\CompanyCmdRepositoryInterface;
use Application\Domain\Event\Company\CompanyUpdated;
use Application\Domain\Event\Company\AccountChart\ChartCreated;
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
class ItemAttributeFactory
{

    /**
     *
     * @param AttributeGroupSnapshot $snapshot
     * @throws InvalidArgumentException
     * @return \Application\Domain\Company\ItemAttribute\GenericAttributeGroup
     */
    public static function contructFromDB(AttributeGroupSnapshot $snapshot)
    {
        if (! $snapshot instanceof AttributeGroupSnapshot) {
            throw new InvalidArgumentException("AttributeGroupSnapshot not found!");
        }

        $instance = new GenericAttributeGroup();
        GenericObjectAssembler::updateAllFieldsFrom($instance, $snapshot);
        return $instance;
    }

    /**
     *
     * @param BaseCompany $companyEntity
     * @param BaseAttributeGroupSnapshot $snapshot
     * @param CommandOptions $options
     * @param SharedServiceInterface $sharedService
     * @throws \InvalidArgumentException
     * @return \Application\Domain\Company\AccountChart\GenericChart
     */
    public static function createFrom(BaseCompany $companyEntity, BaseAttributeGroupSnapshot $snapshot, CommandOptions $options, SharedServiceInterface $sharedService)
    {
        Assert::notNull($companyEntity, "Company not found");
        Assert::notNull($snapshot, "BaseChartSnapshot not found");
        Assert::notNull($sharedService, "SharedService service not found");

        $localEntity = new GenericAttributeGroup();
        $snapshot->init($options);
        GenericObjectAssembler::updateAllFieldsFrom($localEntity, $snapshot);

        $chartCollection = $companyEntity->getLazyAccountChartCollection();

        if ($chartCollection->isExits($localEntity)) {
            throw new \InvalidArgumentException(\sprintf("Attribute Group (%s) exits already!", $localEntity->getGroupName()));
        }

        $validationService = ChartValidatorFactory::forCreatingChart($sharedService);

        // create default location.
        $localEntity->validateChart($validationService);

        if ($localEntity->hasErrors()) {
            throw new \InvalidArgumentException($localEntity->getNotification()->errorMessage());
        }

        $localEntity->clearEvents();

        /**
         *
         * @var ChartSnapshot $localSnapshot ;
         * @var CompanyCmdRepositoryInterface $rep ;
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $localSnapshot = $rep->storeAccountChart($companyEntity, $localEntity, true);

        $target = $localSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($localSnapshot->getId());
        $defaultParams->setTargetToken($localSnapshot->getToken());
        $defaultParams->setTargetRrevisionNo($localSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new ChartCreated($target, $defaultParams, $params);
        $localEntity->addEvent($event);
        return $localEntity;
    }

    public static function updateFrom(BaseChart $companyEntity, ChartSnapshot $snapshot, CommandOptions $options, $params, SharedServiceInterface $sharedService)
    {
        Assert::notNull($companyEntity, "BaseChart not found.");
        Assert::notNull($snapshot, "ChartSnapshot not found!");
        Assert::notNull($options, "Cmd options not found!");
        Assert::notNull($options, "SharedService service not found!");

        ChartSnapshotAssembler::updateEntityExcludedDefaultFieldsFrom($companyEntity, $snapshot);

        $snapshot->update($options);
        $validationService = ChartValidatorFactory::create($sharedService);
        $companyEntity->validate($validationService);

        if ($companyEntity->hasErrors()) {
            throw new \RuntimeException($companyEntity->getNotification()->errorMessage());
        }

        $companyEntity->clearEvents();
        /**
         *
         * @var CompanySnapshot $rootSnapshot ;
         * @var CompanyCmdRepositoryInterface $rep ;
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rootSnapshot = $rep->store($companyEntity, true);

        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetToken($rootSnapshot->getToken());
        $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());

        $event = new CompanyUpdated($target, $defaultParams, $params);
        $companyEntity->addEvent($event);

        return $companyEntity;
    }
}