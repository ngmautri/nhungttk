<?php
namespace Application\Infrastructure\Mapper;

use Doctrine\ORM\EntityManager;
use Application\Domain\Attachment\AttachmentSnapshot;
use Application\Entity\NmtApplicationAttachment;
use Application\Domain\Attachment\AttachmentSnapshotAssembler;
use Application\Application\DTO\Attachment\AttachmentDTOAssembler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AttachmentMapper
{

    /**
     *
     * @param EntityManager $doctrineEM
     * @param AttachmentSnapshot $snapshot
     * @param NmtApplicationAttachment $entity
     * @return NULL|\Application\Entity\NmtApplicationAttachment
     */
    public static function mapSnapshotEntity(EntityManager $doctrineEM, AttachmentSnapshot $snapshot, NmtApplicationAttachment $entity)
    {
        if ($snapshot == null || $entity == null || $doctrineEM == null) {
            return null;
        }

        // DATE MAPPING
        // ==================

        // $entity->setValidFrom($snapshot->validFrom);
        if ($snapshot->validFrom !== null) {
            $entity->setCreatedOn(new \DateTime($snapshot->validFrom));
        }

        // $entity->setValidTo($snapshot->validTo);
        if ($snapshot->validTo !== null) {
            $entity->setCreatedOn(new \DateTime($snapshot->validTo));
        }

        // $entity->setCreatedOn($snapshot->createdOn);
        if ($snapshot->createdOn !== null) {
            $entity->setCreatedOn(new \DateTime($snapshot->createdOn));
        }

        // $entity->setLastChangeOn($snapshot->lastChangeOn);
        if ($snapshot->lastChangeOn !== null) {
            $entity->setCreatedOn(new \DateTime($snapshot->lastChangeOn));
        }

        // REFERRENCE MAPPING
        // ==================
        $entity->setCreatedBy($snapshot->createdBy);
        // $entity->setPmtTerm($snapshot->pmtTerm);
        if ($snapshot->createdBy > 0) {
            /**
             *
             * @var \Application\Entity\MlaUsers $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\MlaUsers')->find($snapshot->createdBy);
            $entity->setPmtTerm($obj);
        }

        $entity->setItem($snapshot->item);
        if ($snapshot->item > 0) {
            /**
             *
             * @var \Application\Entity\NmtInventoryItem $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->find($snapshot->item);
            $entity->setPmtTerm($obj);
        }

        $entity->setPo($snapshot->po);
        if ($snapshot->po > 0) {
            /**
             *
             * @var \Application\Entity\NmtProcurePo $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtProcurePo')->find($snapshot->po);
            $entity->setPmtTerm($obj);
        }

        $entity->setPoRow($snapshot->poRow);
        if ($snapshot->poRow > 0) {
            /**
             *
             * @var \Application\Entity\NmtProcurePoRow $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtProcurePoRow')->find($snapshot->poRow);
            $entity->setPmtTerm($obj);
        }

        $entity->setQo($snapshot->qo);
        if ($snapshot->qo > 0) {
            /**
             *
             * @var \Application\Entity\NmtProcureQo $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtProcureQo')->find($snapshot->qo);
            $entity->setPmtTerm($obj);
        }

        $entity->setCompany($snapshot->company);
        if ($snapshot->company > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationCompany $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationCompany')->find($snapshot->company);
            $entity->setPmtTerm($obj);
        }

        $entity->setLastChangeBy($snapshot->lastChangeBy);
        if ($snapshot->lastChangeBy > 0) {
            /**
             *
             * @var \Application\Entity\MlaUsers $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\MlaUsers')->find($snapshot->lastChangeBy);
            $entity->setPmtTerm($obj);
        }

        $entity->setProject($snapshot->project);
        if ($snapshot->project > 0) {
            /**
             *
             * @var \Application\Entity\NmtPmProject $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtPmProject')->find($snapshot->project);
            $entity->setPmtTerm($obj);
        }

        $entity->setEmployee($snapshot->employee);
        if ($snapshot->employee > 0) {
            /**
             *
             * @var \Application\Entity\NmtHrEmployee $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtHrEmployee')->find($snapshot->employee);
            $entity->setPmtTerm($obj);
        }

        $entity->setVendor($snapshot->vendor);
        if ($snapshot->vendor > 0) {
            /**
             *
             * @var \Application\Entity\NmtBpVendor $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtBpVendor')->find($snapshot->vendor);
            $entity->setPmtTerm($obj);
        }

        $entity->setPr($snapshot->pr);
        if ($snapshot->pr > 0) {
            /**
             *
             * @var \Application\Entity\NmtProcurePr $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtProcurePr')->find($snapshot->pr);
            $entity->setPmtTerm($obj);
        }

        $entity->setVInvoice($snapshot->vInvoice);
        if ($snapshot->vInvoice > 0) {
            /**
             *
             * @var \Application\Entity\FinVendorInvoice $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->find($snapshot->vInvoice);
            $entity->setPmtTerm($obj);
        }

        $entity->setId($snapshot->id);
        $entity->setDocumentSubject($snapshot->documentSubject);
        $entity->setKeywords($snapshot->keywords);
        $entity->setIsPicture($snapshot->isPicture);
        $entity->setIsContract($snapshot->isContract);
        $entity->setSigningDate($snapshot->signingDate);
        $entity->setUrl($snapshot->url);
        $entity->setFiletype($snapshot->filetype);
        $entity->setFilename($snapshot->filename);
        $entity->setFilenameOriginal($snapshot->filenameOriginal);
        $entity->setFilePassword($snapshot->filePassword);
        $entity->setSize($snapshot->size);
        $entity->setVisibility($snapshot->visibility);
        $entity->setFolder($snapshot->folder);
        $entity->setAttachmentFolder($snapshot->attachmentFolder);
        $entity->setFolderRelative($snapshot->folderRelative);
        $entity->setChecksum($snapshot->checksum);
        $entity->setToken($snapshot->token);
        $entity->setIsActive($snapshot->isActive);
        $entity->setMarkedForDeletion($snapshot->markedForDeletion);
        $entity->setRemarks($snapshot->remarks);
        $entity->setChangeFor($snapshot->changeFor);
        $entity->setPrRowId($snapshot->prRowId);
        $entity->setTargetClass($snapshot->targetClass);
        $entity->setTargetId($snapshot->targetId);
        $entity->setTargetToken($snapshot->targetToken);
        $entity->setFileExtension($snapshot->fileExtension);
        $entity->setFileExits($snapshot->fileExits);
        $entity->setUuid($snapshot->uuid);

        return $entity;
    }

    /**
     *
     * @see AttachmentDTOAssembler
     *
     * @param NmtApplicationAttachment $entity
     * @param AttachmentSnapshot $snapshot
     * @return NULL|\Application\Domain\Attachment\AttachmentSnapshot
     */
    public static function createSnapshot(NmtApplicationAttachment $entity, AttachmentSnapshot $snapshot)
    {
        if ($entity == null || $snapshot == null) {
            return null;
        }

        // Mapping Reference
        // =====================
        // $snapshot->item= $entity->getItem();
        if ($entity->getItem() !== null) {
            $snapshot->item = $entity->getItem()->getId();
        }

        // $snapshot->po= $entity->getPo();
        if ($entity->getPo() !== null) {
            $snapshot->po = $entity->getPo()->getId();
        }

        // $snapshot->poRow= $entity->getPoRow();
        if ($entity->getPoRow() !== null) {
            $snapshot->poRow = $entity->getPoRow()->getId();
        }

        // $snapshot->qo= $entity->getQo();
        if ($entity->getQo() !== null) {
            $snapshot->qo = $entity->getQo()->getId();
        }

        // $snapshot->company= $entity->getCompany();
        if ($entity->getCompany() !== null) {
            $snapshot->company = $entity->getCompany()->getId();
        }

        // $snapshot->lastChangeBy= $entity->getLastChangeBy();
        if ($entity->getLastChangeBy() !== null) {
            $snapshot->lastChangeBy = $entity->getLastChangeBy()->getId();
        }

        // $snapshot->project= $entity->getProject();
        if ($entity->getProject() !== null) {
            $snapshot->project = $entity->getProject()->getId();
        }

        // $snapshot->employee= $entity->getEmployee();
        if ($entity->getEmployee() !== null) {
            $snapshot->employee = $entity->getEmployee()->getId();
        }

        // $snapshot->vendor= $entity->getVendor();
        if ($entity->getVendor() !== null) {
            $snapshot->vendor = $entity->getVendor()->getId();
        }

        // $snapshot->itemPurchasing= $entity->getItemPurchasing();
        if ($entity->getItemPurchasing() !== null) {
            $snapshot->itemPurchasing = $entity->getItemPurchasing()->getId();
        }

        // $snapshot->pr= $entity->getPr();
        if ($entity->getPr() !== null) {
            $snapshot->pr = $entity->getPr()->getId();
        }

        // $snapshot->vInvoice= $entity->getVInvoice();
        if ($entity->getVInvoice() !== null) {
            $snapshot->vInvoice = $entity->getVInvoice()->getId();
        }

        // $snapshot->createdBy= $entity->getCreatedBy();
        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
        }

        // Mapping Date
        // =====================
        $snapshot->createdOn = $entity->getCreatedOn();
        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d");
        }

        $snapshot->lastChangeOn = $entity->getLastChangeOn();
        if (! $entity->getLastChangeOn() == null) {
            $snapshot->lastChangeOn = $entity->getLastChangeOn()->format("Y-m-d");
        }

        $snapshot->signingDate = $entity->getSigningDate();
        if (! $entity->getSigningDate() == null) {
            $snapshot->signingDate = $entity->getSigningDate()->format("Y-m-d");
        }

        $snapshot->validFrom = $entity->getValidFrom();
        if (! $entity->getValidFrom() == null) {
            $snapshot->validFrom = $entity->getValidFrom()->format("Y-m-d");
        }

        $snapshot->validTo = $entity->getValidTo();
        if (! $entity->getValidTo() == null) {
            $snapshot->validTo = $entity->getValidTo()->format("Y-m-d");
        }

        // Mapping None-Object Field
        // =====================

        $snapshot->id = $entity->getId();
        $snapshot->documentSubject = $entity->getDocumentSubject();
        $snapshot->keywords = $entity->getKeywords();
        $snapshot->isPicture = $entity->getIsPicture();
        $snapshot->isContract = $entity->getIsContract();
        $snapshot->url = $entity->getUrl();
        $snapshot->filetype = $entity->getFiletype();
        $snapshot->filename = $entity->getFilename();
        $snapshot->filenameOriginal = $entity->getFilenameOriginal();
        $snapshot->filePassword = $entity->getFilePassword();
        $snapshot->size = $entity->getSize();
        $snapshot->visibility = $entity->getVisibility();
        $snapshot->folder = $entity->getFolder();
        $snapshot->attachmentFolder = $entity->getAttachmentFolder();
        $snapshot->folderRelative = $entity->getFolderRelative();
        $snapshot->checksum = $entity->getChecksum();
        $snapshot->token = $entity->getToken();
        $snapshot->isActive = $entity->getIsActive();
        $snapshot->markedForDeletion = $entity->getMarkedForDeletion();
        $snapshot->remarks = $entity->getRemarks();
        $snapshot->changeFor = $entity->getChangeFor();
        $snapshot->prRowId = $entity->getPrRowId();
        $snapshot->targetClass = $entity->getTargetClass();
        $snapshot->targetId = $entity->getTargetId();
        $snapshot->targetToken = $entity->getTargetToken();
        $snapshot->fileExtension = $entity->getFileExtension();
        $snapshot->fileExits = $entity->getFileExits();
        $snapshot->uuid = $entity->getUuid();

        return $snapshot;
    }
}
