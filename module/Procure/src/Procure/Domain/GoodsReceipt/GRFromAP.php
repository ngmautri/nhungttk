<?php
namespace Procure\Domain\GoodsReceipt;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\Command\CommandOptions;
use Procure\Domain\AccountPayable\APDoc;
use Procure\Domain\AccountPayable\APRow;
use Procure\Domain\AccountPayable\GenericAP;
use Procure\Domain\Contracts\AutoGeneratedDocInterface;
use Procure\Domain\Contracts\ProcureDocType;
use Procure\Domain\Contracts\ProcureGoodsFlow;
use Procure\Domain\Event\Gr\GrPosted;
use Procure\Domain\GoodsReceipt\Repository\GrCmdRepositoryInterface;
use Procure\Domain\GoodsReceipt\Validator\ValidatorFactory;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Shared\ProcureDocStatus;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GRFromAP extends GenericGoodsReceipt implements AutoGeneratedDocInterface
{

    private function __construct()
    {}

    /**
     *
     * @return \Procure\Domain\GoodsReceipt\GRFromAP
     */
    public static function getInstance()
    {
        return new self();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GoodsReceipt\GenericGR::specify()
     */
    public function specify()
    {
        $this->flow = ProcureGoodsFlow::IN;
        $this->docType = ProcureDocType::GR_FROM_INVOICE;
    }

    /**
     *
     * @param APDoc $sourceObj
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @return \Procure\Domain\GoodsReceipt\GRDoc
     */
    public static function copyFromAP(APDoc $sourceObj, CommandOptions $options, SharedService $sharedService)
    {
        if (! $sourceObj instanceof APDoc) {
            throw new \InvalidArgumentException("AP Entity is required");
        }

        $rows = $sourceObj->getDocRows();

        if ($rows == null) {
            throw new \InvalidArgumentException("AP Entity is empty!");
        }
        if ($options == null) {
            throw new \InvalidArgumentException("No Options is found");
        }

        if ($sourceObj->getDocStatus() !== ProcureDocStatus::POSTED) {
            throw new \RuntimeException("AP document is not posted!");
        }

        /**
         *
         * @var \Procure\Domain\GoodsReceipt\GRFromAP $instance
         */
        $instance = new self();
        $instance = $sourceObj->convertTo($instance);

        // overwrite.
        $instance->setDocType(ProcureDocType::GR_FROM_AP); // important.
        $instance->setBaseDocId($sourceObj->getId()); // important.
        $instance->setBaseDocType($sourceObj->getDocType()); // important.

        $instance->initDoc($options);

        $validationService = ValidatorFactory::create($instance->getDocType(), $sharedService);
        $instance->validateHeader($validationService->getHeaderValidators());

        foreach ($rows as $r) {

            /**
             *
             * @var APRow $r ;
             */
            $grRow = GRRow::copyFromApRow($r, $options);
            $instance->addRow($grRow);

            $instance->validateRow($grRow, $validationService->getRowValidators());
        }
        return $instance;
    }

    /**
     *
     * @param GenericAP $sourceObj
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @return \Procure\Domain\GoodsReceipt\GRFromAP
     */
    public static function postCopyFromAP(GenericAP $sourceObj, CommandOptions $options, SharedService $sharedService)
    {
        Assert::isInstanceOf($sourceObj, GenericAP::class, sprintf("Generic AP Entity is required"));
        Assert::eq($sourceObj->getDocStatus(), ProcureDocStatus::POSTED, sprintf("AP is not posted yet! %s", $sourceObj->getId()));

        $rows = $sourceObj->getDocRows();
        Assert::notNull($rows, "AP Entity is empty!");

        Assert::notNull($options, "Command options not found");

        /**
         *
         * @var \Procure\Domain\GoodsReceipt\GRFromAP $instance
         */
        $instance = new self();
        $instance = $sourceObj->convertTo($instance);
        $instance->setLogger($sharedService->getLogger());
        $instance->logInfo(count($rows) . " will be added!");

        $createdBy = $options->getUserId();

        // echo 'nmt';
        $instance->initDoc($options);

        // overwrite.
        $instance->setDocType(ProcureDocType::GR_FROM_INVOICE); // important.
        $instance->setRelevantDocId($sourceObj->getId()); // important.
        $instance->setBaseDocId($sourceObj->getId()); // important.
        $instance->setBaseDocType($sourceObj->getDocType()); // important.
        $instance->markAsPosted($createdBy, $sourceObj->getPostingDate());
        $instance->setInvoiceNo($sourceObj->getDocNumber());
        $instance->setRemarks(\sprintf("[Auto.] Ref.AP %s", $sourceObj->getSysNumber()));

        foreach ($rows as $r) {

            /**
             *
             * @var APRow $r ;
             */

            $grRow = GRRow::copyFromApRow($instance, $r, $options);
            $grRow->markRowAsPosted($instance, $options);
            $instance->addRow($grRow);
        }

        $validationService = ValidatorFactory::createForCopyFromAP($instance->getDocType(), $sharedService);
        $instance->validate($validationService);

        if ($instance->hasErrors()) {
            throw new \RuntimeException($instance->getErrorMessage());
        }

        $instance->clearEvents();

        /**
         *
         * @var GRSnapshot $rootSnapshot
         * @var GrCmdRepositoryInterface $rep
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $snapshot = $rep->post($instance, true);

        $target = $snapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($snapshot->getId());
        $defaultParams->setTargetToken($snapshot->getToken());
        $defaultParams->setTargetDocVersion($snapshot->getDocVersion());
        $defaultParams->setTargetRrevisionNo($snapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;
        $event = new GrPosted($target, $defaultParams, $params);
        $instance->addEvent($event);

        $instance->updateIdentityFrom($snapshot);
        $instance->specify(); // important
        return $instance;
    }
}