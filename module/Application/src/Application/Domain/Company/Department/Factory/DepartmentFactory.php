<?php
namespace Application\Domain\Company\Department\Factory;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Company\BaseCompany;
use Application\Domain\Company\Contracts\CompanyStatus;
use Application\Domain\Company\Department\DepartmentSnapshot;
use Application\Domain\Company\Department\GenericDepartment;
use Application\Domain\Company\Department\Validator\DepartmentValidatorFactory;
use Application\Domain\Company\Repository\CompanyCmdRepositoryInterface;
use Application\Domain\Event\Company\DepartmentSaved;
use Application\Domain\Service\Contracts\SharedServiceInterface;
use Application\Domain\Shared\Command\CommandOptions;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class DepartmentFactory
{

    /**
     *
     * @param BaseCompany $rootEntity
     * @param DepartmentSnapshot $snapshot
     * @param CommandOptions $options
     * @param SharedServiceInterface $sharedService
     * @param boolean $storeNow
     * @throws \RuntimeException
     * @return \Application\Domain\Company\BaseCompany|\Application\Domain\Company\Department\DepartmentSnapshot
     */
    static public function createFrom(BaseCompany $rootEntity, DepartmentSnapshot $snapshot, CommandOptions $options, SharedServiceInterface $sharedService, $storeNow = true)
    {
        Assert::notEq($rootEntity->getStatus(), CompanyStatus::INACTIVE, sprintf("Company inactive! %s", $rootEntity->getId()));
        Assert::notNull($snapshot, "DepartmentSnapshot not founds");
        Assert::notNull($options, "Options not founds");

        $validators = DepartmentValidatorFactory::create($sharedService);
        $snapshot->setCompany($options->getCompanyVO()
            ->getId());

        $createdDate = new \Datetime();
        $snapshot->setCreatedOn(date_format($createdDate, 'Y-m-d H:i:s'));
        $snapshot->setCreatedBy($options->getUserId());

        $localEntity = GenericDepartment::validateAndCreateFromSnapshot($rootEntity, $snapshot, $validators, true);

        if ($localEntity->hasErrors()) {
            throw new \RuntimeException($localEntity->getNotification()->errorMessage());
        }

        $rootEntity->clearEvents();

        if (! $storeNow) {
            return $rootEntity;
        }

        /**
         *
         * @var DepartmentSnapshot $localSnapshot ;
         * @var CompanyCmdRepositoryInterface $rep ;
         *
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $localSnapshot = $rep->storeDeparment($rootEntity, $snapshot);

        $params = [
            "rowId" => $localSnapshot->getNodeId(),
            "rowToken" => $localSnapshot->getToken()
        ];

        $target = $rootEntity->createValueObject();
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootEntity->getId());
        $defaultParams->setTargetToken($rootEntity->getToken());
        // $defaultParams->setTargetDocVersion($rootEntity->getDocVersion());
        $defaultParams->setTargetRrevisionNo($rootEntity->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());

        $event = new DepartmentSaved($target, $defaultParams, $params);
        $rootEntity->addEvent($event);

        return $localSnapshot;
    }

    /**
     *
     * @param BaseCompany $rootEntity
     * @param DepartmentSnapshot $snapshot
     * @param CommandOptions $options
     * @param SharedServiceInterface $sharedService
     * @return \Application\Domain\Company\BaseCompany
     */
    static public function remove(BaseCompany $rootEntity, DepartmentSnapshot $snapshot, CommandOptions $options, SharedServiceInterface $sharedService)
    {
        Assert::notEq($rootEntity->getStatus(), CompanyStatus::INACTIVE, sprintf("Company inactive! %s", $rootEntity->getId()));
        Assert::notNull($snapshot, "Row Snapshot not founds");
        Assert::notNull($options, "Options not founds");

        /**
         *
         * @var DepartmentSnapshot $localSnapshot ;
         * @var CompanyCmdRepositoryInterface $rep ;
         *
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rep->removeDepartment($rootEntity, $snapshot);

        return $rootEntity;
    }
}