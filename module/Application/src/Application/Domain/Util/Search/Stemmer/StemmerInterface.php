<?php
namespace Application\Domain\Util\Search\Stemmer;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface StemmerInterface
{

    public static function stem($word);
}

