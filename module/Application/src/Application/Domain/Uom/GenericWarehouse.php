<?php
namespace Inventory\Domain\Warehouse;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Inventory\Domain\Event\Warehouse\WhLocationCreated;
use Inventory\Domain\Event\Warehouse\WhLocationUpdated;
use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Service\Contracts\WhValidationServiceInterface;
use Inventory\Domain\Warehouse\Location\BaseLocation;
use Inventory\Domain\Warehouse\Location\GenericLocation;
use Inventory\Domain\Warehouse\Location\LocationSnapshot;
use Inventory\Domain\Warehouse\Repository\WhCmdRepositoryInterface;
use Inventory\Domain\Warehouse\Validator\ValidatorFactory;
use Inventory\Domain\Warehouse\Validator\Contracts\LocationValidatorCollection;
use Inventory\Domain\Warehouse\Validator\Contracts\WarehouseValidatorCollection;
use InvalidArgumentException;
use RuntimeException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GenericWarehouse extends BaseWarehouse
{

    /**
     *
     * @param LocationSnapshot $snapshot
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @throws InvalidArgumentException
     * @throws \RuntimeException
     * @throws RuntimeException
     * @return \Inventory\Domain\Warehouse\Location\LocationSnapshot
     */
    public function createLocationFrom(LocationSnapshot $snapshot, CommandOptions $options, SharedService $sharedService)
    {
        if ($snapshot == null) {
            throw new InvalidArgumentException("Row Snapshot not found");
        }

        if ($options == null) {
            throw new InvalidArgumentException("Options not found");
        }

        $validationService = ValidatorFactory::create($sharedService, ValidatorFactory::CREATE_NEW_LOCATION);

        if (! $validationService->getLocationValidators() instanceof LocationValidatorCollection) {
            throw new InvalidArgumentException("Location Validators not given!");
        }

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $snapshot->init($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        $location = GenericLocation::makeFromSnapshot($snapshot);

        $this->validateLocation($location, $validationService->getRowValidators());

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getNotification()->errorMessage());
        }

        $this->clearEvents();

        /**
         *
         * @var LocationSnapshot $localSnapshot ;
         * @var WhCmdRepositoryInterface $rep ;
         */

        $rep = $sharedService->getPostingService()->getCmdRepository();
        $localSnapshot = $rep->storeLocation($this, $location);

        if ($localSnapshot == null) {
            throw new RuntimeException(sprintf("Error occured when creating warehouse location #%s", $this->getId()));
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
        $event = new WhLocationCreated($target, $defaultParams, $params);
        $this->addEvent($event);

        return $localSnapshot;
    }

    /**
     *
     * @param LocationSnapshot $snapshot
     * @param CommandOptions $options
     * @param array $params
     * @param SharedService $sharedService
     * @throws InvalidArgumentException
     * @throws \RuntimeException
     * @throws RuntimeException
     * @return \Inventory\Domain\Transaction\TrxRowSnapshot
     */
    public function updateLocationFrom(LocationSnapshot $snapshot, CommandOptions $options, $params, SharedService $sharedService)
    {
        if ($snapshot == null) {
            throw new InvalidArgumentException("LocationSnapshot not found");
        }

        if ($options == null) {
            throw new InvalidArgumentException("Options not found");
        }

        $validationService = ValidatorFactory::create($sharedService, ValidatorFactory::EDIT_LOCATION);

        if (! $validationService->getLocationValidators() instanceof LocationValidatorCollection) {
            throw new InvalidArgumentException("Location Validators not given!");
        }

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $snapshot->updateSnapshot($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        $location = GenericLocation::makeFromSnapshot($snapshot);

        $this->validateLocation($location, $validationService->getLocationValidators());

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getNotification()->errorMessage());
        }

        $this->clearEvents();

        /**
         *
         * @var LocationSnapshot $localSnapshot ;
         * @var WhCmdRepositoryInterface $rep ;
         */

        $rep = $sharedService->getPostingService()->getCmdRepository();
        $localSnapshot = $rep->storeLocation($this, $location);

        if ($localSnapshot == null) {
            throw new RuntimeException(sprintf("Error occured when updatting warehouse location #%s", $this->getId()));
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
        $event = new WhLocationUpdated($target, $defaultParams, $params);
        $this->addEvent($event);

        return $localSnapshot;
    }

    /**
     *
     * @param WhValidationServiceInterface $validationService
     * @param boolean $isPosting
     * @throws InvalidArgumentException
     * @return \Inventory\Domain\Warehouse\GenericWarehouse
     */
    public function validate(WhValidationServiceInterface $validationService, $isPosting = false)
    {
        if ($validationService == null) {
            throw new InvalidArgumentException("Validation service not given!");
        }

        if (! $validationService->getWarehouseValidators() instanceof WarehouseValidatorCollection) {
            throw new InvalidArgumentException("WH Validators not given!");
        }

        if (! $validationService->getLocationValidators() instanceof LocationValidatorCollection) {
            throw new InvalidArgumentException("Location Validators not given!");
        }

        // Clear Notification.
        $this->clearNotification();

        $this->validateWarehouse($validationService->getWarehouseValidators(), $isPosting);

        if ($this->hasErrors()) {
            return $this;
        }

        if (count($this->getLocations()) == 0) {
            $this->addError("Warehouse has no default location");
            return $this;
        }

        foreach ($this->getLocations() as $location) {
            $this->validateLocation($location, $validationService->getLocationValidators(), $isPosting);
        }

        return $this;
    }

    /**
     *
     * @param WarehouseValidatorCollection $validators
     * @param boolean $isPosting
     */
    protected function validateWarehouse(WarehouseValidatorCollection $validators, $isPosting = false)
    {
        $validators->validate($this);
    }

    /**
     *
     * @param BaseLocation $location
     * @param LocationValidatorCollection $validators
     * @param boolean $isPosting
     */
    protected function validateLocation(BaseLocation $location, LocationValidatorCollection $validators, $isPosting = false)
    {
        $validators->validate($this, $location);
    }

    /**
     *
     * @return NULL|object
     */
    public function makeSnapshot()
    {
        return SnapshotAssembler::createSnapshotFrom($this, new WarehouseSnapshot());
    }
}