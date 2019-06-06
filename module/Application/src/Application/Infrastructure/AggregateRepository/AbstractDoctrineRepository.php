<?php
namespace Application\Infrastructure\AggregateRepository;

use Application\Domain\Exception\InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AbstractDoctrineRepository
{

    public function __construct(\Doctrine\ORM\EntityManager $doctrineEM)
    {
        if ($doctrineEM == null)
            throw new InvalidArgumentException("Entitiy manager not found.");

        $this->doctrineEM = $doctrineEM;
    }

    protected $doctrineEM;

    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }
}
