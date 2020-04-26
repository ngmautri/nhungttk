<?php
namespace ApplicationTest\EventBus;

use Application\Domain\EventBus\Event\EventInterface;

class DummyEvent implements EventInterface
{

    public $body;

    public function __construct()
    {
        $d = new \DateTime();
        $this->body = \sprintf("%s-%s", "Dummy event created on ", $d->format("Y-m-d H:i:s"));
    }
}