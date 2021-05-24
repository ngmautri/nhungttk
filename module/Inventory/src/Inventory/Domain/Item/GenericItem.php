<?php
namespace Inventory\Domain\Item;

use Application\Application\Command\Options\CmdOptions;
use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Application\Domain\Shared\Uom\Uom;
use Application\Domain\Util\Math\Combinition;
use Doctrine\Common\Collections\ArrayCollection;
use Inventory\Application\DTO\Item\ItemDTO;
use Inventory\Domain\Item\Collection\ItemVariantCollection;
use Inventory\Domain\Item\Repository\ItemCmdRepositoryInterface;
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

    private $variantCollection;

    private $variantCollectionRef;

    abstract public function specifyItem();

    /*
     * |=============================
     * | Update Snapshot from Array
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
     * @param array $input
     * @param CmdOptions $options
     * @param SharedService $sharedService
     * @throws \InvalidArgumentException
     * @return boolean|\Inventory\Domain\Item\Collection\ItemVariantCollection|\Doctrine\Common\Collections\ArrayCollection
     */
    public function generateVariants($input, CmdOptions $options, SharedService $sharedService)
    {
        Assert::isArray($input);
        if (count($input) == 0) {
            throw new \InvalidArgumentException('Input for item variant empty!');
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
                continue;
            }
            $variantCollection->add($variant);
        }

        /**
         *
         * @var ItemCmdRepositoryInterface $rep
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rep->storeVariantCollection($this);
        return $this->getVariantCollection();
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
}