<?php
namespace Procure\Domain\PurchaseOrder;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\Command\CommandOptions;
use Procure\Application\DTO\Po\PoDetailsDTO;
use Procure\Domain\Contracts\ProcureDocStatus;
use Procure\Domain\Event\Po\PoAmendmentAccepted;
use Procure\Domain\Event\Po\PoAmendmentEnabled;
use Procure\Domain\Event\Po\PoPosted;
use Procure\Domain\Event\Po\PoRowAdded;
use Procure\Domain\Event\Po\PoRowUpdated;
use Procure\Domain\PurchaseOrder\Repository\POCmdRepositoryInterface;
use Procure\Domain\PurchaseOrder\Validator\ValidatorFactory;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Service\Contracts\SharedServiceInterface;
use Procure\Domain\Service\Contracts\ValidationServiceInterface;
use Procure\Domain\Shared\Constants;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class GenericPO extends BaseDoc
{

    public function deactivateRow(PORow $row, CommandOptions $options, ValidationServiceInterface $validationService, SharedServiceInterface $sharedService)
    {}

    public function enableAmendment(CommandOptions $options, SharedService $sharedService)
    {
        Assert::eq($this->getDocStatus(), ProcureDocStatus::POSTED, sprintf("PO can not be amended! %s", $this->getId()));
        Assert::notEq($this->getTransactionStatus(), Constants::TRANSACTION_STATUS_COMPLETED, 'PO is completed');
        Assert::notNull($options, "command options not found");

        $validationService = ValidatorFactory::create($sharedService);
        Assert::notNull($validationService, "Validation can not be created!");

        $createdDate = new \Datetime();
        $this->setLastchangeOn(date_format($createdDate, 'Y-m-d H:i:s'));
        $this->setLastchangeBy($options->getUserId());
        $this->setDocVersion($this->getDocVersion() + 1);
        $this->setDocStatus(ProcureDocStatus::AMENDING);

        $this->validateHeader($validationService->getHeaderValidators());

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getErrorMessage());
        }

        $this->recordedEvents = array();

        /**
         *
         * @var POSnapshot $rootSnapshot
         * @var POCmdRepositoryInterface $rep ;
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        Assert::implementsInterface($rep, POCmdRepositoryInterface::class, "Repository not valid");

        $rootSnapshot = $rep->storeHeader($this, false);
        Assert::notNull($rootSnapshot, sprintf("Error orcured when enabling PO for amendment #%s", $this->getId()));

        $this->id = $rootSnapshot->getId();

        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetToken($rootSnapshot->getToken());
        $defaultParams->setTargetDocVersion($rootSnapshot->getDocVersion());
        $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new PoAmendmentEnabled($target, $defaultParams, $params);
        $this->addEvent($event);
        return $this;
    }

    public function acceptAmendment(CommandOptions $options, SharedService $sharedService)
    {
        Assert::eq($this->getDocStatus(), ProcureDocStatus::AMENDING, sprintf("Document is not on amendment! %s", $this->getId()));
        Assert::notNull($options, "command options not found");

        $validationService = ValidatorFactory::create($sharedService);
        Assert::notNull($validationService, "Validation can not be created!");

        $createdDate = new \Datetime();
        $this->setLastchangeOn(date_format($createdDate, 'Y-m-d H:i:s'));
        $this->setLastchangeBy($options->getUserId());
        $this->setDocStatus(ProcureDocStatus::POSTED);

        $this->validate($validationService->getHeaderValidators(), $validationService->getRowValidators());

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getErrorMessage());
        }

        $this->clearEvents();

        /**
         *
         * @var POSnapshot $rootSnapshot
         * @var POCmdRepositoryInterface $rep ;
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        Assert::implementsInterface($rep, POCmdRepositoryInterface::class, "Repository not valid");

        $rootSnapshot = $rep->post($this, false);
        Assert::notNull($rootSnapshot, sprintf("Error orcured when enabling PO for amendment #%s", $this->getId()));

        $this->id = $rootSnapshot->getId();
        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetToken($rootSnapshot->getToken());
        $defaultParams->setTargetDocVersion($rootSnapshot->getDocVersion());
        $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new PoAmendmentAccepted($target, $defaultParams, $params);
        $this->addEvent($event);

        $event = new PoPosted($target, $defaultParams, $params);
        $this->addEvent($event);

        return $this;
    }

    public function createRowFrom(PORowSnapshot $snapshot, CommandOptions $options, SharedService $sharedService)
    {
        Assert::notEq($this->getDocStatus(), ProcureDocStatus::POSTED, sprintf("Po is posted!%s", $this->getId()));
        Assert::notNull($options, "command options not found");
        Assert::notNull($snapshot, "PORowSnapshot not found");

        $validationService = ValidatorFactory::create($sharedService);
        Assert::notNull($validationService, "Validation can not be created!");

        $snapshot->initRow($options);

        $row = PORow::createFromSnapshot($this, $snapshot);

        $this->validateRow($row, $validationService->getRowValidators());

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getNotification()->errorMessage());
        }

        $this->clearEvents();

        /**
         *
         * @var PORowSnapshot $localSnapshot
         * @var POCmdRepositoryInterface $rep ;
         */

        $rep = $sharedService->getPostingService()->getCmdRepository();
        $localSnapshot = $rep->storeRow($this, $row);
        Assert::notNull($localSnapshot, sprintf("Error occured when creating PO Row #%s", $this->getId()));

        $params = [
            "rowId" => $localSnapshot->getId(),
            "rowToken" => $localSnapshot->getToken()
        ];

        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        $defaultParams->setTargetDocVersion($this->getDocVersion());
        $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());

        $event = new PoRowAdded($this->makeSnapshot(), $defaultParams, $params);
        $this->addEvent($event);

        return $localSnapshot;
    }

    public function updateRowFrom(PORowSnapshot $snapshot, CommandOptions $options, $params, SharedService $sharedService)
    {
        Assert::notEq($this->getDocStatus(), ProcureDocStatus::POSTED, sprintf("PO is posted!%s", $this->getId()));
        Assert::notNull($options, "command options not found");
        Assert::notNull($snapshot, "PORowSnapshot not found");

        $validationService = ValidatorFactory::create($sharedService);
        Assert::notNull($validationService, "Validation can not be created!");

        $createdDate = new \Datetime();
        $snapshot->markAsChange($options->getUserId(), date_format($createdDate, 'Y-m-d H:i:s'));

        $row = PORow::createFromSnapshot($this, $snapshot);

        $this->validateRow($row, $validationService->getRowValidators());

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getNotification()->errorMessage());
        }

        $this->clearEvents();

        /**
         *
         * @var PORowSnapshot $localSnapshot
         * @var POCmdRepositoryInterface $rep ;
         */

        $rep = $sharedService->getPostingService()->getCmdRepository();
        $localSnapshot = $rep->storeRow($this, $row);
        Assert::notNull($localSnapshot, sprintf("Error occured when creating PO Row #%s", $this->getId()));

        $target = $this->makeSnapshot();
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        $defaultParams->setTargetDocVersion($this->getDocVersion());
        $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());

        $event = new PoRowUpdated($target, $defaultParams, $params);
        $this->addEvent($event);

        return $localSnapshot;
    }

    public function post(CommandOptions $options, SharedService $sharedService)
    {
        Assert::eq($this->getDocStatus(), ProcureDocStatus::DRAFT, sprintf("PO is already posted/closed or being amended! %s", $this->getId()));
        Assert::notNull($sharedService, "SharedService service not found");
        Assert::notNull($options, "Comnand Options not found!");

        $validationService = ValidatorFactory::create($sharedService);
        Assert::notNull($validationService, "Validation can not created!");

        $this->validate($validationService, true);

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getErrorMessage());
        }

        $this->clearEvents();

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

        $event = new PoPosted($target, $defaultParams, $params);
        $this->addEvent($event);
        return $this;
    }

    public function makeDetailsDTO()
    {
        $dto = new PoDetailsDTO();
        $dto = DTOFactory::createDTOFrom($this, $dto);

        if (count($this->docRows) > 0) {
            foreach ($this->docRows as $row) {

                if ($row instanceof PORow) {
                    $dto->docRowsDTO[] = $row->makeDetailsDTO();
                }
            }
        }
        return $dto;
    }

    public function makeHeaderDTO()
    {
        $dto = new PoDetailsDTO();
        $dto = DTOFactory::createDTOFrom($this, $dto);
        return $dto;
    }

    public function makeDTOForGrid()
    {
        $dto = new PoDetailsDTO();
        $dto = DTOFactory::createDTOFrom($this, $dto);
        $rowDTOList = [];
        if (count($this->docRows) > 0) {
            foreach ($this->docRows as $row) {

                if ($row instanceof PORow) {
                    $rowDTOList[] = $row->makeDTOForGrid();
                }
            }
        }

        $dto->docRowsDTO = $rowDTOList;
        return $dto;
    }

    public function setRowsOutput($rowsOutput)
    {
        $this->rowsOutput = $rowsOutput;
    }
}