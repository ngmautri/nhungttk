<?php
namespace Procure\Domain\PurchaseRequest;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\Command\CommandOptions;
use Application\Domain\Util\Translator;
use Procure\Application\DTO\Pr\PrDTO;
use Procure\Domain\GenericRow;
use Procure\Domain\Contracts\ProcureDocStatus;
use Procure\Domain\Event\Pr\PrPosted;
use Procure\Domain\Event\Pr\PrRowAdded;
use Procure\Domain\Event\Pr\PrRowRemoved;
use Procure\Domain\Event\Pr\PrRowUpdated;
use Procure\Domain\PurchaseRequest\Repository\PrCmdRepositoryInterface;
use Procure\Domain\PurchaseRequest\Validator\ValidatorFactory;
use Procure\Domain\Service\PRPostingService;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Domain\Validator\RowValidatorCollection;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class GenericPR extends BasePR
{

    /**
     *
     * @param PRRow $row
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @return \Procure\Domain\PurchaseRequest\PRRowSnapshot
     */
    public function removeRow(PRRow $row, CommandOptions $options, SharedService $sharedService)
    {
        Assert::notEq($this->getDocStatus(), ProcureDocStatus::POSTED, sprintf("PR is posted already! %s", $this->getId()));
        Assert::notNull($options, "command options not found");
        Assert::notNull($row, "PRRow not found");

        /**
         *
         * @var PRRowSnapshot $localSnapshot
         * @var PrCmdRepositoryInterface $rep ;
         */

        $this->logInfo("GenericPR will be deleted " . $row->getId());

        $rep = $sharedService->getPostingService()->getCmdRepository();

        $rep->removeRow($this, $row);

        $params = [
            "rowId" => $row->getId(),
            "rowToken" => $row->getToken()
        ];

        $target = $this->makeSnapshot();
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        $defaultParams->setTargetDocVersion($this->getDocVersion());
        $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());

        $event = new PrRowRemoved($target, $defaultParams, $params);
        $this->addEvent($event);

        return $localSnapshot;
    }

    /**
     *
     * @param SharedService $sharedService
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @return \Procure\Domain\PurchaseRequest\GenericPR
     */
    public function store(SharedService $sharedService)
    {
        Assert::notNull($sharedService, Translator::translate(sprintf("Shared Service not set! %s", __FUNCTION__)));

        $rep = $sharedService->getPostingService()->getCmdRepository();

        if (! $rep instanceof PrCmdRepositoryInterface) {
            throw new \InvalidArgumentException(Translator::translate(sprintf("PrCmdRepositoryInterface not set! %s", __FUNCTION__)));
        }

        $this->setLogger($sharedService->getLogger());
        $validationService = ValidatorFactory::create($sharedService, true);

        $this->validate($validationService);
        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getErrorMessage());
        }

        $rep->store($this);

        $this->logInfo(\sprintf("PR saved %s", __METHOD__));
        return $this;
    }

    public function deactivateRow(PRRow $row, CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, PRPostingService $postingService)
    {}

    /**
     *
     * @param PRRowSnapshot $snapshot
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @param boolean $storeNow
     * @throws \RuntimeException
     * @return \Procure\Domain\PurchaseRequest\GenericPR|\Procure\Domain\PurchaseRequest\PRRowSnapshot
     */
    public function createRowFrom(PRRowSnapshot $snapshot, CommandOptions $options, SharedService $sharedService, $storeNow = true)
    {
        Assert::notEq($this->getDocStatus(), ProcureDocStatus::POSTED, sprintf("PR is posted!%s", $this->getId()));
        Assert::notNull($options, "command options not found");
        Assert::notNull($snapshot, "PRRowSnapshot not found");

        $validationService = ValidatorFactory::create($sharedService);

        $snapshot->docType = $this->docType;

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $snapshot->initSnapshot($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        $row = PRRow::createFromSnapshot($this, $snapshot);

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
         * @var PRRowSnapshot $localSnapshot
         * @var PrCmdRepositoryInterface $rep ;
         */

        $rep = $sharedService->getPostingService()->getCmdRepository();
        $localSnapshot = $rep->storeRow($this, $row);

        Assert::notNull($localSnapshot, sprintf("Error occured when creating PR Row #%s", $this->getId()));

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

        $event = new PrRowAdded($target, $defaultParams, $params);
        $this->addEvent($event);

        return $localSnapshot;
    }

    /**
     *
     * @param PRRowSnapshot $snapshot
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @throws \RuntimeException
     * @return \Procure\Domain\PurchaseRequest\PRRowSnapshot
     */
    public function updateRowFrom(GenericRow $row, PRRowSnapshot $snapshot, CommandOptions $options, $params, SharedService $sharedService)
    {
        Assert::notEq($this->getDocStatus(), ProcureDocStatus::POSTED, sprintf("PR is posted!%s", $this->getId()));
        Assert::notNull($options, "command options not found");
        Assert::notNull($snapshot, "PRRowSnapshot not found");

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $snapshot->markAsChange($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));
        $validationService = ValidatorFactory::create($sharedService);

        $updatedRow = PRRow::createFromSnapshot($this, $snapshot);

        if ($updatedRow->getId() != $row->getId()) {
            throw new \RuntimeException("ID not matched!");
        }

        // update row.
        // PRRowSnapshotAssembler::updateDefaultFieldsFrom($row, $snapshot);
        // $row->updateRowStatus();

        $this->validateRow($updatedRow, $validationService->getRowValidators());

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getNotification()->errorMessage());
        }

        $this->clearEvents();
        /**
         *
         * @var PRRowSnapshot $localSnapshot
         * @var PrCmdRepositoryInterface $rep ;
         */

        $rep = $sharedService->getPostingService()->getCmdRepository();
        $localSnapshot = $rep->storeRow($this, $updatedRow);

        Assert::notNull($localSnapshot, sprintf("Error occured when creating PR Row #%s", $this->getId()));

        $target = $this->makeSnapshot();
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        $defaultParams->setTargetDocVersion($this->getDocVersion());
        $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());

        $event = new PrRowUpdated($target, $defaultParams, $params);
        $this->addEvent($event);

        return $localSnapshot;
    }

    /**
     *
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @throws \RuntimeException
     * @return \Procure\Domain\PurchaseRequest\GenericPR
     */
    public function post(CommandOptions $options, SharedService $sharedService)
    {
        Assert::notEq($this->getDocStatus(), ProcureDocStatus::POSTED, sprintf("PR is posted!%s", $this->getId()));
        Assert::notNull($options, "command options not found");

        // need to refresh doc.
        $this->refreshDoc();

        $validationService = ValidatorFactory::create($sharedService);

        $this->validate($validationService);
        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getErrorMessage());
        }

        $this->clearEvents();
        $this->clearNotification();

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

        $params = null;

        $event = new PrPosted($target, $defaultParams, $params);
        $this->addEvent($event);

        return $this;
    }

    public function reverse(CommandOptions $options, SharedService $sharedService)
    {
        Assert::eq($this->getDocStatus(), ProcureDocStatus::POSTED, sprintf("PR is not posted!%s", $this->getId()));
        Assert::notNull($options, "command options not found");

        // need to refresh doc.
        $this->refreshDoc();

        $validationService = ValidatorFactory::create($sharedService);
        $this->validate($validationService);
        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getErrorMessage());
        }

        $this->clearEvents();

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

        $params = null;

        $event = new PrPosted($target, $defaultParams, $params);
        $this->addEvent($event);

        return $this;
    }

    /**
     *
     * @deprecated
     * @return NULL|object
     */
    public function makeDetailsDTO()
    {
        $dto = new PrDTO();
        $dto = DTOFactory::createDTOFrom($this, $dto);
        return $dto;
    }

    /**
     *
     * @deprecated
     * @return NULL|object
     */
    public function makeHeaderDTO()
    {
        $dto = new PrDTO();
        $dto = DTOFactory::createDTOFrom($this, $dto);
        return $dto;
    }

    /**
     *
     * @deprecated
     * {@inheritdoc}
     * @see \Procure\Domain\GenericDoc::makeDTOForGrid()
     */
    public function makeDTOForGrid()
    {
        $dto = new PrDTO();
        DTOFactory::createDTOFrom($this, $dto);
        $rowDTOList = [];
        if (count($this->docRows) > 0) {
            foreach ($this->docRows as $row) {

                if ($row instanceof PRRow) {
                    $rowDTOList[] = $row->makeDetailsDTO();
                }
            }
        }

        $dto->docRowsDTO = $rowDTOList;
        return $dto;
    }
}