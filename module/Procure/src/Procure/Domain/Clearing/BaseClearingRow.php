<?php
namespace Procure\Domain\Clearing;

use Application\Domain\Shared\Assembler\GenericObjectAssembler;
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

    public static function createFromSnapshot(BaseClearingDoc $rootDoc, ClearingRowSnapshot $snapshot)
    {
        Assert::isInstanceOf($rootDoc, BaseClearingDoc::class, "Clearing Doc is required!");
        Assert::isInstanceOf($snapshot, ClearingRowSnapshot::class, "Clearing row snapshot is required!");

        $instance = new self();

        GenericObjectAssembler::updateAllFieldsFrom($instance, $snapshot);
        // $instance->createVO($rootDoc);
        return $instance;
    }

    public function __construct(BaseRow $row, BaseRow $counterRow, $clearingQuantity = 0)
    {
        Assert::notNull($row);
        Assert::notNull($counterRow);

        $this->row = $row;
        $this->counterRow .= $this->row = $row;
        $this->setClearingStandardQuantity($clearingQuantity);
    }

    public function makeSnapshot()
    {
        return GenericObjectAssembler::updateAllFieldsFrom(new ClearingRowSnapshot(), $this);
    }
}
