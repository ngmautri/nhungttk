<?php
namespace Inventory\Application\Command\Doctrine;

use Doctrine\ORM\EntityManager;
use Procure\Domain\Exception\DBUpdateConcurrencyException;
use Procure\Infrastructure\Doctrine\APQueryRepositoryImpl;
use Procure\Infrastructure\Doctrine\GRQueryRepositoryImpl;
use Procure\Infrastructure\Doctrine\POQueryRepositoryImpl;
use Procure\Infrastructure\Doctrine\PRQueryRepositoryImpl;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class VersionChecker
{

    public static function checkAPVersion(EntityManager $doctrineEM, $entityId, $version)
    {
        $queryRep = new APQueryRepositoryImpl($doctrineEM);
        $currentVersion = $queryRep->getVersion($entityId) - 1;
        if ($version != $currentVersion) {
            throw new DBUpdateConcurrencyException(sprintf("Object has been changed from %s to %s since retrieving. Please retry! ", $version, $currentVersion));
        }
    }

    public static function checkPOVersion(EntityManager $doctrineEM, $entityId, $version)
    {
        $queryRep = new POQueryRepositoryImpl($doctrineEM);
        $currentVersion = $queryRep->getVersion($entityId) - 1;
        if ($version != $currentVersion) {
            throw new DBUpdateConcurrencyException(sprintf("Object has been changed from %s to %s since retrieving. Please retry! ", $version, $currentVersion));
        }
    }

    public static function checkGRVersion(EntityManager $doctrineEM, $entityId, $version)
    {
        $queryRep = new GRQueryRepositoryImpl($doctrineEM);
        $currentVersion = $queryRep->getVersion($entityId) - 1;
        if ($version != $currentVersion) {
            throw new DBUpdateConcurrencyException(sprintf("Object has been changed from %s to %s since retrieving. Please retry! ", $version, $currentVersion));
        }
    }

    public static function checkPRVersion(EntityManager $doctrineEM, $entityId, $version)
    {
        $queryRep = new PRQueryRepositoryImpl($doctrineEM);
        $currentVersion = $queryRep->getVersion($entityId) - 1;
        if ($version != $currentVersion) {
            throw new DBUpdateConcurrencyException(sprintf("Object has been changed from %s to %s since retrieving. Please retry! ", $version, $currentVersion));
        }
    }
}
