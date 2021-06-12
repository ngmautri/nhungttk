<?php
namespace Application\Domain\Company\AccessControl;

use Application\Domain\Company\ItemAttribute\BaseAttributeSnapshot;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BaseRole extends AbstractRole
{

    public function equals(BaseRole $other)
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
