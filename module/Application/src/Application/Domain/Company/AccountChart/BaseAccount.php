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

    /**
     *
     * @return \Application\Domain\Company\AccountChart\BaseAccountSnapshot
     */
    public function makeSnapshot()
    {
        $snapshot = new BaseAccountSnapshot();
        GenericObjectAssembler::updateAllFieldsFrom($this, $snapshot);
        return $snapshot;
    }
}
