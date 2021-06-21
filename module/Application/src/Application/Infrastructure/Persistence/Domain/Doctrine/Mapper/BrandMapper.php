<?php
namespace Application\Infrastructure\Persistence\Domain\Doctrine\Mapper;

use Application\Domain\Company\Brand\BaseBrandSnapshot;
use Application\Domain\Company\Brand\BrandSnapshot;
use Application\Entity\NmtApplicationBrand;
use Doctrine\ORM\EntityManager;

class BrandMapper
{

    /*
     * |=============================
     * |Mapping Variant
     * |
     * |=============================
     */
    public static function mapBrandEntity(EntityManager $doctrineEM, BrandSnapshot $snapshot, NmtApplicationBrand $entity)
    {
        if ($snapshot == null || $entity == null || $doctrineEM == null) {
            return null;
        }

        /*
         * |=============================
         * | Mapping None-Object Field
         * | not mapping setID
         * |=============================
         */

        // $entity->setId($snapshot->id);
        $entity->setUuid($snapshot->uuid);
        $entity->setToken($snapshot->token);
        $entity->setBrandName($snapshot->brandName);
        $entity->setBrandName1($snapshot->brandName1);
        $entity->setDescription($snapshot->description);
        $entity->setRemarks($snapshot->remarks);
        $entity->setIsActive($snapshot->isActive);
        $entity->setLogo($snapshot->logo);
        $entity->setBrandName2($snapshot->brandName2);
        $entity->setVersion($snapshot->version);
        $entity->setRevisionNo($snapshot->revisionNo);

        /*
         * |=============================
         * | DATE MAPPING
         * |
         * |=============================
         */

        // $entity->setCreatedOn($snapshot->createdOn);
        // $entity->setLastChangeOn($snapshot->lastChangeOn);

        if ($snapshot->createdOn !== null) {
            $entity->setCreatedOn(new \DateTime($snapshot->createdOn));
        }

        if ($snapshot->lastChangeOn !== null) {
            $entity->setLastChangeOn(new \DateTime($snapshot->lastChangeOn));
        }

        /*
         * |=============================
         * | REFERRENCE MAPPING
         * |
         * |=============================
         */
        /*
         * $entity->setCompany($snapshot->company);
         * $entity->setCreatedBy($snapshot->createdBy);
         * $entity->setLastChangeBy($snapshot->lastChangeBy);
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

    public static function createBrandSnapshot(EntityManager $doctrineEM, NmtApplicationBrand $entity)
    {
        if ($entity == null) {
            return null;
        }

        $snapshot = new BaseBrandSnapshot();

        /*
         * |=============================
         * | Mapping None-Object Field
         * |
         * |=============================
         */

        $snapshot->id = $entity->getId();
        $snapshot->uuid = $entity->getUuid();
        $snapshot->token = $entity->getToken();
        $snapshot->brandName = $entity->getBrandName();
        $snapshot->brandName1 = $entity->getBrandName1();
        $snapshot->description = $entity->getDescription();
        $snapshot->remarks = $entity->getRemarks();
        $snapshot->isActive = $entity->getIsActive();
        $snapshot->logo = $entity->getLogo();
        $snapshot->brandName2 = $entity->getBrandName2();
        $snapshot->version = $entity->getVersion();
        $snapshot->revisionNo = $entity->getRevisionNo();
        /*
         * |=============================
         * | DATE MAPPING
         * |
         * |=============================
         */
        /*
         * $snapshot->createdOn = $entity->getCreatedOn();
         * $snapshot->lastChangeOn = $entity->getLastChangeOn();
         *
         *
         */
        if (! $entity->getLastChangeOn() == null) {
            $snapshot->lastChangeOn = $entity->getLastChangeOn()->format("Y-m-d");
        }

        // $snapshot->createdOn = $entity->getCreatedOn();
        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d");
        }

        /*
         * |=============================
         * | REFERRENCE MAPPING
         * |
         * |=============================
         */

        /*
         * $snapshot->company = $entity->getCompany();
         * $snapshot->createdBy = $entity->getCreatedBy();
         * $snapshot->lastChangeBy = $entity->getLastChangeBy();
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
        return $snapshot;
    }
}
