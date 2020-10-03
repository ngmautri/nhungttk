<?php
namespace Application\Domain\Shared\Uom;

/**
 * This is a automatically generated file.
 *
 * @method static Uom METER()
 * @method static Uom EACH()
 * @method static Uom BOX()
 * @method static Uom CARTON()
 */
trait UomFactory
{

    /**
     *
     * @param string $method
     * @return Uom
     *
     * @throws \InvalidArgumentException If amount is not integer(ish)
     */
    public static function __callStatic($method, $arguments)
    {
        return new Uom($method);
    }
}
