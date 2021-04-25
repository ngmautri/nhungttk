<?php
namespace Application\Domain\Util\Collection\Contracts;

use Application\Application\Service\Document\Pdf\AbstractBuilder;
use Application\Domain\Util\Collection\Export\AbstractExport;

/**
 * Director in Builder Pattern.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class AbstractExportAsPdf extends AbstractExport
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
