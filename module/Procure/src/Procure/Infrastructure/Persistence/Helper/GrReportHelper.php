<?php
namespace Procure\Infrastructure\Persistence\Helper;

use Doctrine\ORM\EntityManager;
use Procure\Infrastructure\Contract\SqlFilterInterface;
use Procure\Infrastructure\Persistence\Filter\GrReportSqlFilter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GrReportHelper
{

    /**
     *
     * @param EntityManager $doctrineEM
     * @param SqlFilterInterface $filter
     * @param string $sort_by
     * @param string $sort
     * @param int $limit
     * @param int $offset
     * @return NULL|array|mixed|\Doctrine\DBAL\Driver\Statement|NULL
     */
    static public function getList(EntityManager $doctrineEM, SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        if (! $doctrineEM instanceof EntityManager) {
            return null;
        }

        if (! $filter instanceof GrReportSqlFilter) {
            return null;
        }
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @param SqlFilterInterface $filter
     * @return NULL|number
     */
    static public function getListTotal(EntityManager $doctrineEM, SqlFilterInterface $filter)
    {
        if (! $doctrineEM instanceof EntityManager) {
            return null;
        }

        if (! $filter instanceof GrReportSqlFilter) {
            return null;
        }
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @param SqlFilterInterface $filter
     * @param string $sort_by
     * @param string $sort
     * @param int $limit
     * @param int $offset
     * @return NULL|object
     */
    static public function getAllRow(EntityManager $doctrineEM, SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        if (! $doctrineEM instanceof EntityManager) {
            return null;
        }

        if (! $filter instanceof GrReportSqlFilter) {
            return null;
        }
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @param SqlFilterInterface $filter
     * @return NULL|number
     */
    static public function getAllRowTotal(EntityManager $doctrineEM, SqlFilterInterface $filter)
    {
        if (! $doctrineEM instanceof EntityManager) {
            return null;
        }

        if (! $filter instanceof GrReportSqlFilter) {
            return null;
        }
    }
}
