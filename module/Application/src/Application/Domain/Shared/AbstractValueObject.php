<?php
namespace Application\Domain\Shared;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class AbstractValueObject
{

    abstract function getAttributesToCompare();

    /**
     *
     * @param AbstractValueObject $other
     * @return boolean
     */
    public function equals(AbstractValueObject $other, $caseIncentive = false)
    {
        if ($other == null) {
            return false;
        }
        return $this->getAttributesToCompare() === $other->getAttributesToCompare();
    }
}
