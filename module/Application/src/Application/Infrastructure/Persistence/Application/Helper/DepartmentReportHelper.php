<?php
namespace Application\Infrastructure\Persistence\Application\Helper;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Inventory\Infrastructure\Persistence\Contracts\SqlFilterInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class DepartmentReportHelper
{

    static public function getList(EntityManager $doctrineEM, SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        $sql = "SELECT * FROM nmt_application_department";

        if ($limit > 0) {
            $sql = $sql . " LIMIT " . $limit;
        }

        if ($offset > 0) {
            $sql = $sql . " OFFSET " . $offset;
        }
        $sql = $sql . ";";

        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtApplicationDepartment', 'nmt_application_department');
            $query = $doctrineEM->createNativeQuery($sql, $rsm);

            return $query->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }
}
