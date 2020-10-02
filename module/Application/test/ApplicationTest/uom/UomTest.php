<?php
namespace ApplicationTest\Attachment;

use Application\Domain\Shared\Uom\Collection\Uoms;
use PHPUnit_Framework_TestCase;

class UomTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        $uoms = new Uoms();
        $uoms = $uoms->loadUomsFromFile();
        $methodBuffer = '';

        $buffer = <<<PHP
<?php

namespace Application\Domain\Shared\Uom;

/**
 * This is a automatically generated file.
 *
PHPDOC
 */
trait UomFactory
{
    /**
     *
     * @param string \$method
     * @return Uom
     *
     * @throws \InvalidArgumentException If amount is not integer(ish)
     */
    public static function __callStatic(\$method)
    {
        return new Uom(\$method);
    }
}

PHP;

        foreach ($uoms as $k => $v) {
            $methodBuffer .= sprintf(" * @method static Uom %s()\n", \strtoupper($k));
        }

        $buffer = str_replace('PHPDOC', rtrim($methodBuffer), $buffer);

        file_put_contents(__DIR__ . '/UomFactory.php', $buffer);

    }
}