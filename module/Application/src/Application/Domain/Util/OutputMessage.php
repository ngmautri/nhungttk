<?php
namespace Application\Domain\Util;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class OutputMessage
{

    static public function error($text, $input)
    {
        return \sprintf('%s! [%s]', $text, $input);
    }
}

