<?php
namespace Application\Domain\Shared\Command;

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
}
