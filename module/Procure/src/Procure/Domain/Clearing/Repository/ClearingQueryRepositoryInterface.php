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

    public function getById($id, $outputStragegy = null);

    public function getHeaderById($id, $token = null);

    public function getHeaderDTO($id, $token = null);

    public function getByUUID($uuid);

    public function getRootEntityByTokenId($id, $token = null);

    public function getVersion($id, $token = null);

    public function getVersionArray($id, $token = null);
}
