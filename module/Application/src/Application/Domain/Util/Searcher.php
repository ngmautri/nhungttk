<?php
namespace Application\Domain\Util;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class Searcher
{

    /**
     *
     * @param array $arr
     * @param mixed $x
     * @return boolean
     */
    public static function binarySearch(Array $arr, $x)
    {
        // check for empty array
        if (count($arr) === 0)
            return false;
        $low = 0;
        $high = count($arr) - 1;

        while ($low <= $high) {

            // compute middle index
            $mid = floor(($low + $high) / 2);

            // element found at mid
            if ($arr[$mid] == $x) {
                return true;
            }

            if ($x < $arr[$mid]) {
                // search the left side of the array
                $high = $mid - 1;
            } else {
                // search the right side of the array
                $low = $mid + 1;
            }
        }

        // If we reach here element x doesnt exist
        return false;
    }

    /**
     *
     * @param array $arr
     * @param mixed $x
     * @return boolean
     */
    public static function linearSearch(Array $arr, $x)
    {
        // check for empty array
        if (count($arr) === 0)
            return false;

        foreach ($arr as $a) {
            if ($a == $x) {
                return true;
            }
        }

        return false;
    }
}

