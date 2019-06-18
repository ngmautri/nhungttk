<?php
namespace Procure\Infrastructure\Persistence\Doctrine;

use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Procure\Infrastructure\Persistence\POListRepositoryInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class POListRepository extends AbstractDoctrineRepository implements POListRepositoryInterface
{
    /**
     * 
     * {@inheritDoc}
     * @see \Procure\Infrastructure\Persistence\POListRepositoryInterface::getPoList()
     */
    public function getPoList($is_active = 1, $current_state = null, $docStatus = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0)
    {}

    
    /**
     * 
     * {@inheritDoc}
     * @see \Procure\Infrastructure\Persistence\POListRepositoryInterface::getPOStatus()
     */
    public function getPOStatus($id, $token)
    {}

    /**
     * 
     * {@inheritDoc}
     * @see \Procure\Infrastructure\Persistence\POListRepositoryInterface::getPoOfItem()
     */
    public function getPoOfItem($item_id, $token)
    {}
    public function getPoRowOfVendor($vendor_id = null, $vendor_token = null, $sort_by = null, $order = 'DESC', $limit = 0, $offset = 0)
    {}

    public function getOpenPoAP($id, $token)
    {}

    public function getOpenPoGr($id, $token)
    {}


    
}
