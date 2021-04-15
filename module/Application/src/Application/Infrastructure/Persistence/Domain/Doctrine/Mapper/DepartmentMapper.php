<?php
namespace Application\Infrastructure\Persistence\Domain\Doctrine\Mapper;

use Application\Domain\Company\CompanySnapshot;
use Application\Domain\Company\Department\DepartmentSnapshot;
use Application\Entity\NmtApplicationCompany;
use Application\Entity\NmtApplicationDepartment;
use Doctrine\ORM\EntityManager;

class DepartmentMapper
{

    /**
     *
     * @param EntityManager $doctrineEM
     * @param CompanySnapshot $snapshot
     * @param NmtApplicationCompany $entity
     * @return NULL|\Application\Entity\NmtApplicationCompany
     */
    public static function mapSnapshotEntity(EntityManager $doctrineEM, DepartmentSnapshot $snapshot, NmtApplicationDepartment $entity)
    {
        if ($snapshot == null || $entity == null || $doctrineEM == null) {
            return null;
        }

        // $entity->setNodeId($snapshot->nodeId);
        $entity->setNodeName($snapshot->nodeName);
        $entity->setNodeParentId($snapshot->nodeParentId);
        $entity->setPath($snapshot->path);
        $entity->setPathDepth($snapshot->pathDepth);
        $entity->setStatus($snapshot->status);
        $entity->setRemarks($snapshot->remarks);
        $entity->setUuid($snapshot->uuid);
        $entity->setDepartmentName($snapshot->departmentName);
        $entity->setDepartmentCode($snapshot->departmentCode);
        $entity->setIsActive($snapshot->isActive);
        $entity->setDepartmentNameLocal($snapshot->departmentNameLocal);
        $entity->setParentName($snapshot->parentName);
        $entity->setParentCode($snapshot->parentCode);
        $entity->setToken($snapshot->token);

        // Mapping Date
        // =====================
        /*
         * $entity->setCreatedOn($snapshot->createdOn);
         * $entity->setLastChangeOn($snapshot->lastChangeOn);
         */
        if ($snapshot->createdOn !== null) {
            $entity->setCreatedOn(new \DateTime($snapshot->createdOn));
        }

        if ($snapshot->lastChangeOn !== null) {
            $entity->setLastChangeOn(new \DateTime($snapshot->lastChangeOn));
        }

        // Mapping Reference
        // =====================
        /*
         * $entity->setLastChangeBy($snapshot->lastChangeBy);
         * $entity->setCreatedBy($snapshot->createdBy);
         * $entity->setCompany($snapshot->company);
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

        return $entity;
    }

    /**
     *
     * @param NmtApplicationCompany $entity
     * @return NULL|\Application\Domain\Company\CompanyDetailsSnapshot
     */
    public static function createSnapshot(NmtApplicationDepartment $entity)
    {
        if ($entity == null) {
            return null;
        }

        $snapshot = new DepartmentSnapshot();
        $snapshot->nodeId = $entity->getNodeId();
        $snapshot->nodeName = $entity->getNodeName();
        $snapshot->nodeParentId = $entity->getNodeParentId();
        $snapshot->path = $entity->getPath();
        $snapshot->pathDepth = $entity->getPathDepth();
        $snapshot->status = $entity->getStatus();
        $snapshot->remarks = $entity->getRemarks();
        $snapshot->uuid = $entity->getUuid();
        $snapshot->departmentName = $entity->getDepartmentName();
        $snapshot->departmentCode = $entity->getDepartmentCode();
        $snapshot->isActive = $entity->getIsActive();
        $snapshot->departmentNameLocal = $entity->getDepartmentNameLocal();

        $snapshot->parentName = $entity->getParentName();
        $snapshot->parentCode = $entity->getParentCode();
        $snapshot->token = $entity->getToken();

        // Override
        $snapshot->departmentName = $entity->getNodeName();
        $snapshot->departmentCode = $entity->getNodeId();

        // Mapping Date
        // =====================
        /*
         * $snapshot->createdOn = $entity->getCreatedOn();
         * $snapshot->lastChangeOn = $entity->getLastChangeOn();
         *
         */
        if (! $entity->getLastChangeOn() == null) {
            $snapshot->lastChangeOn = $entity->getLastChangeOn()->format("Y-m-d");
        }

        // $snapshot->createdOn = $entity->getCreatedOn();
        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d");
        }

        // Mapping Reference
        // =====================

        /*
         * $snapshot->lastChangeBy = $entity->getLastChangeBy();
         * $snapshot->createdBy = $entity->getCreatedBy();
         * $snapshot->company = $entity->getCompany();
         */

        $snapshot->createdBy = $entity->getCreatedBy();
        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
        }

        $snapshot->lastChangeBy = $entity->getLastChangeBy();
        if ($entity->getLastChangeBy() !== null) {
            $snapshot->lastChangeBy = $entity->getLastChangeBy()->getId();
        }

        if ($entity->getCompany() !== null) {
            $snapshot->company = $entity->getCompany()->getId();
        }
        return $snapshot;
    }
}
