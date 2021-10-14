<?php
namespace Procure\Domain\Clearing\Repository;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface ClearingQueryRepositoryInterface
{

    public function getHeaderIdByRowId($id);

    public function findAll();

    public function getById($id, $outputStragegy = null);

    public function getHeaderById($id, $token = null);

    public function getHeaderDTO($id, $token = null);

    public function getByUUID($uuid);

    public function getRootEntityByTokenId($id, $token = null);

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
}
