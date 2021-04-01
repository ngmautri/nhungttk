<?php
namespace Application\Domain\Company;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Company\Contracts\CompanyStatus;
use Application\Domain\Company\Department\BaseDepartment;
use Application\Domain\Company\Department\BaseDepartmentSnapshot;
use Application\Domain\Company\Department\DepartmentSnapshot;
use Application\Domain\Company\Department\GenericDepartment;
use Application\Domain\Company\Repository\CompanyCmdRepositoryInterface;
use Application\Domain\Company\Validator\ValidatorFactory;
use Application\Domain\Event\Company\DepartmentCreated;
use Application\Domain\Service\Contracts\SharedServiceInterface;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Procure\Domain\AccountPayable\APRowSnapshot;
use Webmozart\Assert\Assert;
use Application\Domain\Company\Validator\Contracts\DepartmentValidatorCollection;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BaseCompany extends AbstractCompany
{

    protected $warehouses;

    protected $departments;

    protected $postingPeriods;

    public function createValueObject()
    {
        $vo = new CompanyVO();
        GenericObjectAssembler::updateAllFieldsFrom($vo, $this);
        return $vo;
    }

    public function createDepartmentFrom(BaseDepartmentSnapshot $snapshot, CommandOptions $options, SharedServiceInterface $sharedService, $storeNow = true)
    {
        Assert::notEq($this->getStatus(), CompanyStatus::INACTIVE, sprintf("Company inactive! %s", $this->getId()));
        Assert::notNull($snapshot, "Row Snapshot not founds");
        Assert::notNull($options, "Options not founds");

        $validators = ValidatorFactory::createForDepartment($sharedService);
        $snapshot->init($options);

        $department = GenericDepartment::createFromSnapshot($this, $snapshot);
        $this->validateDepartment($department, $validators);

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getNotification()->errorMessage());
        }

        $this->clearEvents();

        if (! $storeNow) {
            return $this;
        }

        /**
         *
         * @var DepartmentSnapshot $localSnapshot ;
         * @var CompanyCmdRepositoryInterface $rep ;
         *
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $localSnapshot = $rep->storeDeparment($this, $department);

        $params = [
            "rowId" => $localSnapshot->getId(),
            "rowToken" => $localSnapshot->getToken()
        ];

        $target = $this->makeSnapshot();
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        $defaultParams->setTargetDocVersion($this->getDocVersion());
        $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());

        $event = new DepartmentCreated($target, $defaultParams, $params);
        $this->addEvent($event);

        return $localSnapshot;
    }

    protected function validateDepartment(BaseDepartment $department, DepartmentValidatorCollection $validators, $isPosting = false)
    {
        Assert::isInstanceOf($department, BaseDepartment::class, "BaseDepartment not given!");

        $validators->validate($this);

        if ($department->hasErrors()) {
            $this->addErrorArray($department->getErrors());
            return;
        }
    }
}
