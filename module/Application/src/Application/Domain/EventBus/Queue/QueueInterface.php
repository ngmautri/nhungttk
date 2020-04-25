<?php
namespace Application\Domain\EventBus\Queue;

use Application\Domain\EventBus\Event\EventInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface QueueInterface
{

    public function name();

    public function push(EventInterface $event);

    public function pop();

    public function hasElements();
}