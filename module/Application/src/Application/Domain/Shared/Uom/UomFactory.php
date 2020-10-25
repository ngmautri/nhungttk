<?php

namespace Application\Domain\Shared\Uom;

/**
 * This is a automatically generated file.
 *
 * @method static Uom BAG()
 * @method static Uom BOTTLE()
 * @method static Uom BOX()
 * @method static Uom BUNDLE()
 * @method static Uom DOZEN()
 * @method static Uom EACH()
 * @method static Uom G()
 * @method static Uom KG()
 * @method static Uom KILOGRAM()
 * @method static Uom LITER()
 * @method static Uom M()
 * @method static Uom PACK()
 * @method static Uom PAIL()
 * @method static Uom PAIR()
 * @method static Uom PC()
 * @method static Uom ROLL()
 * @method static Uom SET()
 * @method static Uom TABLET()
 * @method static Uom TANK()
 * @method static Uom TON()
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 */
trait UomFactory
{
    /**
     *
     * @param string $method
       @param string $arguments
     * @return Uom
     *
     * @throws \InvalidArgumentException
     */
    public static function __callStatic($method, $arguments)
    {
        return new Uom($method);
    }
}
