<?php
namespace Application\Application\Event;

use Doctrine\ORM\EntityManager;
use Application\Domain\Shared\Event\EventHandlerInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractEventHandler implements EventHandlerInterface
{

    protected $doctrineEM;

    /**
     *
     * @param EntityManager $doctrineEM
     */
    public function __construct(EntityManager $doctrineEM)
    {
        if (! $doctrineEM instanceof EntityManager) {
            return;
        }

        $this->doctrineEM = $doctrineEM;
    }

    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }
}
