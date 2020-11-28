<?php
namespace Application\Domain\Shared\Quantity;

/**
 *
 * @author nguyen mau tri - ngmautri@gmail.com
 *
 */
final class NullQuantity implements \jsonserializable
{

    public function jsonSerialize()
    {}
}
