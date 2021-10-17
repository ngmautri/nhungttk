<?php
namespace Procure\Infrastructure\Persistence\Domain\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Procure\Domain\Clearing\Repository\ClearingQueryRepositoryInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ClearingQueryRepositoryImpl extends AbstractDoctrineRepository implements ClearingQueryRepositoryInterface
{

    public function getVersion($id, $token = null)
    {}

    public function getHeaderById($id, $token = null)
    {}

    public function getHeaderIdByRowId($id)
    {}

    public function getById($id, $outputStragegy = null)
    {}

    public function getVersionArray($id, $token = null)
    {}

    public function getHeaderDTO($id, $token = null)
    {}

    public function getByUUID($uuid)
    {}

    public function getRootEntityByTokenId($id, $token = null)
    {}
}
