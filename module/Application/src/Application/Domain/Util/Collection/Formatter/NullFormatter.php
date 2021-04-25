<?php
namespace Application\Domain\Util\Collection\Formatter;

use Application\Domain\Util\Collection\Contracts\ElementFormatterInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class NullFormatter implements ElementFormatterInterface
{

    public function format($element)
    {
        return $element;
    }
}

