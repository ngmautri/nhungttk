<?php
namespace Inventory\Domain\Item;

use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Application\Domain\Shared\Uom\Uom;
use Application\Domain\Util\Math\Combinition;
use Doctrine\Common\Collections\ArrayCollection;
use Inventory\Application\DTO\Item\ItemDTO;
use Inventory\Domain\Validator\Item\ItemValidatorCollection;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class GenericItem extends BaseItem
{

    abstract public function specifyItem();

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
        $combinations = $helper->getPossibleCombinitions($data);
        $result['ItemId'] = $this->getId();

        $result1 = [];
        foreach ($combinations as $c) {
            $attributeArray = \explode(';', $c);

            $tmp = [];
            foreach ($attributeArray as $a) {
                if ($a == null) {
                    continue;
                }
                $tmp[] = $a;
            }
            $result1[] = $tmp;
        }
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
}