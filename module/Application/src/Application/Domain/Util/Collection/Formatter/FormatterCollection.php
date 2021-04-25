<?php
namespace Application\Domain\Util\Collection\Formatter;

use Application\Domain\Util\Collection\Contracts\ElementFormatterInterface;
use InvalidArgumentException;

/**
 * Element Decorator
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class FormatterCollection implements ElementFormatterInterface
{

    protected $formatters = [];

    public function add(ElementFormatterInterface $formatter)
    {
        $this->formatter[] = $formatter;
    }

    public function format($element)
    {
        if (count($this->formatters) == 0) {
            throw new InvalidArgumentException("Formatters not found");
        }

        foreach ($this->formatters as $formatter) {

            /**
             *
             * @var ElementFormatterInterface $formatter ;
             */
            $formatter->format($element);
        }

        return $element;
    }
}
