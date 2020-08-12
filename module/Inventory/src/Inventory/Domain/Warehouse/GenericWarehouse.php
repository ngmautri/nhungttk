<?php
namespace Inventory\Domain\Warehouse;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\Constants;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Inventory\Domain\Event\Transaction\TrxRowAdded;
use Inventory\Domain\Event\Transaction\TrxRowUpdated;
use Inventory\Domain\Exception\InvalidOperationException;
use Inventory\Domain\Item\Validator\ValidatorFactory;
use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Service\Contracts\WhValidationServiceInterface;
use Inventory\Domain\Transaction\TrxRow;
use Inventory\Domain\Transaction\TrxRowSnapshot;
use Inventory\Domain\Transaction\Validator\Contracts\HeaderValidatorCollection;
use Inventory\Domain\Transaction\Validator\Contracts\RowValidatorCollection;
use Inventory\Domain\Warehouse\Location\LocationSnapshot;
use InvalidArgumentException;
use RuntimeException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GenericWarehouse extends BaseWarehouse
{

    public function createLocationFrom(LocationSnapshot $snapshot, CommandOptions $options, SharedService $sharedService)
    {
        if ($this->getDocStatus() == Constants::DOC_STATUS_POSTED) {
            throw new InvalidOperationException(sprintf("PR is posted! %s", $this->getId()));
        }

        if ($snapshot == null) {
            throw new InvalidArgumentException("Row Snapshot not found");
        }

        if ($options == null) {
            throw new InvalidArgumentException("Options not found");
        }

        $validationService = ValidatorFactory::create($this->getMovementType(), $sharedService);

        $this->_checkParams($validationService, $sharedService);

        if (! $validationService->getRowValidators() instanceof RowValidatorCollection) {
            throw new InvalidArgumentException("Row Validators not given!");
        }

        $snapshot->flow = $this->getMovementFlow();
        $snapshot->wh = $this->getWarehouse();

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $snapshot->initSnapshot($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        $row = TrxRow::makeFromSnapshot($snapshot);

        $this->validateRow($row, $validationService->getRowValidators());

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getNotification()->errorMessage());
        }

        $this->recordedEvents = array();

        /**
         *
         * @var TrxRowSnapshot $localSnapshot
         */
        $localSnapshot = $sharedService->getPostingService()
            ->getCmdRepository()
            ->storeRow($this, $row);

        if ($localSnapshot == null) {
            throw new RuntimeException(sprintf("Error occured when creating row #%s", $this->getId()));
        }

        $params = [
            "rowId" => $localSnapshot->getId(),
            "rowToken" => $localSnapshot->getToken()
        ];

        $target = $this;

        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        $defaultParams->setTargetDocVersion($this->getDocVersion());
        $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $event = new TrxRowAdded($target, $defaultParams, $params);
        $this->addEvent($event);

        return $localSnapshot;
    }

    public function updateLocationFrom(LocationSnapshot $snapshot, CommandOptions $options, $params, SharedService $sharedService)
    {
        if ($this->getDocStatus() == Constants::DOC_STATUS_POSTED) {
            throw new InvalidOperationException(sprintf("Trx is posted! %s", $this->getId()));
        }

        if ($snapshot == null) {
            throw new InvalidArgumentException("TrxRowSnapshot not found");
        }

        if ($options == null) {
            throw new InvalidArgumentException("Options not found");
        }

        $validationService = ValidatorFactory::create($this->getMovementType(), $sharedService);

        $this->_checkParams($validationService, $sharedService);

        if (! $validationService->getRowValidators() instanceof RowValidatorCollection) {
            throw new InvalidArgumentException("Row Validators not given!");
        }

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $snapshot->updateSnapshot($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        $row = TrxRow::makeFromSnapshot($snapshot);

        $this->validateRow($row, $validationService->getRowValidators());

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getNotification()->errorMessage());
        }

        $this->recordedEvents = array();

        /**
         *
         * @var TrxRowSnapshot $localSnapshot
         */
        $localSnapshot = $sharedService->getPostingService()
            ->getCmdRepository()
            ->storeRow($this, $row);

        if ($localSnapshot == null) {
            throw new RuntimeException(sprintf("Error occured when updatting Trx row #%s", $this->getId()));
        }

        // $target = $this->makeSnapshot(); //

        $target = null;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        $defaultParams->setTargetDocVersion($this->getDocVersion());
        $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $event = new TrxRowUpdated($target, $defaultParams, $params);

        $this->addEvent($event);

        return $localSnapshot;
    }

    public function validate(WhValidationServiceInterface $validationService, $isPosting = false)
    {
        if ($validationService == null) {
            throw new InvalidArgumentException("Validation service not given!");
        }

        if (! $validationService->getHeaderValidators() instanceof HeaderValidatorCollection) {
            throw new InvalidArgumentException("Headers Validators not given!");
        }

        if (! $validationService->getRowValidators() instanceof RowValidatorCollection) {
            throw new InvalidArgumentException("Headers Validators not given!");
        }

        // Clear Notification.
        $this->clearNotification();

        $this->validateHeader($validationService->getHeaderValidators(), $isPosting);

        if ($this->hasErrors()) {
            return $this;
        }

        if (count($this->getDocRows()) == 0) {
            $this->addError("Documment is empty. Please add line!");
            return $this;
        }

        foreach ($this->getDocRows() as $row) {
            $this->validateRow($row, $validationService->getRowValidators(), $isPosting);
        }

        return $this;
    }

    public function makeSnapshot()
    {
        return SnapshotAssembler::createSnapshotFrom($this, new WarehouseSnapshot());
    }
}