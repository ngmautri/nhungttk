<?php
namespace HR\Domain\Employee\Factory;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use HR\Domain\Contracts\IndividualType;
use HR\Domain\Employee\BaseIndividual;
use HR\Domain\Employee\BaseIndividualSnapshot;
use HR\Domain\Employee\GenericApplicant;
use HR\Domain\Employee\GenericEmployee;
use HR\Domain\Employee\IndividualSnapshot;
use HR\Domain\Employee\Validator\ValidatorFactory;
use HR\Domain\Event\Employee\IndividualCreated;
use HR\Domain\Service\Contracts\SharedServiceInterface;
use HR\Infrastructure\Persistence\Domain\Doctrine\IndividualCmdRepositoryImpl;
use Inventory\Domain\Event\Item\ItemUpdated;
use Inventory\Domain\Item\GenericItem;
use Inventory\Domain\Item\ItemSnapshot;
use Inventory\Domain\Item\ItemSnapshotAssembler;
use Webmozart\Assert\Assert;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class LabourContractFactory
{

    /**
     *
     * @param IndividualSnapshot $snapshot
     * @throws InvalidArgumentException
     * @return NULL|\HR\Domain\Employee\GenericEmployee
     */
    public static function contructFromDB(IndividualSnapshot $snapshot)
    {
        if (! $snapshot instanceof IndividualSnapshot) {
            throw new InvalidArgumentException("IndividualSnapshot not found!");
        }

        $itemTypeId = $snapshot->getIndividualType();
        $instance = LabourContractFactory::_createIndvidual($itemTypeId);

        GenericObjectAssembler::updateAllFieldsFrom($instance, $snapshot);
        $instance->specify();
        $instance->createVO();

        return $instance;
    }

    /**
     *
     * @param BaseIndividualSnapshot $snapshot
     * @param CommandOptions $options
     * @param SharedServiceInterface $sharedService
     * @throws \InvalidArgumentException
     * @return \HR\Domain\Employee\BaseIndividual
     */
    public static function createFrom(BaseIndividualSnapshot $snapshot, CommandOptions $options, SharedServiceInterface $sharedService)
    {
        Assert::notNull($snapshot, "IndividualSnapshot not found");
        Assert::notNull($sharedService, "SharedService service not found");

        $individualTypeId = $snapshot->getIndividualType();
        $individual = LabourContractFactory::_createIndvidual($individualTypeId);

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $snapshot->init($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        /**
         *
         * @var BaseIndividual $item
         */
        GenericObjectAssembler::updateAllFieldsFrom($individual, $snapshot);

        $individual->specify(); // important
        $individual->createVO();

        if ($individual->hasErrors()) {
            throw new \InvalidArgumentException($individual->getNotification()->errorMessage());
        }

        $validators = ValidatorFactory::create($individualTypeId, $sharedService);
        $individual->validate($validators);

        if ($individual->hasErrors()) {
            throw new \InvalidArgumentException($individual->getNotification()->errorMessage());
        }

        $individual->clearEvents();

        /**
         *
         * @var IndividualCmdRepositoryImpl $rep
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rootSnapshot = $rep->store($individual);

        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetToken($rootSnapshot->getToken());
        $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new IndividualCreated($target, $defaultParams, $params);
        $individual->addEvent($event);
        return $individual;
    }

    public static function updateFrom(BaseIndividual $rootEntity, IndividualSnapshot $snapshot, CommandOptions $options, $params, SharedServiceInterface $sharedService)
    {
        Assert::notNull($rootEntity, "BaseIndividual not found.");
        Assert::notNull($snapshot, "IndividualSnapshot not found!");
        Assert::notNull($options, "Cmd options not found!");
        Assert::notNull($options, "SharedService service not found!");

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $snapshot->updateDoc($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        /**
         *
         * @var GenericItem $item
         */
        ItemSnapshotAssembler::updateEntityExcludedDefaultFieldsFrom($rootEntity, $snapshot);
        $rootEntity->specifyItem();

        $validators = ValidatorFactory::create($rootEntity->getItemTypeId(), $sharedService);
        $rootEntity->validate($validators);

        if ($rootEntity->hasErrors()) {
            throw new \RuntimeException($rootEntity->getNotification()->errorMessage());
        }

        $rootEntity->clearEvents();

        /**
         *
         * @var ItemSnapshot $rootSnapshot
         */
        $rootSnapshot = $sharedService->getPostingService()
            ->getCmdRepository()
            ->store($rootEntity, false);

        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetToken($rootSnapshot->getToken());
        $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());

        $event = new ItemUpdated($target, $defaultParams, $params);
        $rootEntity->addEvent($event);

        return $rootEntity;
    }

    private static function _createIndvidual($individualTypeId)
    {
        $individual = null;

        if (! \in_array($individualTypeId, IndividualType::getSupportedType())) {
            throw new InvalidArgumentException("Individual type empty or not supported! #" . $individualTypeId);
        }

        switch ($individualTypeId) {

            case IndividualType::APPLICANT:
                $individual = new GenericApplicant();
                break;

            case IndividualType::EMPLOYEE:
                $individual = new GenericEmployee();
                break;
        }

        Assert::notNull($individual, 'Can not create individual!');
        return $individual;
    }
}