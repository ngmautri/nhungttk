<?php
namespace Inventory\Application\Service\Item;

use Application\Service\AbstractService;
use Inventory\Domain\Item\GenericItem;
use Inventory\Infrastructure\Doctrine\ItemQueryRepositoryImpl;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class EmployeeService extends AbstractService
{

    public function getDocDetailsByTokenId($id, $token)
    {
        $rep = new ItemQueryRepositoryImpl($this->getDoctrineEM());
        $rootEntity = $rep->getRootEntityByTokenId($id, $token);

        if (! $rootEntity instanceof GenericItem) {
            return null;
        }
        return $rootEntity;
    }
}
