<?php
namespace Inventory\Domain\Item;

use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Application\Domain\Shared\Uom\Uom;
use Application\Domain\Util\Math\Combinition;
use Doctrine\Common\Collections\ArrayCollection;
use Inventory\Application\DTO\Item\ItemDTO;
use Inventory\Domain\Item\Collection\ItemVariantCollection;
use Inventory\Domain\Validator\Item\ItemValidatorCollection;
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
     * @return \Application\Domain\Company\Collection\ItemAttributeGroupCollection|mixed
     */
    public function getLazyVariantCollection()
    {
        $ref = $this->getItemAttributeCollectionRef();
        if (! $ref instanceof Closure) {
            return new ItemVariantCollection();
        }

        $this->variantCollection = $ref();
        return $this->variantCollection;
    }

    /**
     *
     * @param ArrayCollection $input
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function generateVariants(ArrayCollection $input)
    {
        if ($input->isEmpty()) {
            throw new \InvalidArgumentException("No attribute date provided");
        }

        $data = [];

        $productId = '';
        $data[] = [
            $productId
        ];

        foreach ($input as $k => $attrArray) {
            $data[] = $attrArray;
        }

        $helper = new Combinition();
        $result1 = $helper->getPossibleCombinitionArray($data);

        $result['ItemId'] = $this->getId();
        $result['Variants'] = $result1;
        return $result;
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