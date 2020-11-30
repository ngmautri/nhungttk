<?php
namespace Procure\Domain\AccountPayable;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\Command\CommandOptions;
use Procure\Domain\AccountPayable\Validator\ValidatorFactory;
use Procure\Domain\Contracts\ProcureDocStatus;
use Procure\Domain\Contracts\ProcureDocType;
use Procure\Domain\Contracts\ProcureTrxStatus;
use Procure\Domain\Event\Ap\ApHeaderCreated;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\Service\SharedService;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
final class APFromPO extends GenericAP
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\AccountPayable\GenericAP::specify()
     */
    public function specify()
    {
        $this->setDocType(ProcureDocType::INVOICE_FROM_PO);
    }

    // ===================
    private function __construct()
    {}

    /**
     *
     * @return \Procure\Domain\AccountPayable\APFromPO
     */
    public static function getInstance()
    {
        return new self();
    }

    /**
     *
     * @param PODoc $sourceObj
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @throws \InvalidArgumentException
     * @return \Procure\Domain\AccountPayable\APDoc
     */
    public static function createFromPo(PODoc $sourceObj, CommandOptions $options, SharedService $sharedService)
    {
        Assert::isInstanceOf($sourceObj, PODoc::class, sprintf("PO Entity is required %s", __FUNCTION__));
        Assert::Eq($sourceObj->getDocStatus(), ProcureDocStatus::POSTED, sprintf("PO document is not posted!%s", __FUNCTION__));
        Assert::notEq($sourceObj->getDocStatus(), ProcureTrxStatus::COMPLETED, sprintf("PO document is completed!%s", __FUNCTION__));

        $rows = $sourceObj->getDocRows();
        Assert::notNull($rows, sprintf("PO Entity is empty! %s", __FUNCTION__));

        Assert::notNull($options, sprintf("No command options is found%s", __FUNCTION__));

        /**
         *
         * @var APFromPO $instance ;
         */
        $instance = new self();
        $instance = $sourceObj->convertTo($instance);

        // overwrite.

        $createdBy = $options->getUserId();
        $createdDate = new \DateTime();
        $instance->initDoc($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));
        $instance->setDocType(ProcureDocType::INVOICE_FROM_PO);
        $instance->setBaseDocId($sourceObj->getId());
        $instance->setBaseDocType($sourceObj->getDocType());
        $validationService = ValidatorFactory::createForCopyFromPO($sharedService);
        $instance->validateHeader($validationService->getHeaderValidators());

        foreach ($rows as $r) {

            /**
             *
             * @var APRow $r ;
             */
            $localEntity = APRow::createFromPoRow($instance, $r, $options);
            $instance->addRow($localEntity);
            $instance->validateRow($localEntity, $validationService->getRowValidators());
        }
        return $instance;
    }

    /**
     *
     * @param APSnapshot $snapshot
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @return \Procure\Domain\AccountPayable\APSnapshot
     */
    public function saveFromPO(APSnapshot $snapshot, CommandOptions $options, SharedService $sharedService)
    {
        if (! $this->getDocStatus() == ProcureDocStatus::DRAFT) {
            throw new \RuntimeException(sprintf("PO is already posted/closed or being amended! %s", __FUNCTION__));
        }

        if ($this->getDocRows() == null) {
            throw new \RuntimeException(sprintf("Documment is empty! %s", __FUNCTION__));
        }

        if (! $this->getDocType() == ProcureDocType::INVOICE_FROM_PO) {
            throw new \RuntimeException(sprintf("Doctype is not vadid! %s", __FUNCTION__));
        }

        if ($options == null) {
            throw new \InvalidArgumentException("Comnand Options not found!");
        }

        $validationService = ValidatorFactory::createForCopyFromPO($sharedService);

        // Entity from Snapshot
        if ($snapshot !== null) {
            $this->setDocCurrency($snapshot->getDocCurrency());
            $this->setDocDate($snapshot->getDocDate());
            $this->setDocNumber($snapshot->getDocNumber());
            $this->setPostingDate($snapshot->getPostingDate());
            $this->setGrDate($snapshot->getGrDate());
            $this->setWarehouse($snapshot->getWarehouse());
            $this->setPmtTerm($snapshot->getPmtTerm());
            $this->setRemarks($snapshot->getRemarks());
            $this->setCompany($options->getCompanyId());
        }

        // update warehouse for row.
        $this->refreshRows($snapshot);

        $createdDate = new \Datetime();
        $this->setCreatedOn(date_format($createdDate, 'Y-m-d H:i:s'));

        $this->validate($validationService);
        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getErrorMessage());
        }

        $this->clearEvents();
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rootSnapshot = $rep->store($this);

        if (! $rootSnapshot instanceof APSnapshot) {
            throw new \RuntimeException(\sprintf("Errors occured when saving AP"));
        }

        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetToken($rootSnapshot->getToken());
        $defaultParams->setTargetDocVersion($rootSnapshot->getDocVersion());
        $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new ApHeaderCreated($target, $defaultParams, $params);
        $this->addEvent($event);

        return $rootSnapshot;
    }
}