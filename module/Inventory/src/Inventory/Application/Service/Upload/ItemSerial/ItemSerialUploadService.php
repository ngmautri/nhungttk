<?php
namespace Inventory\Application\Service\Upload;

use Application\Service\Upload\AbstractUploadService;

/**
 * PrRowUpload Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemSerialUploadService extends AbstractUploadService
{

    public function __construct()
    {
        $this->uploadPath = "/data/inventory/attachment/serial";
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Service\Upload\AbstractUploadService::setUploadPath()
     */
    public function setUploadPath()
    {
        $this->uploadPath = "/data/inventory/attachment/serial";
    }

    /**
     *
     * {@inheritdoc}
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
         *
         * @var \Application\Entity\NmtInventoryItemSerial $target ;
         */
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemSerial')->findOneBy($criteria);

        if ($target !== null) {
            $this->targetClass = get_class($target);
            $this->targetId = $target->getId();

            $this->getAttachmentEntity()->setTargetClass(get_class($target));
            $this->getAttachmentEntity()->setTargetId($target->getId());
            $this->getAttachmentEntity()->setTargetToken($target->getToken());

            // addtional data
            // $this->getAttachmentEntity()->setItem($target);
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Service\Upload\AbstractUploadService::doLogging()
     */
    public function doLogging($priority, $m, $u, $createdOn)
    {
        // Trigger Activity Log . AbtractController is EventManagerAware.
        $this->getEventManager()->trigger('inventory.activity.log', __METHOD__, array(
            'priority' => $priority,
            'message' => $m . ' for Item',
            'createdBy' => $u,
            'createdOn' => $createdOn
        ));
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Service\Upload\AbstractUploadService::doLoggingForChange()
     */
    public function doLoggingForChange($priority, $m, $objectId, $objectToken, $changeArray, $u, $createdOn)
    {
        $this->getEventManager()->trigger('item.change.log', __METHOD__, array(
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
