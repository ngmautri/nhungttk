<?php
namespace Inventory\Application\Export\Transaction\Formatter;

/**
 * Row Output Decorator
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class RowFormatterDecorator extends AbstractRowFormatter
{

    protected $formatter;

    /**
     *
     * @param AbstractRowFormatter $formatter
     */
    public function __construct(AbstractRowFormatter $formatter)
    {
        $this->formatter = $formatter;
    }
}
