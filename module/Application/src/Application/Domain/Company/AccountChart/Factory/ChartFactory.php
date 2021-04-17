<?php
namespace Application\Domain\Company\AccountChart\Factory;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Company\BaseCompany;
use Application\Domain\Company\BaseCompanySnapshot;
use Application\Domain\Company\CompanySnapshot;
use Application\Domain\Company\CompanySnapshotAssembler;
use Application\Domain\Company\GenericCompany;
use Application\Domain\Company\Repository\CompanyCmdRepositoryInterface;
use Application\Domain\Company\Validator\ValidatorFactory;
use Application\Domain\Event\Company\CompanyCreated;
use Application\Domain\Event\Company\CompanyUpdated;
use Application\Domain\Service\SharedService;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Webmozart\Assert\Assert;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ChartFactory
{

    /**
     *
     * @param CompanySnapshot $snapshot
     * @throws InvalidArgumentException
     * @return \Application\Domain\Company\GenericCompany
     */
    public static function contructFromDB(CompanySnapshot $snapshot)
    {
        if (! $snapshot instanceof CompanySnapshot) {
            throw new InvalidArgumentException("CompanySnapshot not found!");
        }

        $instance = new GenericCompany();
        GenericObjectAssembler::updateAllFieldsFrom($instance, $snapshot);
        return $instance;
    }

    /**
     *
     * @param CompanySnapshot $snapshot
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @throws \RuntimeException
     * @return \Application\Domain\Company\GenericCompany
     */
    public static function createFrom(BaseCompanySnapshot $snapshot, CommandOptions $options = null, SharedService $sharedService = null)
    {
        Assert::notNull($snapshot, "CompanySnapshot not found");
        Assert::notNull($sharedService, "SharedService service not found");

        $snapshot->init($options);

        $company = new GenericCompany();
        GenericObjectAssembler::updateAllFieldsFrom($company, $snapshot);

        $validationService = ValidatorFactory::create($sharedService);

        // create default location.
        $company->validate($validationService);

        if ($company->hasErrors()) {
            throw new \RuntimeException($company->getNotification()->errorMessage());
        }

        $company->clearEvents();

        /**
         *
         * @var CompanySnapshot $rootSnapshot ;
         * @var CompanyCmdRepositoryInterface $rep ;
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rootSnapshot = $rep->storeDeparment($company, true);

        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetToken($rootSnapshot->getToken());
        $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new CompanyCreated($target, $defaultParams, $params);
        $company->addEvent($event);
        return $company;
    }

    public static function updateFrom(BaseCompany $rootEntity, BaseCompanySnapshot $snapshot, CommandOptions $options, $params, SharedService $sharedService)
    {
        Assert::notNull($rootEntity, "BaseCompany not found.");
        Assert::notNull($snapshot, "CompanySnapshot not found!");
        Assert::notNull($options, "Cmd options not found!");
        Assert::notNull($options, "SharedService service not found!");

        CompanySnapshotAssembler::updateEntityExcludedDefaultFieldsFrom($rootEntity, $snapshot);

        $snapshot->update($options);
        $validationService = ValidatorFactory::create($sharedService);
        $rootEntity->validate($validationService);

        if ($rootEntity->hasErrors()) {
            throw new \RuntimeException($rootEntity->getNotification()->errorMessage());
        }

        $rootEntity->clearEvents();
        /**
         *
         * @var CompanySnapshot $rootSnapshot ;
         * @var CompanyCmdRepositoryInterface $rep ;
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rootSnapshot = $rep->store($rootEntity, true);

        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetToken($rootSnapshot->getToken());
        $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());

        $event = new CompanyUpdated($target, $defaultParams, $params);
        $rootEntity->addEvent($event);

        return $rootEntity;
    }
}