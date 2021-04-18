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

    /**
     *
     * @param EntityManager $doctrineEM
     * @param SqlFilterInterface $filter
     * @return NULL|array|mixed|\Doctrine\DBAL\Driver\Statement|NULL
     */
    static public function getDepartments(EntityManager $doctrineEM, SqlFilterInterface $filter)
    {
        if (! $filter instanceof CompanyQuerySqlFilter) {
            return null;
        }

        $sql = "SELECT * FROM nmt_application_department WHERE 1";

        if ($filter->getCompanyId() > 0) {
            $sql = $sql . \sprintf(" AND company_id  = %s", $filter->getCompanyId());
        }

        $sql = $sql . \sprintf(" ORDER BY %s", "node_name");

        if ($filter->getLimit() > 0) {
            $sql = $sql . " LIMIT " . $filter->getLimit();
        }

        if ($filter->getOffset() > 0) {
            $sql = $sql . " OFFSET " . $filter->getOffset();
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

    /**
     *
     * @param EntityManager $doctrineEM
     * @param SqlFilterInterface $filter
     * @return NULL|array|mixed|\Doctrine\DBAL\Driver\Statement|NULL
     */
    static public function getAccountCharts(EntityManager $doctrineEM, SqlFilterInterface $filter)
    {
        if (! $filter instanceof CompanyQuerySqlFilter) {
            return null;
        }

        $sql = "SELECT * FROM app_coa WHERE 1";

        if ($filter->getCompanyId() > 0) {
            $sql = $sql . \sprintf(" AND company_id  = %s", $filter->getCompanyId());
        }

        $sql = $sql . \sprintf(" ORDER BY %s", "coa_name");

        if ($filter->getLimit() > 0) {
            $sql = $sql . " LIMIT " . $filter->getLimit();
        }

        if ($filter->getOffset() > 0) {
            $sql = $sql . " OFFSET " . $filter->getOffset();
        }
        $sql = $sql . ";";

        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\AppCoa', 'app_coa');
            $query = $doctrineEM->createNativeQuery($sql, $rsm);

            return $query->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    static public function getPostingPeriods(EntityManager $doctrineEM, SqlFilterInterface $filter)
    {
        if (! $filter instanceof CompanyQuerySqlFilter) {
            return null;
        }

        $sql = "SELECT * FROM nmt_fin_posting_period WHERE 1";

        if ($filter->getCompanyId() > 0) {
            $sql = $sql . \sprintf(" AND company_id  = %s", $filter->getCompanyId());
        }

        $sql = $sql . \sprintf(" ORDER BY %s", "period_code");

        if ($filter->getLimit() > 0) {
            $sql = $sql . " LIMIT " . $filter->getLimit();
        }

        if ($filter->getOffset() > 0) {
            $sql = $sql . " OFFSET " . $filter->getOffset();
        }
        $sql = $sql . ";";

        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtFinPostingPeriod', 'nmt_fin_posting_period');
            $query = $doctrineEM->createNativeQuery($sql, $rsm);

            return $query->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }
}
