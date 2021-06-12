<?php
namespace Application\Domain\Company\ItemAssociation;

use Application\Domain\Company\ItemAttribute\BaseAttribute;
use Application\Domain\Company\ItemAttribute\BaseAttributeSnapshot;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BaseAssociation extends AbstractAssociation
{

    /**
     *
     * @param BaseAttribute $other
     * @return boolean
     */
    public function equals(BaseAssociation $other)
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
