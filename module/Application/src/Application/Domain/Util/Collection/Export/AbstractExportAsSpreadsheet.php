<?php
namespace Application\Domain\Util\Collection\Export;

use Application\Application\Service\Document\Spreadsheet\AbstractBuilder;

/**
 * Director in Builder Pattern.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class AbstractExportAsSpreadsheet extends AbstractExport
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
