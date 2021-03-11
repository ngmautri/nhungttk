<?php
namespace Procure\Domain\QuotationRequest;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Procure\Domain\Contracts\ProcureDocType;
use Procure\Domain\Event\Qr\QrHeaderCreated;
use Procure\Domain\Exception\ValidationFailedException;
use Procure\Domain\QuotationRequest\Repository\QrCmdRepositoryInterface;
use Procure\Domain\QuotationRequest\Validator\ValidatorFactory;
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
final class QRDoc extends GenericQR
{

    private static $instance = null;

    // Specific Attribute
    // ===================

    // ====================
    private function __construct()
    {}

    public function cloneAndSave(CommandOptions $options, SharedService $sharedService)
    {
        $rows = $this->getDocRows();
        Assert::notNull($rows, "GR Entity is empty!");
        $validationService = ValidatorFactory::create($sharedService);
        Assert::notNull($validationService, "Validation can not created!");

        $instance = new self();

        $exculdedProps = [
            "id",
            "uuid",
            "token",
            "docRows",
            "rowIdArray",
            "instance"
        ];

        /**
         *
         * @var QRDoc $instance ;
         */
        $instance = $this->convertExcludeFieldsTo($instance, $exculdedProps);

        // overwrite.
        $instance->initDoc($options);
        $instance->setDocType(ProcureDocType::QUOTE);
        $instance->setBaseDocId($this->getId());
        $instance->setBaseDocType($this->getDocType());
        $instance->validateHeader($validationService->getHeaderValidators());

        $instance->setDocNumber($this->getDocNumber() . "(copied)");
        $instance->setRemarks(\sprintf("Copied from %s", $this->getSysNumber()));

        foreach ($rows as $r) {

            $localEntity = QRRow::cloneFrom($instance, $r, $options);
            $instance->addRow($localEntity);
            $instance->validateRow($localEntity, $validationService->getRowValidators());
        }

        if ($instance->hasErrors()) {
            throw new \RuntimeException($instance->getErrorMessage());
        }

        $this->clearEvents();
        /**
         *
         * @var QRRowSnapshot $localSnapshot
         * @var QrCmdRepositoryInterface $rep ;
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

        $event = new QrHeaderCreated($target, $defaultParams, $params);
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
        return SnapshotAssembler::createSnapshotFrom($this, new QRSnapshot());
    }

    public function makeDetailsSnapshot()
    {
        $snapshot = new QRSnapshot();
        $snapshot = SnapshotAssembler::createSnapshotFrom($this, $snapshot);
        return $snapshot;
    }

    /**
     *
     * @param QRSnapshot $snapshot
     * @return void|\Procure\Domain\GoodsReceipt\GRDoc
     */
    public static function makeFromSnapshot(QRSnapshot $snapshot)
    {
        if (! $snapshot instanceof QRSnapshot)
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
     * @return \Procure\Domain\QuotationRequest\QRDoc
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new QRDoc();
        }
        return self::$instance;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GenericDoc::doPost()
     */
    protected function doPost(CommandOptions $options, ValidationServiceInterface $validationService, SharedServiceInterface $sharedService)
    {
        /**
         *
         * @var QRRow $row ;
         */
        $postedDate = new \Datetime();

        $this->markAsPosted($options->getUserId(), date_format($postedDate, 'Y-m-d H:i:s'));

        foreach ($this->getDocRows() as $row) {

            if ($row->getDocQuantity() == 0) {
                continue;
            }

            $row->markAsPosted($options->getUserId(), date_format($postedDate, 'Y-m-d H:i:s'));
        }
        $this->clearNotification();

        $this->validate($validationService, true);

        if ($this->hasErrors()) {
            throw new ValidationFailedException(sprintf("%s-%s", $this->getNotification()->errorMessage(), __FUNCTION__));
        }

        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rep->post($this, true);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GoodsReceipt\GenericGR::doReverse()
     */
    protected function doReverse(CommandOptions $options, ValidationServiceInterface $validationService, SharedServiceInterface $sharedService)
    {
        // blank
    }

    protected function afterPost(CommandOptions $options, ValidationServiceInterface $validationService, SharedServiceInterface $sharedService)
    {}

    protected function prePost(CommandOptions $options, ValidationServiceInterface $validationService, SharedServiceInterface $sharedService)
    {}

    protected function preReserve(CommandOptions $options, ValidationServiceInterface $validationService, SharedServiceInterface $sharedService)
    {}

    protected function afterReserve(CommandOptions $options, ValidationServiceInterface $validationService, SharedServiceInterface $sharedService)
    {}

    protected function raiseEvent()
    {}
}