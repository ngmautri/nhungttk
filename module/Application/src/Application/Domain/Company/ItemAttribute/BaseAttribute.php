<?php
namespace Application\Domain\Company\ItemAttribute;

use Application\Domain\Shared\Assembler\GenericObjectAssembler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BaseAttribute extends AbstractAttribute
{

    /**
     *
     * @param BaseAttribute $other
     * @return boolean
     */
    public function equals(BaseAttribute $other)
    {
        if ($other == null) {
            return false;
        }

        return \strtolower(trim($this->getAttributeName())) == \strtolower(trim($other->getAttributeName()));
    }

    /**
     *
     * @return \Application\Domain\Company\ItemAttribute\BaseAttributeSnapshot
     */
    public function makeSnapshot()
    {
        $snapshot = new BaseAttributeSnapshot();
        GenericObjectAssembler::updateAllFieldsFrom($snapshot, $this);
        return $snapshot;
    }
}
