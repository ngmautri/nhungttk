<?php
namespace Inventory\Application\Export\Item;

use Application\Application\Service\Document\Spreadsheet\AbstractBuilder;
use Inventory\Application\Export\Item\Contracts\SaveAsInterface;

/**
 * Director in Builder Pattern.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractSaveAsSpreadsheet implements SaveAsInterface
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
     * @return \Application\Application\Service\Document\Spreadsheet\AbstractBuilder
     */
    public function getBuilder()
    {
        return $this->builder;
    }
}
