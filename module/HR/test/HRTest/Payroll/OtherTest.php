<?php
namespace HRTest\Payroll;

use PHPUnit_Framework_TestCase;

class OtherTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {
        $root = realpath(dirname(dirname(dirname(__FILE__))));
        echo "Test starting";
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {}
}