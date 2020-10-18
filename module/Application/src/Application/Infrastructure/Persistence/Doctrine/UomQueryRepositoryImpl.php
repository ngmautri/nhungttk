<?php
namespace Application\Infrastructure\Persistence\Doctrine;

use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Application\Infrastructure\Persistence\Contracts\SqlFilterInterface;
use Application\Infrastructure\Persistence\Contracts\UomQueryRepositoryInterface;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Application\Infrastructure\Mapper\UomMapper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class UomQueryRepositoryImpl extends AbstractDoctrineRepository implements UomQueryRepositoryInterface
{

    public function getList(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        if (! $filter instanceof SqlFilterInterface) {
            return null;
        }

        $sql = "SELECT * FROM nmt_application_uom WHERE 1";



        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtApplicationUom', 'nmt_application_uom');
            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            return $query->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }
}
