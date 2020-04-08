<?php
namespace Procure\Application\Service\Output;

/**
 * Row Output Decorator
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class RowOutputDecorator extends RowOutputStrategy
{

    protected $outputStrategy;

    /**
     *
     * @param RowOutputStrategy $outputStrategy
     */
    public function __construct(RowOutputStrategy $outputStrategy)
    {
        $this->outputStrategy = $outputStrategy;
    }
}
