<?php
namespace ApplicationTest\UtilityTest;

use Application\Domain\Util\Math\Combinition;
use PHPUnit_Framework_TestCase;

class CombinitionTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        $c = new Combinition();

        $data[] = array(
            ''
        );
        $data[] = array(
            'red',
            'yellow',
            'black'
        );
        $data[] = array(
            'xs',
            's',
            'm',
            'l',
            'xl',
            '2xl'
        );

        $data[] = array(
            'cotton',
            'polyter 50',
            'polyter 65',
            'polyter 75'
        );

        var_dump($data);

        $result = $c->getPossibleCombinitions($data);

        \var_dump($result);
    }
}
