<?php
namespace Inventory\Domain\Item;

use Application\Application\Command\Options\CmdOptions;
use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Application\Domain\Shared\Uom\Uom;
use Application\Domain\Util\Math\Combinition;
use Doctrine\Common\Collections\ArrayCollection;
use Inventory\Application\DTO\Item\ItemDTO;
use Inventory\Domain\Event\Item\ItemVariantsGenerated;
use Inventory\Domain\Item\Collection\ItemPictureCollection;
use Inventory\Domain\Item\Collection\ItemSerialCollection;
use Inventory\Domain\Item\Collection\ItemVariantCollection;
use Inventory\Domain\Item\Contracts\ItemType;
use Inventory\Domain\Item\Repository\ItemCmdRepositoryInterface;
use Inventory\Domain\Item\Statistics\ItemStatistics;
use Inventory\Domain\Item\Variant\Factory\ItemVariantFactory;
use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Validator\Item\ItemValidatorCollection;
use Webmozart\Assert\Assert;
use Closure;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class GenericItem extends BaseItem
{

    protected $statistics;

    protected $variantCollection;

    protected $variantCollectionRef;

    protected $serialCollection;

    protected $serialCollectionRef;

    protected $pictureCollection;

    protected $pictureCollectionRef;

    abstract public function specifyItem();

    /*
     * |=============================
     * | Collection
     * |
     * |=============================
     */

    /**
     *
     * @return \Inventory\Domain\Item\Collection\ItemVariantCollection
     */
    public function getLazyVariantCollection()
    {
        $ref = $this->getVariantCollectionRef();
        if (! $ref instanceof Closure) {
            $this->variantCollection = new ItemVariantCollection();
        } else {
            $this->variantCollection = $ref();
        }

        return $this->variantCollection;
    }

    /**
     *
     * @return \Inventory\Domain\Item\Collection\ItemSerialCollection
     */
    public function getLazySerialCollection()
    {
        $ref = $this->getSerialCollectionRef();
        if (! $ref instanceof Closure) {
            $this->serialCollection = new ItemSerialCollection();
        } else {
            $this->serialCollection = $ref();
        }

        return $this->serialCollection;
    }

    /**
     *
     * @return \Inventory\Domain\Item\Collection\ItemPictureCollection
     */
    public function getLazyPictureCollection()
    {
        $ref = $this->getPictureCollectionRef();
        if (! $ref instanceof Closure) {
            $this->serialCollection = new ItemPictureCollection();
        } else {
            $this->serialCollection = $ref();
        }

        return $this->serialCollection;
    }

    /*
     * |=============================
     * |Variants
     * |
     * |=============================
     */

    /**
     *
     * @param array $input
     * @param CmdOptions $options
     * @param SharedService $sharedService
     * @throws \InvalidArgumentException
     * @return boolean|\Inventory\Domain\Item\Collection\ItemVariantCollection|\Doctrine\Common\Collections\ArrayCollection
     */
    public function generateVariants($input, CmdOptions $options, SharedService $sharedService)
    {
        if ($this->getItemTypeId() == ItemType::SERVICE_ITEM_TYPE) {
            throw new \InvalidArgumentException('Variant is not applied to service items');
        }

        Assert::isArray($input);

        if (count($input) == 0) {
            throw new \InvalidArgumentException('Input for item variants empty!');
        }

        $inputCollection = new ArrayCollection();

        foreach ($input as $a) {
            $inputCollection->add(array_unique(array_map("strtolower", $a)));
        }

        $data = [];

        $productId = '';
        $data[] = [
            $productId
        ];

        foreach ($inputCollection as $k => $attrArray) {
            $data[] = $attrArray;
        }

        $helper = new Combinition();
        $result1 = $helper->getPossibleCombinitionArray($data);

        if ($result1 == null) {
            return false;
        }

        $variantCollection = $this->getLazyVariantCollection();
        foreach ($result1 as $attributes) {
            $variant = ItemVariantFactory::generateVariantFrom($this, $attributes, $options, $sharedService);
            if ($variantCollection->isExits($variant)) {
                throw new \InvalidArgumentException('Variant already exits!');
            }
            $variantCollection->add($variant);
        }

        /**
         *
         * @var ItemCmdRepositoryInterface $rep
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rep->storeVariantCollection($this);

        $target = $this->makeSnapshot();
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new ItemVariantsGenerated($target, $defaultParams, $params);

        $this->clearEvents();
        $this->addEvent($event);
        return $this;
    }

    public function createUom()
    {
        if ($this->getStandardUnitName() == null) {
            return;
        }
        $this->baseUom = new Uom($this->getStandardUnitName());
    }

    public function makeSnapshot()
    {
        $rootSnapshot = GenericObjectAssembler::updateAllFieldsFrom(new ItemSnapshot(), $this);

        if (! $rootSnapshot instanceof ItemSnapshot) {
            throw new \InvalidArgumentException("Root snapshot not created!");
        }

        return $rootSnapshot;
    }

    public function makeDTO()
    {
        return DTOFactory::createDTOFrom($this, new ItemDTO());
    }

    /*
     * |=============================
     * |Validating
     * |
     * |=============================
     */

    /**
     *
     * @param ItemValidatorCollection $validators
     * @throws InvalidArgumentException
     */
    public function validate(ItemValidatorCollection $validators)
    {
        if (! $validators instanceof ItemValidatorCollection) {
            throw new InvalidArgumentException("Validators not given!");
        }
        $validators->validate($this);
    }

    /**
     *
     * @return boolean
     */
    public function isValid()
    {
        $notification = $this->validate();
        return ! $notification->hasErrors();
    }

    /*
     * |=============================
     * |Getter and Setter
     * |
     * |=============================
     */

    /**
     *
     * @return \Inventory\Domain\Item\Collection\ItemVariantCollection|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getVariantCollection()
    {
        if ($this->variantCollection == null) {
            return new ItemVariantCollection();
        }
        return $this->variantCollection;
    }

    /**
     *
     * @param ArrayCollection $variantCollection
     */
    public function setVariantCollection(ItemVariantCollection $variantCollection)
    {
        $this->variantCollection = $variantCollection;
    }

    /**
     *
     * @return Closure
     */
    public function getVariantCollectionRef()
    {
        return $this->variantCollectionRef;
    }

    /**
     *
     * @param Closure $variantCollectionRef
     */
    public function setVariantCollectionRef(Closure $variantCollectionRef)
    {
        $this->variantCollectionRef = $variantCollectionRef;
    }

    /**
     *
     * @return mixed
     */
    public function getSerialCollection()
    {
        return $this->serialCollection;
    }

    /**
     *
     * @param mixed $serialCollection
     */
    public function setSerialCollection($serialCollection)
    {
        $this->serialCollection = $serialCollection;
    }

    /**
     *
     * @return mixed
     */
    public function getSerialCollectionRef()
    {
        return $this->serialCollectionRef;
    }

    /**
     *
     * @param mixed $serialCollectionRef
     */
    public function setSerialCollectionRef($serialCollectionRef)
    {
        $this->serialCollectionRef = $serialCollectionRef;
    }

    /**
     *
     * @return mixed
     */
    public function getPictureCollection()
    {
        return $this->pictureCollection;
    }

    /**
     *
     * @param mixed $pictureCollection
     */
    public function setPictureCollection($pictureCollection)
    {
        $this->pictureCollection = $pictureCollection;
    }

    /**
     *
     * @return Closure
     */
    public function getPictureCollectionRef()
    {
        return $this->pictureCollectionRef;
    }

    /**
     *
     * @param Closure $pictureCollectionRef
     */
    public function setPictureCollectionRef(Closure $pictureCollectionRef)
    {
        $this->pictureCollectionRef = $pictureCollectionRef;
    }

    /**
     *
     * @return \Inventory\Domain\Item\Statistics\ItemStatistics
     */
    public function getStatistics()
    {
        if ($this->statistics == null) {
            $this->statistics = new ItemStatistics();
        }
        return $this->statistics;
    }

    /**
     *
     * @param ItemStatistics $statistics
     */
    public function setStatistics(ItemStatistics $statistics)
    {
        $this->statistics = $statistics;
    }
}