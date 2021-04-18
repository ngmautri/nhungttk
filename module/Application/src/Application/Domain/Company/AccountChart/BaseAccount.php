<?php
namespace Application\Domain\Company\AccountChart;

use Application\Domain\Shared\Assembler\GenericObjectAssembler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BaseAccount extends AbstractAccount
{

    public function equals(BaseAccount $other)
    {
        if ($other == null) {
            return false;
        }

        return \strtolower(trim($this->getAccountNumer())) == \strtolower(trim($other->getAccountNumer()));
    }

    /**
     *
     * @return \Application\Domain\Company\AccountChart\BaseAccountSnapshot
     */
    public function makeSnapshot()
    {
        $snapshot = new BaseAccountSnapshot();
        GenericObjectAssembler::updateAllFieldsFrom($snapshot, $this);
        return $snapshot;
    }
}
