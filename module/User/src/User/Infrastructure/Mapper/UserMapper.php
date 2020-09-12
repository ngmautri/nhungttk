<?php
namespace User\Infrastructure\Mapper;

use Application\Entity\MlaUsers;
use Doctrine\ORM\EntityManager;
use User\Domain\User\UserSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class UserMapper
{

    /**
     *
     * @param EntityManager $doctrineEM
     * @param UserSnapshot $snapshot
     * @param MlaUsers $entity
     * @return NULL|\Application\Entity\MlaUsers
     */
    public static function mapSnapshotEntity(EntityManager $doctrineEM, UserSnapshot $snapshot, MlaUsers $entity)
    {
        if ($snapshot == null || $entity == null || $doctrineEM == null) {
            return null;
        }

        // =================================
        // Mapping None-Object Field
        // =================================

        // $entity->setId($snapshot->id);
        $entity->setToken($snapshot->token);
        $entity->setChecksum($snapshot->checksum);
        $entity->setTitle($snapshot->title);
        $entity->setFirstname($snapshot->firstname);
        $entity->setLastname($snapshot->lastname);
        $entity->setPassword($snapshot->password);
        $entity->setSalt($snapshot->salt);
        $entity->setEmail($snapshot->email);
        $entity->setRole($snapshot->role);
        $entity->setRegistrationKey($snapshot->registrationKey);
        $entity->setConfirmed($snapshot->confirmed);
        $entity->setBlock($snapshot->block);

        // ============================
        // DATE MAPPING
        // ============================
        $entity->setRegisterDate($snapshot->registerDate);
        $entity->setLastvisitDate($snapshot->lastvisitDate);

        if ($snapshot->registerDate !== null) {
            $entity->setRegisterDate(new \DateTime($snapshot->registerDate));
        }

        if ($snapshot->lastChangeOn !== null) {
            $entity->setLastvisitDate(new \DateTime($snapshot->registerDate));
        }

        // ============================
        // REFERRENCE MAPPING
        // ============================

        // =========
        $entity->setCompany($snapshot->company);

        if ($snapshot->company > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationCompany $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationCompany')->find($snapshot->company);
            $entity->setCompany($obj);
        }

        return $entity;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @param MlaUsers $entity
     * @param object $snapshot
     * @return NULL|string|\User\Domain\User\UserSnapshot
     */
    public static function createSnapshot(EntityManager $doctrineEM, MlaUsers $entity, $snapshot = null)
    {
        if ($entity == null) {
            return null;
        }

        if ($snapshot == null) {
            $snapshot = new UserSnapshot();
        }

        // =================================
        // Mapping None-Object Field
        // =================================
        $snapshot->id = $entity->getId();
        $snapshot->token = $entity->getToken();
        $snapshot->checksum = $entity->getChecksum();
        $snapshot->title = $entity->getTitle();
        $snapshot->firstname = $entity->getFirstname();
        $snapshot->lastname = $entity->getLastname();
        $snapshot->password = $entity->getPassword();
        $snapshot->salt = $entity->getSalt();
        $snapshot->email = $entity->getEmail();
        $snapshot->role = $entity->getRole();
        $snapshot->registrationKey = $entity->getRegistrationKey();
        $snapshot->confirmed = $entity->getConfirmed();
        $snapshot->block = $entity->getBlock();

        // ============================
        // DATE MAPPING
        // ============================

        // $snapshot->registerDate = $entity->getRegisterDate();
        // $snapshot->lastvisitDate = $entity->getLastvisitDate();

        if (! $entity->getRegisterDate() == null) {
            $snapshot->registerDate = $entity->getRegisterDate()->format("Y-m-d H:i:s");
        }

        if (! $entity->getLastvisitDate() == null) {
            $snapshot->lastvisitDate = $entity->getLastvisitDate()->format("Y-m-d H:i:s");
        }

        // ============================
        // REFERRENCE MAPPING
        // ============================
        // $snapshot->company = $entity->getCompany();
        if ($entity->getCompany() !== null) {
            $snapshot->company = $entity->getCompany()->getId();
        }

        return $snapshot;
    }
}
