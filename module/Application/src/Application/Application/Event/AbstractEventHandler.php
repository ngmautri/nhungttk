<?php
namespace Application\Application\Event;

use Application\Domain\Shared\Event\EventHandlerInterface;
use Doctrine\ORM\EntityManager;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractEventHandler implements EventHandlerInterface
{

    private $doctrineEM;

    private $params;

    /**
     *
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     */
    public function __construct(EntityManager $doctrineEM, $params = null)
    {
        if (! $doctrineEM instanceof EntityManager) {
            return;
        }

        $this->doctrineEM = $doctrineEM;
        $this->params = $params;
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
