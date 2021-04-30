<?php
namespace Inventory\Domain\Warehouse;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Company\AccountChart\Validator\ChartValidatorFactory;
use Application\Domain\Company\Repository\CompanyCmdRepositoryInterface;
use Application\Domain\Service\SharedService;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Application\Domain\Util\Translator;
use Doctrine\Common\Collections\ArrayCollection;
use Inventory\Domain\Event\Warehouse\WhLocationCreated;
use Inventory\Domain\Event\Warehouse\WhLocationRemoved;
use Inventory\Domain\Event\Warehouse\WhLocationUpdated;
use Inventory\Domain\Service\Contracts\WhValidationServiceInterface;
use Inventory\Domain\Warehouse\Contracts\DefaultLocation;
use Inventory\Domain\Warehouse\Location\BaseLocation;
use Inventory\Domain\Warehouse\Location\BaseLocationSnapshot;
use Inventory\Domain\Warehouse\Location\GenericLocation;
use Inventory\Domain\Warehouse\Location\LocationSnapshot;
use Inventory\Domain\Warehouse\Location\LocationSnapshotAssembler;
use Inventory\Domain\Warehouse\Repository\WhCmdRepositoryInterface;
use Inventory\Domain\Warehouse\Tree\DefaultLocationTree;
use Inventory\Domain\Warehouse\Tree\LocationNode;
use Inventory\Domain\Warehouse\Validator\ValidatorFactory;
use Inventory\Domain\Warehouse\Validator\Contracts\LocationValidatorCollection;
use Inventory\Domain\Warehouse\Validator\Contracts\WarehouseValidatorCollection;
use Webmozart\Assert\Assert;
use Closure;
use InvalidArgumentException;
use RuntimeException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BaseWarehouse extends AbstractWarehouse
{

    private $locationCollection;

    private $locationCollectionRef;

    // Addtional Attributes
    protected $locationList;

    protected $locations;

    protected $rootLocation;

    protected $returnLocation;

    protected $scrapLocation;

    protected $recycleLocation;

    /**
     *
     * @param BaseWarehouse $other
     * @return boolean
     */
    public function equals(BaseWarehouse $other)
    {
        if ($other == null) {
            return false;
        }

        return \strtolower(trim($this->getWhCode())) == \strtolower(trim($other->getWhCode()));
    }

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
    public function createLocationFrom(LocationSnapshot $snapshot, CommandOptions $options, \Application\Domain\Service\Contracts\SharedServiceInterface $sharedService)
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

        $location = GenericLocation::createFromSnapshot($this, $snapshot);

        // Add to tree, in order to check.
        // create node
        $tree = $this->createLocationTree();
        $root = $tree->getRoot();

        $parent = $root;
        if ($root->getNodeByCode($location->getParentCode()) != null) {
            $parent = $root->getNodeByCode($location->getParentCode());
        }

        $node = new LocationNode();

        $node->setParentCode($snapshot->getParentCode());
        $node->setParentId($parent->getId());

        $node->setContextObject($snapshot);
        $node->setId($snapshot->getLocationCode());
        $node->setNodeCode($snapshot->getLocationCode());
        $node->setNodeName($snapshot->getLocationName());
        $node->setParentId($parent->getId());

        // Adding to tree. If ok, go further
        $tree->insertLocation($node, $parent, $options);

        $this->validateLocation($location, $validationService->getLocationValidators());

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getNotification()->errorMessage());
        }

        $this->clearEvents();

        /**
         *
         * @var LocationSnapshot $localSnapshot ;
         * @var CompanyCmdRepositoryInterface $rep ;
         */

        $rep = $sharedService->getPostingService()->getCmdRepository();
        $localSnapshot = $rep->storeLocation($this, $location);

        $params = [
            "rowId" => $localSnapshot->getId(),
            "rowToken" => $localSnapshot->getToken()
        ];

        $target = $this;

        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        // $defaultParams->setTargetDocVersion($this->getDocVersion());
        $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $event = new WhLocationCreated($target, $defaultParams, $params);
        $this->addEvent($event);

        return $localSnapshot;
    }

    /**
     *
     * @param BaseLocation $location
     * @param BaseLocationSnapshot $snapshot
     * @param CommandOptions $options
     * @param array $params
     * @param \Application\Domain\Service\Contracts\SharedServiceInterface $sharedService
     * @throws InvalidArgumentException
     * @throws \RuntimeException
     * @return \Inventory\Domain\Warehouse\Location\LocationSnapshot
     */
    public function updateLocationFrom(BaseLocation $location, BaseLocationSnapshot $snapshot, CommandOptions $options, $params, \Application\Domain\Service\Contracts\SharedServiceInterface $sharedService)
    {
        if ($location == null) {
            throw new InvalidArgumentException("BaseLocation not found");
        }
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

        // try to change code. If ok, go further
        $tree = $this->createLocationTree();
        $root = $tree->getRoot();

        $node = $root->getNodeByCode($location->getLocationCode());
        $tree->changeLocationCode($node, $snapshot->getLocationCode(), $options);

        LocationSnapshotAssembler::updateDefaultFieldsFrom($location, $snapshot);

        $this->validateLocation($location, $validationService->getLocationValidators());

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getNotification()->errorMessage());
        }

        $this->clearEvents();

        /**
         *
         * @var LocationSnapshot $localSnapshot ;
         * @var CompanyCmdRepositoryInterface $rep ;
         */

        $rep = $sharedService->getPostingService()->getCmdRepository();
        $localSnapshot = $rep->storeLocation($this, $location);

        $target = null;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        // $defaultParams->setTargetDocVersion($this->getDocVersion());
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

        if ($this->getLocationCollection()->isEmpty()) {
            $this->addError("Warehouse must have no default locations!");
            return $this;
        }

        foreach ($this->getLocationCollection() as $location) {
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
     * @return object
     */
    public function makeSnapshot()
    {
        return GenericObjectAssembler::updateAllFieldsFrom(new WarehouseSnapshot(), $this);
    }

    /**
     *
     * @return \Inventory\Domain\Warehouse\Tree\DefaultLocationTree
     */
    public function createLocationTree()
    {
        $tree = new DefaultLocationTree($this);
        $tree->initTree();
        $tree->createTree($this->getWhCode(), 0);
        return $tree;
    }

    public function store(SharedService $sharedService)
    {
        Assert::notNull($sharedService, Translator::translate(sprintf("Shared Service not set! %s", __FUNCTION__)));

        /**
         *
         * @var WhCmdRepositoryInterface $rep
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();

        $this->setLogger($sharedService->getLogger());
        $validationService = ChartValidatorFactory::forCreatingChart($sharedService);

        $this->validate($validationService);
        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getErrorMessage());
        }

        $rep->store($this);

        $this->logInfo(\sprintf("Warehouse saved %s", __METHOD__));
        return $this;
    }

    /**
     *
     * @param BaseLocation $localEntity
     * @param CommandOptions $options
     * @param \Application\Domain\Service\Contracts\SharedServiceInterface $sharedService
     * @return \Inventory\Domain\Warehouse\BaseWarehouse
     */
    public function removeLocation(BaseLocation $localEntity, CommandOptions $options, \Application\Domain\Service\Contracts\SharedServiceInterface $sharedService)
    {
        Assert::notNull($localEntity, "BaseLocation not found");
        Assert::notNull($options, "Options not founds");
        Assert::notNull($sharedService, "SharedService not found");

        if (\in_array($localEntity->getLocationCode(), \Inventory\Domain\Warehouse\Contracts\DefaultLocation::get())) {
            throw new \InvalidArgumentException(Translator::translate("Default location can not be changed or removed!"));
        }

        /**
         *
         * @var CompanyCmdRepositoryInterface $rep ;
         *
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rep->removeLocation($this, $localEntity);

        $params = null;

        $target = $localEntity->makeSnapshot();
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($localEntity->getId());
        $defaultParams->setTargetToken($this->getToken());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());

        $event = new WhLocationRemoved($target, $defaultParams, $params);
        $this->addEvent($event);

        return $this;
    }

    public function getLocationById($id)
    {
        if ($id == null) {
            return null;
        }
        $collection = $this->getLazyLocationCollection();
        if ($collection->isEmpty()) {
            return null;
        }

        foreach ($collection as $e) {
            /**
             *
             * @var BaseLocation $e
             */
            if ($e->getId() == $id) {
                return $e;
            }
        }

        return null;
    }

    public function getLocationByCode($number)
    {
        if ($number == null) {
            return null;
        }

        $collection = $this->getLazyLocationCollection();

        if ($collection->isEmpty()) {
            // throw new \InvalidArgumentException("Location number [$number] not found!");
            return null;
        }

        foreach ($collection as $e) {
            /**
             *
             * @var BaseLocation $e
             */
            if (\strtolower(trim($e->getLocationCode())) == \strtolower(trim($number))) {
                return $e;
            }
        }

        // throw new \InvalidArgumentException("Location number [$number] not found!");
        return null;
    }

    /**
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getLazyLocationCollection()
    {
        $ref = $this->getLocationCollectionRef();
        if (! $ref instanceof Closure) {
            return new ArrayCollection();
        }

        $this->locationCollection = $ref();
        return $this->locationCollection;
    }

    public function addLocation(BaseLocation $location)
    {
        if (! $location instanceof GenericLocation) {
            throw new InvalidArgumentException("Input not invalid! GenericLocation");
        }
        $locations = $this->getLocationCollection();
        $locations->add($location);
        $this->locationCollection = $locations;
    }

    /**
     *
     * @return mixed
     */
    public function getLocations()
    {
        return $this->locations;
    }

    /**
     *
     * @return NULL|\Inventory\Domain\Warehouse\Location\BaseLocation
     */
    public function getRootLocation()
    {
        if ($this->getLocationCollection()->isEmpty()) {
            return null;
        }

        foreach ($this->getLocationCollection() as $location) {
            /**
             *
             * @var BaseLocation $location ;
             */
            if ($location->getLocationCode() == DefaultLocation::ROOT_LOCATION) {
                return $location;
            }
        }

        return null;
    }

    /**
     *
     * @return NULL|\Inventory\Domain\Warehouse\Location\BaseLocation
     */
    public function getReturnLocation()
    {
        if ($this->getLocationCollection()->isEmpty()) {
            return null;
        }

        foreach ($this->getLocationCollection() as $location) {
            /**
             *
             * @var BaseLocation $location ;
             */
            if ($location->getLocationCode() == DefaultLocation::RETURN_LOCATION) {
                return $location;
            }
        }

        return null;
    }

    /**
     *
     * @return NULL|\Inventory\Domain\Warehouse\Location\BaseLocation
     */
    public function getScrapLocation()
    {
        if ($this->getLocationCollection()->isEmpty()) {
            return null;
        }

        foreach ($this->getLocationCollection() as $location) {
            /**
             *
             * @var BaseLocation $location ;
             */
            if ($location->getLocationCode() == DefaultLocation::SCRAP_LOCATION) {
                return $location;
            }
        }

        return null;
    }

    /**
     *
     * @return mixed
     */
    public function getRecycleLocation()
    {
        if ($this->getLocationCollection()->isEmpty()) {
            return null;
        }

        foreach ($this->getLocationCollection() as $location) {
            /**
             *
             * @var BaseLocation $location ;
             */
            if ($location->getLocationCode() == DefaultLocation::RECYCLE_LOCATION) {
                return $location;
            }
        }

        return null;
    }

    // ==========================================
    // ========== Setter and Getter ============
    // ==========================================

    /**
     *
     * @param mixed $locations
     */
    public function setLocations($locations)
    {
        $this->locations = $locations;
    }

    /**
     *
     * @return mixed
     */
    public function getLocationCollection()
    {
        if ($this->locationCollection == null) {
            return new ArrayCollection();
        }
        return $this->locationCollection;
    }

    /**
     *
     * @return mixed
     */
    public function getLocationCollectionRef()
    {
        return $this->locationCollectionRef;
    }

    /**
     *
     * @param mixed $locationCollection
     */
    public function setLocationCollection($locationCollection)
    {
        $this->locationCollection = $locationCollection;
    }

    /**
     *
     * @param mixed $locationCollectionRef
     */
    public function setLocationCollectionRef($locationCollectionRef)
    {
        $this->locationCollectionRef = $locationCollectionRef;
    }
}