<?php
namespace Application\Domain\Company\AccountChart;

use Application\Domain\Shared\Assembler\GenericObjectAssembler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BaseChart extends AbstractChart
{

    /**
     *
     * @return \Application\Domain\Company\AccountChart\BaseChartSnapshot
     */
    public function makeSnapshot()
    {
        $snapshot = new BaseChartSnapshot();
        GenericObjectAssembler::updateAllFieldsFrom($this, $snapshot);
        return $snapshot;
    }
}
