<?php
namespace Application\Domain\Company\Brand;

use Application\Domain\Company\ItemAttribute\BaseAttribute;
use Application\Domain\Company\ItemAttribute\BaseAttributeSnapshot;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BaseBrand extends AbstractBrand
{

    /**
     *
     * @param BaseAttribute $other
     * @return boolean
     */
    public function equals(BaseBrand $other)
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
