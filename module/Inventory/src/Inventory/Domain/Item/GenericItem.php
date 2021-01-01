<?php
namespace Inventory\Domain\Item;

use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\SnapshotAssembler;
use Inventory\Application\DTO\Item\ItemDTO;
use Inventory\Domain\Validator\Item\ItemValidatorCollection;
use InvalidArgumentException;
use Application\Domain\Shared\Uom\Uom;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class GenericItem extends BaseItem
{

    abstract public function specifyItem();

    public function createUom()
    {
        if ($this->getStandardUnitName() == null) {
            return;
        }
        $this->baseUom = new Uom($this->getStandardUnitName());
    }

    public function makeSnapshot()
    {
        return GenericObjectAssembler::updateAllFieldsFrom(new ItemSnapshot(), $this);
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