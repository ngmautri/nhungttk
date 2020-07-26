<?php
namespace Inventory\Application\Export\Transaction\Header;

use Inventory\Application\Export\Transaction\Formatter\Header\AbstractHeaderFormatter;

/**
 * Header Output Decorator
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class HeaderFormatterDecorator extends AbstractHeaderFormatter
{

    protected $formatter;

    /**
     *
     * @param AbstractHeaderFormatter $formatter
     */
    public function __construct(AbstractHeaderFormatter $formatter)
    {
        $this->formatter = $formatter;
    }
}
