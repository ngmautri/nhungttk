<?php
namespace HR\Application\Command\Doctrine;

use Doctrine\ORM\EntityManager;
use HR\Infrastructure\Persistance\Domain\Doctrine\IndividualQueryRepositoryImpl;
use Procure\Domain\Exception\DBUpdateConcurrencyException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class VersionChecker
{

    /**
     *
     * @param EntityManager $doctrineEM
     * @param int $entityId
     * @param int $version
     * @throws DBUpdateConcurrencyException
     */
    public static function checkIndividualVersion(EntityManager $doctrineEM, $entityId, $version)
    {
        $queryRep = new IndividualQueryRepositoryImpl($doctrineEM);
        $currentVersion = $queryRep->getVersion($entityId) - 1;
        if ($version != $currentVersion) {
            throw new DBUpdateConcurrencyException(sprintf("Object has been changed from %s to %s since retrieving. Please retry! ", $version, $currentVersion));
        }
    }
}
