<?php
namespace Inventory\Application\Service\Association;

use Application\Service\AbstractService;
use Inventory\Infrastructure\Doctrine\AssociationItemQueryRepositoryImpl;
use Inventory\Infrastructure\Persistence\Filter\AssociationSqlFilter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AssociationService extends AbstractService
{

    public function getAssociationOf($entity_id, $limit, $offset)
    {
        $rep = new AssociationItemQueryRepositoryImpl($this->getDoctrineEM());

        $filter = new AssociationSqlFilter();
        $filter->setItemId($entity_id);
        $sort_by = null;
        $sort = null;
        return $rep->getList($filter, $sort_by, $sort, $limit, $offset);
    }
}
