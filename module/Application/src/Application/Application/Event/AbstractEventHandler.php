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
