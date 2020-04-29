<?php
namespace Procure\Application\Service\Output\Header;

use Application\Application\Service\Document\Spreadsheet\AbstractBuilder;

/**
 * Director in Builder Pattern.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractHeaderSaveAsSpreadsheet implements HeaderSaveAsInterface
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
