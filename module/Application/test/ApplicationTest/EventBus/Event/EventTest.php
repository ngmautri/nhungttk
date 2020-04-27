<?php
namespace ApplicationTest\EventBus\Event;

use Application\Application\Event\DefaultParameter;
use PHPUnit_Framework_TestCase;

class EventTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testItCanResolveHandler()
    {
        $target = Null;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId(1);
        $params = [
            "rowId" => "dsf",
            "rowToken" => "DSFDF"
        ];

        $event = new DummyEvent($target, $defaultParams, $params);

        var_dump($event->getParam("changeLog"));
    }
}