<?php
namespace Inventory\Infrastructure\Persistence\Doctrine;

use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Inventory\Infrastructure\Persistence\Contracts\HSCodeReportRepositoryInterface;
use Inventory\Infrastructure\Persistence\Filter\HSCodeReportSqlFilter;
use Inventory\Infrastructure\Persistence\Helper\HSCodeReportHelper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class HSCodeReportRepositoryImpl extends AbstractDoctrineRepository implements HSCodeReportRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Infrastructure\Persistence\Contracts\HSCodeReportRepositoryInterface::getList()
     */
    public function getList()
    {
        $filter = new HSCodeReportSqlFilter();
        $sort_by = null;
        $sort = null;
        $limit = null;
        $offset = null;
        $results = HSCodeReportHelper::getList($this->getDoctrineEM(), $filter, $sort_by, $sort, $limit, $offset);

        return $results;
    }
}