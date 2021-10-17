<?php
namespace Procure\Domain\Clearing;

use Procure\Domain\BaseRow;
use Webmozart\Assert\Assert;

/**
 * Abstract Clearing Row
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class BaseClearingRow extends AbstractClearingRow
{

    private $row;

    private $counterRow;

    public function __construct(BaseRow $row, BaseRow $counterRow, $clearingQuantity = 0)
    {
        Assert::notNull($row);
        Assert::notNull($counterRow);

        $this->row = $row;
        $this->counterRow .= $this->row = $row;
        $this->setClearingStandardQuantity($clearingQuantity);
    }

    public function makeSnapshot()
    {}
}
