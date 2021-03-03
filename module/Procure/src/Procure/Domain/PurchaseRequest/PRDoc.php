<?php
namespace Procure\Domain\PurchaseRequest;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\Constants;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Procure\Domain\Contracts\ProcureDocStatus;
use Procure\Domain\Contracts\ProcureDocType;
use Procure\Domain\Event\Pr\PrHeaderCreated;
use Procure\Domain\Event\Pr\PrHeaderUpdated;
use Procure\Domain\Exception\ValidationFailedException;
use Procure\Domain\PurchaseRequest\Repository\PrCmdRepositoryInterface;
use Procure\Domain\PurchaseRequest\Validator\ValidatorFactory;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Service\Contracts\SharedServiceInterface;
use Procure\Domain\Service\Contracts\ValidationServiceInterface;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
final class PRDoc extends GenericPR
{

    private static $instance = null;

    private function __construct()
    {}

    protected function cloneDoc(CommandOptions $options)
    {
        $createdDate = new \DateTime();
        $this->setCreatedOn(date_format($createdDate, 'Y-m-d H:i:s'));
        $this->setSubmittedOn(date_format($createdDate, 'Y-m-d'));

        $this->setCreatedBy($options->getUserId());
        $this->setDocStatus(ProcureDocStatus::DRAFT);

        $this->setIsActive(1);
        $this->setIsDraft(1);
        $this->setIsPosted(0);

        $this->setSysNumber(Constants::SYS_NUMBER_UNASSIGNED);
        $this->setRevisionNo(0);
        $this->setDocVersion(0);
        $this->setUuid(Uuid::uuid4()->toString());
        $this->setToken($this->getUuid());

        $c = $options->getCompanyVO();
        $this->company = $c->getId();
        $this->setCurrency($this->getDocCurrency());
        $this->setLocalCurrency($c->getDefaultCurrency());
        $this->prName = \sprintf("%s (copied)", $this->prName);
        $this->prNumber = \sprintf("%s (copied)", $this->prNumber);
    }

    public function cloneAndSave(CommandOptions $options, SharedService $sharedService)
    {
        $rows = $this->getDocRows();
        Assert::notNull($rows, "PR Entity is empty!");
        $validationService = ValidatorFactory::create($sharedService);
        Assert::notNull($validationService, "Validation can not created!");

        /**
         *
         * @var \Procure\Domain\PurchaseRequest\PRDoc $instance
         */
        $instance = new self();

        $exculdedProps = [
            "id",
            "uuid",
            "token",
            "docRows",
            "rowIdArray",
            "instance",
            "prAutoNumber"
        ];
        $instance->prAutoNumber;
        $instance = $this->convertExcludeFieldsTo($instance, $exculdedProps);

        // overwrite.
        $instance->cloneDoc($options);
        $instance->setDocType(ProcureDocType::PR);
        $instance->setBaseDocId($this->getId());
        $instance->setBaseDocType($this->getDocType());

        $instance->validateHeader($validationService->getHeaderValidators());

        foreach ($rows as $r) {

            $localEntity = PRRow::cloneFrom($instance, $r, $options);
            $instance->addRow($localEntity);
            $instance->validateRow($localEntity, $validationService->getRowValidators());
        }

        if ($instance->hasErrors()) {
            throw new \RuntimeException($instance->getErrorMessage());
        }

        $this->clearEvents();
        /**
         *
         * @var PRRowSnapshot $localSnapshot
         * @var PRCmdRepositoryInterface $rep ;
         */

        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rootSnapshot = $rep->store($instance);

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
        $this->addEvent($event);
        return $instance;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GenericDoc::makeSnapshot()
     */
    public function makeSnapshot()
    {
        return GenericObjectAssembler::updateAllFieldsFrom(new PRSnapshot(), $this);
    }

    public function makeDetailsSnapshot()
    {
        $snapshot = new PRSnapshot();
        $snapshot = SnapshotAssembler::createSnapshotFrom($this, $snapshot);
        return $snapshot;
    }

    /**
     *
     * @param PRSnapshot $snapshot
     * @return void|\Procure\Domain\GoodsReceipt\GRDoc
     */
    public static function makeFromSnapshot(PRSnapshot $snapshot)
    {
        if (! $snapshot instanceof PRSnapshot)
            return;

        if ($snapshot->uuid == null) {
            $snapshot->uuid = Uuid::uuid4()->toString();
        }

        $instance = new self();
        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);
        return $instance;
    }

    /**
     *
     * @return \Procure\Domain\PurchaseRequest\PRDoc
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new PRDoc();
        }
        return self::$instance;
    }

    public static function createFrom(PRSnapshot $snapshot, CommandOptions $options, SharedService $sharedService)
    {
        Assert::notNull($snapshot, "PO snapshot not found");
        Assert::notNull($options, "command options not found");
        $validationService = ValidatorFactory::create($sharedService);

        $snapshot->initDoc($options);

        $instance = new self();
        PRSnapshotAssembler::updateEntityAllFieldsFrom($instance, $snapshot);
        $instance->setDocType(ProcureDocType::PR);
        $instance->setDocNumber($instance->getPrNumber());
        $instance->setPrName($instance->getPrNumber());

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
        Assert::notEq($rootEntity->getDocStatus(), ProcureDocStatus::POSTED, sprintf("PR is already posted! %s", $rootEntity->getId()));
        Assert::notNull($snapshot, "PR snapshot not found");
        Assert::notNull($options, "Command options not found");
        $validationService = ValidatorFactory::create($sharedService);

        $snapshot->prName = $snapshot->prNumber;

        PRSnapshotAssembler::updateEntityExcludedDefaultFieldsFrom($rootEntity, $snapshot);

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

    /**
     *
     * @param PRSnapshot $snapshot
     * @return void|\Procure\Domain\PurchaseRequest\PRDoc
     */
    public static function constructFromDetailsSnapshot(PRSnapshot $snapshot)
    {
        if (! $snapshot instanceof PRSnapshot) {
            return;
        }

        if ($snapshot->uuid == null) {
            $snapshot->uuid = Uuid::uuid4()->toString();
        }
        $instance = new self();
        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);
        return $instance;
    }

    public static function constructFromSnapshot(PRSnapshot $snapshot)
    {
        if (! $snapshot instanceof PRSnapshot) {
            return;
        }

        if ($snapshot->uuid == null) {
            $snapshot->uuid = Uuid::uuid4()->toString();
            $snapshot->token = $snapshot->uuid;
        }

        $instance = new self();
        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);
        return $instance;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GoodsReceipt\GenericGR::doPost()
     */
    protected function doPost(CommandOptions $options, ValidationServiceInterface $validationService, SharedServiceInterface $sharedService)
    {
        /**
         *
         * @var PRRow $row ;
         */
        $postedDate = new \Datetime();

        $this->markAsPosted($options->getUserId(), date_format($postedDate, 'Y-m-d H:i:s'));

        foreach ($this->getDocRows() as $row) {

            if ($row->getDocQuantity() == 0) {
                continue;
            }

            $row->markRowAsPosted($this, $options);
        }

        $this->validate($validationService, true);

        if ($this->hasErrors()) {
            throw new ValidationFailedException(sprintf("%s-%s", $this->getNotification()->errorMessage(), __FUNCTION__));
        }

        /**
         *
         * @var PrCmdRepositoryInterface $rep ;
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rep->post($this, true);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GenericDoc::afterPost()
     */
    protected function afterPost(\Application\Domain\Shared\Command\CommandOptions $options, \Procure\Domain\Service\Contracts\ValidationServiceInterface $validationService, \Procure\Domain\Service\Contracts\SharedServiceInterface $sharedService)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GenericDoc::afterReserve()
     */
    protected function afterReserve(\Application\Domain\Shared\Command\CommandOptions $options, \Procure\Domain\Service\Contracts\ValidationServiceInterface $validationService, \Procure\Domain\Service\Contracts\SharedServiceInterface $sharedService)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GenericDoc::doReverse()
     */
    protected function doReverse(\Application\Domain\Shared\Command\CommandOptions $options, \Procure\Domain\Service\Contracts\ValidationServiceInterface $validationService, \Procure\Domain\Service\Contracts\SharedServiceInterface $sharedService)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GenericDoc::prePost()
     */
    protected function prePost(\Application\Domain\Shared\Command\CommandOptions $options, \Procure\Domain\Service\Contracts\ValidationServiceInterface $validationService, \Procure\Domain\Service\Contracts\SharedServiceInterface $sharedService)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GenericDoc::preReserve()
     */
    protected function preReserve(\Application\Domain\Shared\Command\CommandOptions $options, \Procure\Domain\Service\Contracts\ValidationServiceInterface $validationService, \Procure\Domain\Service\Contracts\SharedServiceInterface $sharedService)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GenericDoc::raiseEvent()
     */
    protected function raiseEvent()
    {
        // TODO Auto-generated method stub
    }
}