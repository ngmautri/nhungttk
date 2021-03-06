<?php
namespace Procure\Application\Service\Output\Header;

use Procure\Application\Service\Output\Formatter\Header\AbstractHeaderFormatter;

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
