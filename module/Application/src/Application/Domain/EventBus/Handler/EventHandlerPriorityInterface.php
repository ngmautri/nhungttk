<?php
namespace Application\Domain\EventBus\Handler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface EventHandlerPriorityInterface
{

    const HIGH_PRIORITY = PHP_INT_MAX;

    const LOW_PRIORITY = - 1;

    public static function priority();
}