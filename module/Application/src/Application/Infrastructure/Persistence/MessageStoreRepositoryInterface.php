<?php
namespace Application\Infrastructure\Persistence;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface MessageStoreRepositoryInterface
{

    public function getMessages($entityId, $entityToken = null, $limit, $offset);
}
