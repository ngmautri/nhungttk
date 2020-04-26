<?php
namespace Application\Domain\EventBus\Handler\Mapper;

use Application\Domain\EventBus\Event\EventInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface EventHandlerMapperInterface
{

    public function handlerName(EventInterface $event);
}
