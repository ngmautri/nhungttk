<?php
namespace ApplicationTest\UomTest;

use Application\Domain\Shared\Uom\Collection\Uoms;
use PHPUnit_Framework_TestCase;

class UomTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        $uoms = new Uoms();
        $uoms = $uoms->getInput();
        $methodBuffer = '';

        $buffer = <<<PHP
<?php

namespace Application\Domain\Shared\Uom;

/**
 * This is a automatically generated file.
 *
PHPDOC
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 */
trait UomFactory
{
    /**
     *
     * @param string \$method
       @param string \$arguments
     * @return Uom
     *
     * @throws \InvalidArgumentException
     */
    public static function __callStatic(\$method, \$arguments)
    {
        return new Uom(\$method);
    }
}

PHP;

        foreach ($uoms as $k => $v) {
            $methodBuffer .= sprintf(" * @method static Uom %s()\n", \strtoupper($k));
        }

        $buffer = str_replace('PHPDOC', rtrim($methodBuffer), $buffer);
        $root = realpath(dirname(dirname(dirname(dirname(__FILE__)))));

        $file = $root . '/src/Application/Domain/Shared/Uom/UomFactory.php';
        file_put_contents($file, $buffer);
    }
}