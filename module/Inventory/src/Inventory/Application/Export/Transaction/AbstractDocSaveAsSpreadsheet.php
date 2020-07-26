<?php
namespace Inventory\Application\Export\Transaction;

use Application\Application\Service\Document\Spreadsheet\AbstractBuilder;
use Inventory\Application\Export\Transaction\Contracts\DocSaveAsInterface;

/**
 * Director in Builder Pattern.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractDocSaveAsSpreadsheet implements DocSaveAsInterface
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
