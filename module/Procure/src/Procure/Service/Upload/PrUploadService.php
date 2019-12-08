<?php
namespace Procure\Service\Upload;

use Application\Service\Upload\AbstractUploadService;


/**
 * PrRowUpload Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrUploadService extends AbstractUploadService
{
    
    
    public function __construct()
    {
        $this->uploadPath = "/data/procure/attachment/pr";
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \Application\Service\Upload\AbstractUploadService::setUploadPath()
     */
    public function setUploadPath()
    {
        $this->uploadPath = "/data/procure/attachment/pr";
    }
        
    /**
     * 
     * {@inheritDoc}
     * @see \Application\Service\Upload\AbstractUploadService::setTarget()
     */
    public function setTarget($target_id, $target_token)
    {
        $criteria = array(
            'id' => $target_id,
            'token' => $target_token
        );
        
        /**
         * set Target
         * @var \Application\Entity\NmtProcurePr $target ;
         */
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePr')->findOneBy($criteria);
        
        if ($target !== null) {
            $this->targetClass = get_class($target);
            $this->targetId = $target->getId();

            $this->getAttachmentEntity()->setTargetClass(get_class($target));
            $this->getAttachmentEntity()->setTargetId($target->getId());
            $this->getAttachmentEntity()->setTargetToken($target->getToken());
            
            //addtional data 
            $this->getAttachmentEntity()->setPr($target);
         }
        
    }

    
    /**
     * 
     * {@inheritDoc}
     * @see \Application\Service\Upload\AbstractUploadService::doLogging()
     */
    public function doLogging($priority, $m, $u, $createdOn)
    {
        // Trigger Activity Log . AbtractController is EventManagerAware.
        $this->getEventManager()->trigger('procure.activity.log', __METHOD__, array(
            'priority' => $priority,
            'message' => $m . ' for PR',
            'createdBy' => $u,
            'createdOn' => $createdOn
        ));
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Application\Service\Upload\AbstractUploadService::doLoggingForChange()
     */
    public function doLoggingForChange($priority, $m, $objectId, $objectToken, $changeArray, $u, $createdOn)
    {
        $this->getEventManager()->trigger('procure.change.log', __METHOD__, array(
            'priority' => $priority,
            'message' => $m,
            'objectId' => $objectId,
            'objectToken' => $objectToken,
            'changeArray' => $changeArray,
            'changeBy' => $u,
            'changeOn' => $createdOn,
            'revisionNumber' => null,
            'changeDate' => $createdOn,
            'changeValidFrom' => $createdOn
        ));
        
    }

}
