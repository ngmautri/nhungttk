<?php
namespace Application\Domain\EventBus\Handler\Mapper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
use Application\Domain\EventBus\Event\EventInterface;

interface EventHandlerMapperInterface
{

    public function handlerName(EventInterface $event);
}
