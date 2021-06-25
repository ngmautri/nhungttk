<?php
namespace Inventory\Domain\Item;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Company\Repository\CompanyCmdRepositoryInterface;
use Application\Domain\Shared\Command\CommandOptions;
use Inventory\Domain\Event\Warehouse\WhLocationCreated;
use Inventory\Domain\Item\Component\ComponentSnapshot;
use Inventory\Domain\Item\Contracts\ItemType;
use Inventory\Domain\Item\Validator\ValidatorFactory;
use Inventory\Domain\Warehouse\Location\GenericLocation;
use Inventory\Domain\Warehouse\Location\LocationSnapshot;
use Inventory\Domain\Warehouse\Tree\LocationNode;
use Inventory\Domain\Warehouse\Validator\Contracts\LocationValidatorCollection;
use Webmozart\Assert\Assert;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ComponentItem extends GenericItem
{

    private $componentCollection;

    private $componentCollectionRef;

    public function addComponentFrom(ComponentSnapshot $snapshot, CommandOptions $options, \Application\Domain\Service\Contracts\SharedServiceInterface $sharedService)
    {
        Assert::notNull($snapshot, "LocationSnapshot not founds");
        Assert::notNull($options, "Options not founds");
        Assert::notNull($sharedService, "SharedService not found");

        $validationService = ValidatorFactory::create($sharedService, ValidatorFactory::CREATE_NEW_LOCATION);

        if (! $validationService->getLocationValidators() instanceof LocationValidatorCollection) {
            throw new InvalidArgumentException("Location Validators not given!");
        }

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $snapshot->init($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        $location = GenericLocation::createFromSnapshot($this, $snapshot);

        $this->_ensureNotDefaultLocation($location);

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

    public function __construct()
    {
        $this->setItemType("ITEM");
        $this->setItemTypeId(ItemType::COMPOSITE_ITEM);
        // $this->setIsStocked(1);
        // $this->setIsFixedAsset(0);
        // $this->setIsSparepart(1);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\GenericItem::specifyItem()
     */
    public function specifyItem()
    {
        $this->setItemType("ITEM");
        $this->setItemTypeId(ItemType::COMPOSITE_ITEM);
    }

    /**
     *
     * @return mixed
     */
    public function getComponentCollection()
    {
        return $this->componentCollection;
    }

    /**
     *
     * @param mixed $componentCollection
     */
    public function setComponentCollection($componentCollection)
    {
        $this->componentCollection = $componentCollection;
    }

    /**
     *
     * @return mixed
     */
    public function getComponentCollectionRef()
    {
        return $this->componentCollectionRef;
    }

    /**
     *
     * @param mixed $componentCollectionRef
     */
    public function setComponentCollectionRef($componentCollectionRef)
    {
        $this->componentCollectionRef = $componentCollectionRef;
    }
}