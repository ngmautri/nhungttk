<?php
namespace Application\Infrastructure\Persistence\Domain\Doctrine\Mapper;

use Application\Domain\Company\AccountChart\AccountSnapshot;
use Application\Domain\Company\AccountChart\ChartSnapshot;
use Application\Entity\AppCoa;
use Application\Entity\AppCoaAccount;
use Doctrine\ORM\EntityManager;

class ChartMapper
{

    /**
     *
     * @param EntityManager $doctrineEM
     * @param ChartSnapshot $snapshot
     * @param AppCoa $entity
     * @return NULL|\Application\Entity\AppCoa
     */
    public static function mapChartEntity(EntityManager $doctrineEM, ChartSnapshot $snapshot, AppCoa $entity)
    {
        if ($snapshot == null || $entity == null || $doctrineEM == null) {
            return null;
        }

        // $entity->setId($snapshot->id);
        // var_dump($snapshot);
        $entity->setUuid($snapshot->uuid);
        $entity->setCoaCode($snapshot->coaCode);
        $entity->setCoaName($snapshot->coaName);
        $entity->setDescription($snapshot->description);
        $entity->setVersion($snapshot->version);
        $entity->setRevisionNo($snapshot->revisionNo);
        $entity->setIsActive($snapshot->isActive);
        $entity->setToken($snapshot->token);

        // Mapping Date
        // =====================
        /*
         * $entity->setCreatedOn($snapshot->createdOn);
         * $entity->setLastChangeOn($snapshot->lastChangeOn); $entity->setValidFrom($snapshot->validFrom);
         * $entity->setValidTo($snapshot->validTo);
         *
         */
        if ($snapshot->createdOn !== null) {
            $entity->setCreatedOn(new \DateTime($snapshot->createdOn));
        }

        if ($snapshot->lastChangeOn !== null) {
            $entity->setLastChangeOn(new \DateTime($snapshot->lastChangeOn));
        }

        if ($snapshot->validFrom !== null) {
            $entity->setValidFrom(new \DateTime($snapshot->validFrom));
        }

        if ($snapshot->validTo !== null) {
            $entity->setValidTo(new \DateTime($snapshot->validTo));
        }

        // Mapping Reference
        // =====================
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

    /**
     *
     * @param EntityManager $doctrineEM
     * @param AccountSnapshot $snapshot
     * @param AppCoaAccount $entity
     * @return NULL|\Application\Entity\AppCoaAccount
     */
    public static function mapAccountEntity(EntityManager $doctrineEM, AccountSnapshot $snapshot, AppCoaAccount $entity)
    {
        if ($snapshot == null || $entity == null || $doctrineEM == null) {
            return null;
        }

        // $entity->setId($snapshot->id);
        $entity->setUuid($snapshot->uuid);
        $entity->setToken($snapshot->token);
        $entity->setAccountNumer($snapshot->accountNumer);
        $entity->setAccountName($snapshot->accountName);
        $entity->setAccountType($snapshot->accountType);
        $entity->setAccountClass($snapshot->accountClass);
        $entity->setAccountGroup($snapshot->accountGroup);
        $entity->setParentAccountNumber($snapshot->parentAccountNumber);
        $entity->setIsActive($snapshot->isActive);
        $entity->setDescription($snapshot->description);
        $entity->setRemarks($snapshot->remarks);
        $entity->setAllowReconciliation($snapshot->allowReconciliation);
        $entity->setHasCostCenter($snapshot->hasCostCenter);
        $entity->setIsClearingAccount($snapshot->isClearingAccount);
        $entity->setIsControlAccount($snapshot->isControlAccount);
        $entity->setManualPostingBlocked($snapshot->manualPostingBlocked);
        $entity->setAllowPosting($snapshot->allowPosting);

        // Mapping Date
        // =====================
        /*
         * $entity->setCreatedOn($snapshot->createdOn);
         * $entity->setLastChangeOn($snapshot->lastChangeOn);
         *
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
         * $entity->setCoa($snapshot->coa);
         * $entity->setCreatedBy($snapshot->createdBy);
         * $entity->setLastChangeBy($snapshot->lastChangeBy);
         *
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

        if ($snapshot->coa > 0) {
            /**
             *
             * @var \Application\Entity\AppCoa $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\AppCoa')->find($snapshot->coa);
            $entity->setCoa($obj);
        }

        return $entity;
    }

    /**
     *
     * @param AppCoa $entity
     * @return NULL|\Application\Domain\Company\Department\DepartmentSnapshot
     */
    public static function createChartSnapshot(AppCoa $entity)
    {
        if ($entity == null) {
            return null;
        }

        $snapshot = new ChartSnapshot();

        $snapshot->id = $entity->getId();
        $snapshot->uuid = $entity->getUuid();
        $snapshot->coaCode = $entity->getCoaCode();
        $snapshot->coaName = $entity->getCoaName();
        $snapshot->description = $entity->getDescription();
        $snapshot->createdOn = $entity->getCreatedOn();
        $snapshot->lastChangeOn = $entity->getLastChangeOn();
        $snapshot->version = $entity->getVersion();
        $snapshot->revisionNo = $entity->getRevisionNo();
        $snapshot->isActive = $entity->getIsActive();
        $snapshot->token = $entity->getToken();

        // Mapping Date
        // =====================
        /*
         * $snapshot->createdOn = $entity->getCreatedOn();
         * $snapshot->lastChangeOn = $entity->getLastChangeOn();
         * $snapshot->validFrom = $entity->getValidFrom();
         * $snapshot->validTo = $entity->getValidTo();
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

        if (! $entity->getValidFrom() == null) {
            $snapshot->validFrom = $entity->getValidFrom()->format("Y-m-d");
        }

        // $snapshot->createdOn = $entity->getCreatedOn();
        if (! $entity->getValidTo() == null) {
            $snapshot->validTo = $entity->getValidTo()->format("Y-m-d");
        }

        // Mapping Reference
        // =====================

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

    public static function createAccountSnapshot(AppCoaAccount $entity)
    {
        if ($entity == null) {
            return null;
        }

        $snapshot = new AccountSnapshot();

        $snapshot->id = $entity->getId();
        $snapshot->uuid = $entity->getUuid();
        $snapshot->token = $entity->getToken();
        $snapshot->accountNumer = $entity->getAccountNumer();
        $snapshot->accountName = $entity->getAccountName();
        $snapshot->accountType = $entity->getAccountType();
        $snapshot->accountClass = $entity->getAccountClass();
        $snapshot->accountGroup = $entity->getAccountGroup();
        $snapshot->parentAccountNumber = $entity->getParentAccountNumber();
        $snapshot->isActive = $entity->getIsActive();
        $snapshot->description = $entity->getDescription();
        $snapshot->remarks = $entity->getRemarks();
        $snapshot->allowReconciliation = $entity->getAllowReconciliation();
        $snapshot->hasCostCenter = $entity->getHasCostCenter();
        $snapshot->isClearingAccount = $entity->getIsClearingAccount();
        $snapshot->isControlAccount = $entity->getIsControlAccount();
        $snapshot->manualPostingBlocked = $entity->getManualPostingBlocked();
        $snapshot->allowPosting = $entity->getAllowPosting();

        // Mapping Date
        // =====================
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

        // Mapping Reference
        // =====================

        /*
         * $snapshot->coa = $entity->getCoa();
         * $snapshot->createdBy = $entity->getCreatedBy();
         * $snapshot->lastChangeBy = $entity->getLastChangeBy();
         */

        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
        }

        if ($entity->getLastChangeBy() !== null) {
            $snapshot->lastChangeBy = $entity->getLastChangeBy()->getId();
        }

        if ($entity->getCoa()) {
            $snapshot->coa = $entity->getCoa()->getId();
        }
        return $snapshot;
    }
}
