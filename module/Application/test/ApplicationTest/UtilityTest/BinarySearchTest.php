<?php
namespace ApplicationTest\UtilityTest;

use Application\Domain\Util\Searcher;
use Symfony\Component\Stopwatch\Stopwatch;
use PHPUnit_Framework_TestCase;

class CompositeTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        $arr = [
            1,
            2,
            3,
            4,
            5,
            6,
            7,
            9,
            17,
            19,
            21,
            23,
            29,
            50,
            51,
            62,
            65,
            69,
            100,
            101,
            102,
            103,
            150,
            168,
            178,
            190,
            198,
            199,
            200,
            201,
            202,
            501,
            503,
            508,
            780,
            589,
            800,
            801
        ];

        $x = 1;

        $stopWatch = new Stopwatch();
        $stopWatch->start("test");

        $result = Searcher::binarySearch($arr, $x);
        $timer = $stopWatch->stop("test");
        \var_dump($timer->getDuration());
        \var_dump($result);
    }
}
;