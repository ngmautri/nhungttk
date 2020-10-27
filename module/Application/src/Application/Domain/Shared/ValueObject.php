<?php
namespace Application\Domain\Shared;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class ValueObject extends AbstractValueObject
{

    abstract function getAttributesToCompare();

    abstract public function makeSnapshot();

    /**
     *
     * @param ValueObject $other
     * @param boolean $caseIncentive
     * @return boolean
     */
    public function equals(ValueObject $other, $caseIncentive = false)
    {
        if ($other == null) {
            return false;
        }

        return $this->getAttributesToCompare() === $other->getAttributesToCompare();
    }
}
