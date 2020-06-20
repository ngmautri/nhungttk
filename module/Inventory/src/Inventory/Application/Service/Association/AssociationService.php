<?php
namespace Inventory\Application\Service\Association;

use Application\Service\AbstractService;
use Inventory\Infrastructure\Persistence\Doctrine\AssociationQueryRepositoryImpl;
use Inventory\Infrastructure\Persistence\Filter\ItemReportSqlFilter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AssociationService extends AbstractService
{

    public function getList()
    {
        $rep = new AssociationQueryRepositoryImpl($this->getDoctrineEM());

        $filter = new ItemReportSqlFilter();
        $filter->setIsActive(1);
        $sort_by = null;
        $sort = null;
        $limit = null;
        $offset = null;
        return $rep->getList($filter, $sort_by, $sort, $limit, $offset);
    }
}
