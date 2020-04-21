<?php
namespace Procure\Domain\PurchaseRequest\Repository;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface PrQueryRepositoryInterface
{

    /**
     *
     * @param int $id
     */
    public function getHeaderIdByRowId($id);

    /**
     *
     * @param int $id
     * @param string $token
     */
    public function getVersion($id, $token = null);

    /**
     *
     * @param int $id
     * @param string $token
     */
    public function getVersionArray($id, $token = null);

    public function getRootEntityByTokenId($id, $token = null);

    public function getHeaderById($id, $token = null);
}
