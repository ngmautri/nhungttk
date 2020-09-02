<?php
namespace Inventory\Application\Export\Transaction;

use Application\Application\Service\Document\Pdf\AbstractBuilder;
use Inventory\Application\Export\Transaction\Contracts\AbstractSaveAs;
use Inventory\Application\Export\Transaction\Contracts\RowsSaveAsInterface;

/**
 * Director in Builder Pattern.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractRowsSaveAsPdf extends AbstractSaveAs implements RowsSaveAsInterface
{

    protected $builder;

    /**
     *
     * @param AbstractBuilder $builder
     */
    public function __construct(AbstractBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     *
     * @return \Application\Application\Service\Document\Pdf\AbstractBuilder
     */
    public function getBuilder()
    {
        return $this->builder;
    }
}
