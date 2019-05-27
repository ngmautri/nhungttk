<?php
namespace Procure\Infrastructure\Persistence;

use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DoctrinePRListRepository extends AbstractDoctrineRepository implements PRListRepositoryInterface
{

}
