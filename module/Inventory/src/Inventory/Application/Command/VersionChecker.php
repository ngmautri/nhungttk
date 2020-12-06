<?php
namespace Inventory\Application\Command;

use Doctrine\ORM\EntityManager;
use Inventory\Infrastructure\Doctrine\ItemQueryRepositoryImpl;
use Inventory\Infrastructure\Doctrine\TrxQueryRepositoryImpl;
use Procure\Domain\Exception\DBUpdateConcurrencyException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class VersionChecker
{

    public static function checkTrxVersion(EntityManager $doctrineEM, $entityId, $version)
    {
        $queryRep = new TrxQueryRepositoryImpl($doctrineEM);
        $currentVersion = $queryRep->getVersion($entityId) - 1;
        if ($version != $currentVersion) {
            throw new DBUpdateConcurrencyException(sprintf("Object has been changed from %s to %s since retrieving. Please retry! ", $version, $currentVersion));
        }
    }

    public static function checkItemVersion(EntityManager $doctrineEM, $entityId, $version)
    {
        $queryRep = new ItemQueryRepositoryImpl($doctrineEM);
        $currentVersion = $queryRep->getVersion($entityId) - 1;
        if ($version != $currentVersion) {
            throw new DBUpdateConcurrencyException(sprintf("Object has been changed from %s to %s since retrieving. Please retry! ", $version, $currentVersion));
        }
    }
}
