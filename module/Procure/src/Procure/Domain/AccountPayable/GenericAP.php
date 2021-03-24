<?php
namespace Procure\Domain\AccountPayable;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\Command\CommandOptions;
use Application\Domain\Util\Translator;
use Procure\Application\DTO\Ap\ApDTO;
use Procure\Domain\AccountPayable\Factory\APFactory;
use Procure\Domain\AccountPayable\Repository\APCmdRepositoryInterface;
use Procure\Domain\AccountPayable\Validator\ValidatorFactory;
use Procure\Domain\Contracts\AutoGeneratedDocInterface;
use Procure\Domain\Contracts\ProcureDocStatus;
use Procure\Domain\Contracts\ReversalDocInterface;
use Procure\Domain\Event\Ap\ApPosted;
use Procure\Domain\Event\Ap\ApReversed;
use Procure\Domain\Event\Ap\ApRowAdded;
use Procure\Domain\Event\Ap\ApRowRemoved;
use Procure\Domain\Event\Ap\ApRowUpdated;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Service\Contracts\SharedServiceInterface;
use Procure\Domain\Service\Contracts\ValidationServiceInterface;
use Procure\Infrastructure\Doctrine\APCmdRepositoryImpl;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class GenericAP extends BaseDoc
{

    abstract public function specify();

    // ============================
    public function removeRow(APRow $row, CommandOptions $options, SharedService $sharedService)
    {
        Assert::notEq($this->getDocStatus(), ProcureDocStatus::POSTED, sprintf("AP is posted already! %s", $this->getId()));
        Assert::notNull($options, "Command options not found");

        /**
         *
         * @var ApCmdRepositoryInterface $rep ;
         */

        $rep = $sharedService->getPostingService()->getCmdRepository();
        Assert::isInstanceOf($rep, ApCmdRepositoryInterface::class);

        $rep->removeRow($this, $row);

        $params = [
            "rowId" => $row->getId(),
            "rowToken" => $row->getToken()
        ];

        // remove row from collection

        $target = $this->makeSnapshot();
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        $defaultParams->setTargetDocVersion($this->getDocVersion());
        $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());

        $event = new ApRowRemoved($target, $defaultParams, $params);
        $this->addEvent($event);

        return $this;
    }

    public function store(SharedService $sharedService)
    {
        Assert::notNull($sharedService, Translator::translate(sprintf("Shared Service not set! %s", __FUNCTION__)));

        $rep = $sharedService->getPostingService()->getCmdRepository();

        if (! $rep instanceof APCmdRepositoryImpl) {
            throw new \InvalidArgumentException(Translator::translate(sprintf("APCmdRepositoryImpl not set! %s", __FUNCTION__)));
        }

        $this->setLogger($sharedService->getLogger());
        $validationService = ValidatorFactory::create($sharedService, true);

        $this->validate($validationService);
        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getErrorMessage());
        }

        $rep->store($this);

        $this->logInfo(\sprintf("A/P invoice saved %s", __METHOD__));
        return $this;
    }

    /**
     *
     * @return NULL[]|\Procure\Domain\AccountPayable\GenericAP[]
     */
    public function generateDocumentByWarehouse()
    {
        $subDocuments = [];
        $results = $this->splitRowsByWarehouse();

        foreach ($results as $k => $v) {
            $doc = APFactory::createEmptyObject($this->getDocType());
            $this->convertAllTo($doc);

            // Enrichment and overwrite.
            $doc->setWarehouse($k);
            $doc->setDocRows($v);
            $doc->setRemarks(\sprintf("%s-WH%s", $this->getSysNumber(), $k));
            $doc->setRowIdArray(null);
            $subDocuments[] = $doc;
        }

        return $subDocuments;
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
         * @var APRow $row ;
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
            throw new \RuntimeException(sprintf("%s-%s", $this->getNotification()->errorMessage(), __FUNCTION__));
        }

        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rep->post($this, true);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GenericDoc::doReverse()
     */
    protected function doReverse(CommandOptions $options, ValidationServiceInterface $validationService, SharedServiceInterface $sharedService)
    {
        /**
         *
         * @var APRow $row ;
         */
        $postedDate = new \Datetime();
        $this->markAsReversed($options->getUserId(), date_format($postedDate, 'Y-m-d H:i:s'));

        foreach ($this->getDocRows() as $row) {

            if ($row->getDocQuantity() == 0) {
                continue;
            }

            $row->markRowAsReversed($options->getUserId(), date_format($postedDate, 'Y-m-d H:i:s'));
        }

        $this->validate($validationService, true);

        if ($this->hasErrors()) {
            throw new \RuntimeException(sprintf("%s-%s", $this->getNotification()->errorMessage(), __FUNCTION__));
        }

        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rep->post($this, true);
    }

    public function deactivateRow(APRow $row, CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService)
    {}

    /**
     *
     * @param APRowSnapshot $snapshot
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @return \Procure\Domain\AccountPayable\APRowSnapshot
     */
    public function createRowFrom(APRowSnapshot $snapshot, CommandOptions $options, SharedService $sharedService, $storeNow = true)
    {
        Assert::notEq($this->getDocStatus(), ProcureDocStatus::POSTED, sprintf("AP Invoice is posted %s", $this->getId()));
        Assert::notNull($snapshot, "Row Snapshot not founds");
        Assert::notNull($options, "Options not founds");

        $validationService = ValidatorFactory::create($sharedService);
        $snapshot->docType = $this->getDocType();
        $snapshot->initRow($options);

        $row = APRow::createFromSnapshot($this, $snapshot);

        $this->validateRow($row, $validationService->getRowValidators());

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getNotification()->errorMessage());
        }

        $this->clearEvents();
        $this->addRow($row);

        if (! $storeNow) {
            return $this;
        }

        /**
         *
         * @var APRowSnapshot $localSnapshot
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $localSnapshot = $rep->storeRow($this, $row);

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

        $event = new ApRowAdded($target, $defaultParams, $params);
        $this->addEvent($event);

        return $localSnapshot;
    }

    /**
     *
     * @param APRowSnapshot $snapshot
     * @param CommandOptions $options
     * @param array $params
     * @param SharedService $sharedService
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @return \Procure\Domain\AccountPayable\APRowSnapshot
     */
    public function updateRowFrom(APRowSnapshot $snapshot, CommandOptions $options, $params, SharedService $sharedService)
    {
        Assert::notEq($this->getDocStatus(), ProcureDocStatus::POSTED, sprintf("AP is already posted %s", $this->getId()));
        Assert::notNull($snapshot, "Row Snapshot not founds");
        Assert::notNull($options, "Options not founds");

        $validationService = ValidatorFactory::create($sharedService);

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $snapshot->markAsChange($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        $row = APRow::createFromSnapshot($this, $snapshot);

        $this->validateRow($row, $validationService->getRowValidators());

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getNotification()->errorMessage());
        }

        $this->clearEvents();
        $this->clearNotification();

        /**
         *
         * @var APRowSnapshot $localSnapshot ;
         * @var APCmdRepositoryInterface $rep
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $localSnapshot = $rep->storeRow($this, $row);

        if ($localSnapshot == null) {
            throw new \RuntimeException(sprintf("Error occured when creating GR Row #%s", $this->getId()));
        }

        $target = $this->makeSnapshot();
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        $defaultParams->setTargetDocVersion($this->getDocVersion());
        $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());

        $event = new ApRowUpdated($target, $defaultParams, $params);
        $this->addEvent($event);

        $f = 'AP Row updated. (%s fields changed)';
        $this->logInfo(\sprintf($f, count($params)));

        return $localSnapshot;
    }

    /**
     *
     * @param APSnapshot $snapshot
     */
    protected function refreshRows(APSnapshot $snapshot)
    {
        foreach ($this->getDocRows() as $row) {
            /**
             *
             * @var APRow $row ;
             */
            $row->refreshRowsFromNewHeaderSnapshot($snapshot);
        }
    }

    /**
     *
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @return \Procure\Domain\AccountPayable\GenericAP
     */
    public function post(CommandOptions $options, SharedService $sharedService)
    {
        if ($this instanceof AutoGeneratedDocInterface) {
            $f = Translator::translate("Manual posting is not allowed on this document type! %s");
            throw new \InvalidArgumentException(sprintf($f, $this->getDocType()));
        }

        if ($this->getDocStatus() !== ProcureDocStatus::DRAFT) {
            $f = Translator::translate("Document is already posted/closed or being amended! %s");
            throw new \RuntimeException(sprintf($f, __FUNCTION__));
        }

        $validationService = ValidatorFactory::createForPosting($sharedService, true);

        $this->validate($validationService);

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getErrorMessage());
        }

        $this->clearEvents();

        // Templete Pattern
        $this->prePost($options, $validationService, $sharedService);
        $this->doPost($options, $validationService, $sharedService);
        $this->afterPost($options, $validationService, $sharedService);

        $target = $this->makeSnapshot();
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        $defaultParams->setTargetDocVersion($this->getDocVersion());
        $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = [
            "message" => \sprintf("AP Doc %s posted successfully!", $this->getId())
        ];
        $event = new ApPosted($target, $defaultParams, $params);
        $this->addEvent($event);

        return $this;
    }

    /**
     *
     * @param CommandOptions $options
     * @param ValidationServiceInterface $validationService
     * @param SharedService $sharedService
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @return \Procure\Domain\AccountPayable\GenericAP
     */
    public function reverse(CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService)
    {
        if ($this instanceof ReversalDocInterface) {
            $f = Translator::translate("Document is already reversed! %s");
            throw new \InvalidArgumentException(sprintf($f, $this->getDocType()));
        }

        if ($this->getDocStatus() !== ProcureDocStatus::POSTED) {
            throw new \RuntimeException(sprintf("Document is not posted yet! %s", __METHOD__));
        }

        $this->_checkParams($validationService, $sharedService);

        $this->validate($validationService->getHeaderValidators(), $validationService->getRowValidators());
        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getErrorMessage());
        }

        $this->clearEvents();

        // Templete Pattern
        $this->preReserve($options, $validationService, $sharedService);
        $this->doReverse($options, $validationService, $sharedService);
        $this->afterReserve($options, $validationService, $sharedService);

        $target = $this->makeSnapshot();
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        $defaultParams->setTargetDocVersion($this->getDocVersion());
        $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());

        $params = [
            "message" => \sprintf("AP Doc %s reversed successfully!", $this->getId())
        ];

        $event = new ApReversed($target, $defaultParams, $params);
        $this->addEvent($event);
        return $this;
    }

    public function makeSnapshot()
    {
        return APSnapshotAssembler::updateAllFieldsFrom(new APSnapshot(), $this);
    }

    /**
     *
     * @return NULL|object
     */
    public function makeDetailsDTO()
    {
        $dto = new ApDTO();
        $dto = DTOFactory::createDTOFrom($this, $dto);
        return $dto;
    }

    /**
     *
     * @return NULL|object
     */
    public function makeHeaderDTO()
    {
        $dto = new ApDTO();
        $dto = DTOFactory::createDTOFrom($this, $dto);
        return $dto;
    }

    /**
     *
     * @param object $dto
     * @return NULL|object
     */
    public function makeDTOForGrid()
    {
        $dto = DTOFactory::createDTOFrom($this, new ApDTO());
        $rowDTOList = [];
        if (count($this->docRows) > 0) {
            foreach ($this->docRows as $row) {

                if ($row instanceof APRow) {
                    $rowDTOList[] = $row->makeDetailsDTO();
                }
            }
        }

        $dto->docRowsDTO = $rowDTOList;
        return $dto;
    }

    protected function raiseEvent()
    {}

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
}