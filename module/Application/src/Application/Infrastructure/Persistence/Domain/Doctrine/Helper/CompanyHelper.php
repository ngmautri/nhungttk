<?php
namespace Application\Infrastructure\Persistence\Domain\Doctrine\Helper;

use Application\Domain\Contracts\Repository\SqlFilterInterface;
use Application\Infrastructure\Persistence\Domain\Doctrine\Filter\CompanyQuerySqlFilter;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class CompanyHelper
{

    static public function getDepartmentList(EntityManager $doctrineEM, SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        if (! $filter instanceof CompanyQuerySqlFilter) {
            return null;
        }

        $sql = "SELECT * FROM nmt_application_department WHERE 1";

        if ($filter->getCompanyId() > 0) {
            $sql = $sql . \sprintf(" AND company_id  = %s", $filter->getCompanyId());
        }

        $sql = $sql . \sprintf(" ORDER BY %s", "node_name");

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
