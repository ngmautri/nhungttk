<?php
namespace Application\Domain\EventBus\Handler\Resolver;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface EventHandlerResolverInterface
{

    public function instantiate($handler);
}