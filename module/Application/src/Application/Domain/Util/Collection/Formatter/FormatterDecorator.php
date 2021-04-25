<?php
namespace Application\Domain\Util\Collection\Formatter;

use Application\Domain\Util\Collection\Contracts\ElementFormatterInterface;

/**
 * Element Decorator
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class FormatterDecorator implements ElementFormatterInterface
{

    protected $formatter;

    public function __construct(ElementFormatterInterface $formatter)
    {
        $this->formatter = $formatter;
    }
}
