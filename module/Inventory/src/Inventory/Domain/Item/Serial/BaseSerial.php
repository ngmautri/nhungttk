<?php
namespace Inventory\Domain\Item\Serial;

use Application\Domain\Shared\Assembler\GenericObjectAssembler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class BaseSerial extends AbstractSerial

{

    /**
     *
     * @param BaseSerial $other
     * @return boolean|unknown
     */
    public function equals(BaseSerial $other)
    {
        if ($other == null) {
            return false;
        }

        return $this->getId()->equals($other->getId());
    }

    /**
     *
     * @return object
     */
    public function makeSnapshot()
    {
        return GenericObjectAssembler::updateAllFieldsFrom(new SerialSnapshot(), $this);
    }
}
