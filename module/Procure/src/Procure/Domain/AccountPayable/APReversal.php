<?php
namespace Procure\Domain\AccountPayable;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\Command\CommandOptions;
use Procure\Domain\AccountPayable\Validator\ValidatorFactory;
use Procure\Domain\Contracts\ProcureDocType;
use Procure\Domain\Contracts\ReversalDocInterface;
use Procure\Domain\Event\Ap\ApReservalCreated;
use Procure\Domain\Event\Ap\ApReversed;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Shared\ProcureDocStatus;
use Procure\Domain\AccountPayable\Repository\APCmdRepositoryInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
final class APReversal extends GenericAP implements ReversalDocInterface
{

    private function __construct()
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\AccountPayable\GenericAP::specify()
     */
    public function specify()
    {
        $this->setDocType(ProcureDocType::INVOICE_REVERSAL);
    }

    /**
     *
     * @return \Procure\Domain\AccountPayable\APReversal
     */
    public static function getInstance()
    {
        return new self();
    }

    /**
     *
     * @param GenericAP $sourceObj
     * @param APSnapshot $snapshot
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @return \Procure\Domain\AccountPayable\APReversal
     */
    public static function createAndPostReversal(GenericAP $sourceObj, APSnapshot $snapshot, CommandOptions $options, SharedService $sharedService)
    {
        if (! $sourceObj instanceof GenericAP) {
            throw new \InvalidArgumentException("AP Doc is required");
        }

        if ($sourceObj->getDocStatus() != ProcureDocStatus::POSTED) {
            throw new \RuntimeException("AP document is not posted yet!");
        }

        $rows = $sourceObj->getDocRows();

        if (empty($rows)) {
            throw new \InvalidArgumentException("AP Doc is empty!");
        }

        /**
         *
         * @var APReversal $instance
         * @var APRow $r ;
         */
        $instance = new self();
        $instance = $sourceObj->convertTo($instance);

        $instance->specify(); // important
        $validationService = ValidatorFactory::createForPosting($sharedService);

        if ($validationService->getHeaderValidators() == null) {
            throw new \InvalidArgumentException("Header validators not found");
        }
        // also row validation needed.
        if ($validationService->getRowValidators() == null) {
            throw new \InvalidArgumentException("Rows validators not found");
        }

        if ($options == null) {
            throw new \InvalidArgumentException("No Options is found");
        }

        // overwrite.

        $instance->setBaseDocId($sourceObj->getId());
        $instance->setBaseDocType($sourceObj->getDocType());

        $reversalDate = $snapshot->getReversalDate();
        $createdDate = new \DateTime();
        $createdBy = $options->getUserId();

        $instance->initDoc($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));
        $instance->markAsReversed($createdBy, $reversalDate);
        $instance->validateHeader($validationService->getHeaderValidators());

        // $sourceObj
        $sourceObj->markAsReversed($createdBy, $reversalDate);
        $sourceObj->validateHeader($validationService->getHeaderValidators());

        foreach ($rows as $r) {

            // $sourceObj
            $r->markAsReversed($createdBy, $reversalDate);
            $sourceObj->validateRow($r, $validationService->getRowValidators());

            $localEntity = APRow::createRowReversal($instance, $r, $options);

            $instance->addRow($localEntity);
            $instance->validateRow($localEntity, $validationService->getRowValidators());
        }

        if ($instance->hasErrors()) {
            throw new \RuntimeException($instance->getErrorMessage());
        }

        if ($sourceObj->hasErrors()) {
            throw new \RuntimeException($sourceObj->getErrorMessage());
        }

        $instance->clearEvents();
        $sourceObj->clearEvents();

        /**
         *
         * @var APCmdRepositoryInterface $rep
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();

        // Post new object
        $snapshot = $rep->post($instance, true);

        if (! $snapshot instanceof APSnapshot) {
            throw new \RuntimeException(sprintf("Error orcured when reveral AP #%s", $sourceObj->getId()));
        }

        $instance->updateIdentityFrom($snapshot);

        // Post source object
        $rep->post($sourceObj, false);

        $target = $snapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($snapshot->getId());
        $defaultParams->setTargetToken($snapshot->getToken());
        $defaultParams->setTargetDocVersion($snapshot->getDocVersion());
        $defaultParams->setTargetRrevisionNo($snapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;
        $e1 = new ApReservalCreated($target, $defaultParams, $params);

        $target = $sourceObj;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($sourceObj->getId());
        $defaultParams->setTargetToken($sourceObj->getToken());
        $defaultParams->setTargetDocVersion($sourceObj->getDocVersion());
        $defaultParams->setTargetRrevisionNo($sourceObj->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;
        $e2 = new ApReversed($target, $defaultParams, $params);

        $instance->addEvent($e1);
        $instance->addEvent($e2);

        $sourceObj->addEvent($e1);
        $sourceObj->addEvent($e2);

        return $instance;
    }
}