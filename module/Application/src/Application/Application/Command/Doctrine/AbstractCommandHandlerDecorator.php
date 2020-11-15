<?php
namespace Application\Application\Command\Doctrine;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class AbstractCommandHandlerDecorator extends AbstractCommandHandler
{

    protected $handler;

    public function __construct(AbstractCommandHandler $cmdHandler)
    {
        $this->handler = $cmdHandler;
    }

    public function getOutput()
    {
        return $this->handler->getOutput();
    }
}
