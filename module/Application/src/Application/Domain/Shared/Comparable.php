<?php
namespace Application\Domain\Shared;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface Comparable
{

    /**
     * Compares the current object to the passed $other.
     *
     * Returns 0 if they are semantically equal, 1 if the other object
     * is less than the current one, or -1 if its more than the current one.
     *
     * This method should not check for identity using ===, only for semantical equality for example
     * when two different DateTime instances point to the exact same Date + TZ.
     *
     * @param mixed $other
     *
     * @return int
     */
    public function compareTo($other);
}
