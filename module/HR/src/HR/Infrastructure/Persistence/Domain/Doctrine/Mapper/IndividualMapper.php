<?php
namespace HR\Infrastructure\Persistence\Domain\Doctrine\Mapper;

use Application\Entity\HrIndividual;
use Doctrine\ORM\EntityManager;
use HR\Domain\Employee\BaseIndividualSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class IndividualMapper
{

    /**
     *
     * @param EntityManager $doctrineEM
     * @param BaseIndividualSnapshot $snapshot
     * @param HrIndividual $entity
     * @return NULL|\Application\Entity\HrIndividual
     */
    public static function mapSnapshotEntity(EntityManager $doctrineEM, BaseIndividualSnapshot $snapshot, HrIndividual $entity)
    {
        if ($snapshot == null || $entity == null || $doctrineEM == null) {
            return null;
        }

        // =================================
        // Mapping None-Object Field
        // =================================

        // $entity->setId($snapshot->id);
        $entity->setIndividualType($snapshot->individualType);
        $entity->setIndividualName($snapshot->individualName);
        $entity->setIndividualNameLocal($snapshot->individualNameLocal);
        $entity->setFirstName($snapshot->firstName);
        $entity->setFirstNameLocal($snapshot->firstNameLocal);
        $entity->setMiddleName($snapshot->middleName);
        $entity->setMiddleNameLocal($snapshot->middleNameLocal);
        $entity->setLastName($snapshot->lastName);
        $entity->setLastNameLocal($snapshot->lastNameLocal);
        $entity->setNickName($snapshot->nickName);
        $entity->setPersonalIdNumber($snapshot->personalIdNumber);
        $entity->setGender($snapshot->gender);
        $entity->setLastStatusId($snapshot->lastStatusId);
        $entity->setRemarks($snapshot->remarks);
        $entity->setEmployeeStatus($snapshot->employeeStatus);
        $entity->setEmployeeCode($snapshot->employeeCode);
        $entity->setRevisionNo($snapshot->revisionNo);
        $entity->setVersion($snapshot->version);
        $entity->setSysNumber($snapshot->sysNumber);
        $entity->setToken($snapshot->token);
        $entity->setUuid($snapshot->uuid);

        $entity->setPassportNo($snapshot->passportNo);
        $entity->setPassportIssuePlace($snapshot->passportIssuePlace);
        $entity->setWorkPermitNo($snapshot->workPermitNo);
        $entity->setFamilyBookNo($snapshot->familyBookNo);
        $entity->setSsoNumber($snapshot->ssoNumber);
        $entity->setPersonalIdNumber($snapshot->personalIdNumber);

        // manual
        $entity->setIndividualName(\sprintf("%s %s %s", $snapshot->firstName, $snapshot->middleName, $snapshot->lastName));

        // ============================
        // DATE MAPPING
        // ============================

        /*
         * $entity->setBirthday($snapshot->birthday);
         * $entity->setCreatedOn($snapshot->createdOn);
         * $entity->setLastChangeOn($snapshot->lastChangeOn);
         * $entity->setPassportIssueDate($snapshot->passportIssueDate);
         * $entity->setPassportExpiredDate($snapshot->passportExpiredDate);
         * $entity->setWorkPermitDate($snapshot->workPermitDate);
         * $entity->setWorkPermitExpiredDate($snapshot->workPermitExpiredDate);
         * $entity->setPersonalIdNumberDate($snapshot->personalIdNumberDate);
         * $entity->setPersonalIdNumberExpiredDate($snapshot->personalIdNumberExpiredDate);
         */

        if ($snapshot->birthday !== null) {
            $entity->setBirthday(new \DateTime($snapshot->birthday));
        }

        if ($snapshot->createdOn !== null) {
            $entity->setCreatedOn(new \DateTime($snapshot->createdOn));
        }

        if ($snapshot->lastChangeOn !== null) {
            $entity->setLastChangeOn(new \DateTime($snapshot->lastChangeOn));
        }

        // --

        if ($snapshot->passportIssueDate !== null) {
            $entity->setPassportIssueDate(new \DateTime($snapshot->passportIssueDate));
        }

        if ($snapshot->passportExpiredDate !== null) {
            $entity->setPassportExpiredDate(new \DateTime($snapshot->passportExpiredDate));
        }

        if ($snapshot->workPermitDate !== null) {
            $entity->setWorkPermitDate(new \DateTime($snapshot->workPermitDate));
        }
        if ($snapshot->workPermitExpiredDate !== null) {
            $entity->setWorkPermitExpiredDate(new \DateTime($snapshot->workPermitExpiredDate));
        }
        if ($snapshot->personalIdNumberDate !== null) {
            $entity->setPersonalIdNumberDate(new \DateTime($snapshot->personalIdNumberDate));
        }
        if ($snapshot->personalIdNumberExpiredDate !== null) {
            $entity->setPersonalIdNumberExpiredDate(new \DateTime($snapshot->personalIdNumberExpiredDate));
        }

        // ============================
        // REFERRENCE MAPPING
        // ============================
        /*
         * $entity->setCreatedBy($snapshot->createdBy);
         * $entity->setLastChangeBy($snapshot->lastChangeBy);
         * $entity->setCompany($snapshot->company);
         * $entity->setNationality($snapshot->nationality);
         */

        if ($snapshot->createdBy > 0) {
            /**
             *
             * @var \Application\Entity\MlaUsers $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\MlaUsers')->find($snapshot->createdBy);
            $entity->setCreatedBy($obj);
        }

        if ($snapshot->lastChangeBy > 0) {
            /**
             *
             * @var \Application\Entity\MlaUsers $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\MlaUsers')->find($snapshot->lastChangeBy);
            $entity->setLastChangeBy($obj);
        }

        if ($snapshot->company > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationCompany $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationCompany')->find($snapshot->company);
            $entity->setCompany($obj);
        }

        if ($snapshot->nationality > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationCountry $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationCountry')->find($snapshot->nationality);
            $entity->setNationality($obj);
        }

        return $entity;
    }

    /**
     *
     * @param HrIndividual $entity
     * @param BaseIndividualSnapshot $snapshot
     * @param boolean $needDetails
     * @return NULL|\HR\Domain\Employee\BaseIndividualSnapshot
     */
    public static function createSnapshot(HrIndividual $entity, BaseIndividualSnapshot $snapshot = null, $needDetails = false)
    {
        if ($entity == null) {
            return null;
        }

        if ($snapshot == null) {
            $snapshot = new BaseIndividualSnapshot();
        }

        // =================================
        // Mapping None-Object Field
        // =================================

        $snapshot->id = $entity->getId();
        $snapshot->individualType = $entity->getIndividualType();
        $snapshot->individualName = $entity->getIndividualName();
        $snapshot->individualNameLocal = $entity->getIndividualNameLocal();
        $snapshot->firstName = $entity->getFirstName();
        $snapshot->firstNameLocal = $entity->getFirstNameLocal();
        $snapshot->middleName = $entity->getMiddleName();
        $snapshot->middleNameLocal = $entity->getMiddleNameLocal();
        $snapshot->lastName = $entity->getLastName();
        $snapshot->lastNameLocal = $entity->getLastNameLocal();
        $snapshot->nickName = $entity->getNickName();
        $snapshot->personalIdNumber = $entity->getPersonalIdNumber();
        $snapshot->gender = $entity->getGender();
        $snapshot->birthday = $entity->getBirthday();
        $snapshot->lastStatusId = $entity->getLastStatusId();
        $snapshot->remarks = $entity->getRemarks();
        $snapshot->employeeStatus = $entity->getEmployeeStatus();
        $snapshot->employeeCode = $entity->getEmployeeCode();
        $snapshot->revisionNo = $entity->getRevisionNo();
        $snapshot->version = $entity->getVersion();
        $snapshot->sysNumber = $entity->getSysNumber();
        $snapshot->token = $entity->getToken();
        $snapshot->uuid = $entity->getUuid();

        $snapshot->passportNo = $entity->getPassportNo();
        $snapshot->passportIssuePlace = $entity->getPassportIssuePlace();
        $snapshot->workPermitNo = $entity->getWorkPermitNo();
        $snapshot->familyBookNo = $entity->getFamilyBookNo();
        $snapshot->ssoNumber = $entity->getSsoNumber();
        $snapshot->personalIdNumber = $entity->getPersonalIdNumber();

        // ============================
        // DATE MAPPING
        // ============================
        /*
         * $snapshot->birthday = $entity->getBirthday();
         * $snapshot->createdOn = $entity->getCreatedOn();
         * $snapshot->lastChangeOn = $entity->getLastChangeOn();
         * $snapshot->passportIssueDate = $entity->getPassportIssueDate();
         * $snapshot->passportExpiredDate = $entity->getPassportExpiredDate();
         * $snapshot->workPermitDate = $entity->getWorkPermitDate();
         * $snapshot->workPermitExpiredDate = $entity->getWorkPermitExpiredDate();
         * $snapshot->personalIdNumberDate = $entity->getPersonalIdNumberDate();
         * $snapshot->personalIdNumberExpiredDate = $entity->getPersonalIdNumberExpiredDate();
         */

        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d H:i:s");
        }

        if (! $entity->getLastChangeOn() == null) {
            $snapshot->lastChangeOn = $entity->getLastChangeOn()->format("Y-m-d H:i:s");
        }

        if (! $entity->getBirthday() == null) {
            $snapshot->birthday = $entity->getBirthday()->format("Y-m-d");
        }

        // ==

        if (! $entity->getPassportIssueDate() == null) {
            $snapshot->passportIssueDate = $entity->getPassportIssueDate()->format("Y-m-d");
        }
        if (! $entity->getPassportExpiredDate() == null) {
            $snapshot->passportExpiredDate = $entity->getPassportExpiredDate()->format("Y-m-d");
        }
        if (! $entity->getWorkPermitDate() == null) {
            $snapshot->workPermitDate = $entity->getWorkPermitDate()->format("Y-m-d");
        }
        if (! $entity->getWorkPermitExpiredDate() == null) {
            $snapshot->workPermitExpiredDate = $entity->getWorkPermitExpiredDate()->format("Y-m-d");
        }
        if (! $entity->getPersonalIdNumberDate() == null) {
            $snapshot->personalIdNumberDate = $entity->getPersonalIdNumberDate()->format("Y-m-d");
        }
        if (! $entity->getPersonalIdNumberExpiredDate() == null) {
            $snapshot->personalIdNumberExpiredDate = $entity->getPersonalIdNumberExpiredDate()->format("Y-m-d");
        }

        // ============================
        // REFERRENCE MAPPING
        // ============================

        /*
         * $snapshot->createdBy = $entity->getCreatedBy();
         * $snapshot->lastChangeBy = $entity->getLastChangeBy();
         * $snapshot->company = $entity->getCompany();
         * $snapshot->nationality = $entity->getNationality();
         */
        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
        }

        if ($entity->getLastChangeBy() !== null) {
            $snapshot->lastChangeBy = $entity->getLastChangeBy()->getId();
        }

        if ($entity->getCompany() !== null) {
            $snapshot->company = $entity->getCompany()->getId();
        }

        if ($entity->getNationality() !== null) {
            $snapshot->nationality = $entity->getNationality()->getId();
        }

        return $snapshot;
    }
}
