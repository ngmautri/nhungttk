<?php
namespace Application\Domain\Company\Brand;

use Application\Domain\Company\Brand\Validator\Contracts\BrandValidatorCollection;
use Application\Domain\Company\ItemAttribute\BaseAttribute;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BaseBrand extends AbstractBrand
{

    public function validateBrand(BrandValidatorCollection $validators, $isPosting = false)
    {
        $validators->validate($this);

        if ($this->hasErrors()) {
            $this->addErrorArray($this->getErrors());
        }
    }

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

        return \strtolower(trim($this->getBrandName())) == \strtolower(trim($other->getBrandName()));
    }

    /**
     *
     * @return \Application\Domain\Company\ItemAttribute\BaseAttributeSnapshot
     */
    public function makeSnapshot()
    {
        $snapshot = new BaseBrandSnapshot();
        GenericObjectAssembler::updateAllFieldsFrom($snapshot, $this);
        return $snapshot;
    }
}
