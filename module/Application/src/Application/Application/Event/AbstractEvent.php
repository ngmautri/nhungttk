<?php
namespace Application\Application\Event;

use Symfony\Component\EventDispatcher\Event;
use Application\Domain\Shared\Event\EventInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractEvent extends Event implements EventInterface
{

    private $target;
   
    /**
     * @return mixed
     */
    public function getTarget()
    {
        return $this->target;
    }

    public function __construct($target)
    {
        $this->target = $target;
    }
}
