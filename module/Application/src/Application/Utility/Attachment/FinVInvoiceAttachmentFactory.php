<?php
namespace Application\Utility\Attachment;

use Application\Entity\NmtApplicationAttachment;

/**
 *
 * @author nmt
 *        
 */
class FinVInvoiceAttachmentFactory extends AbstractAttachmentFactory
{

    const ATTACHMENT_FOLDER = "/data/fin/attachment/v_invoice";

    /**
     *
     * {@inheritdoc}
     * @see \Application\Utility\Attachment\AbstractAttachmentFactory::saveAttachment()
     */
    public function saveAttachment()
    {
        $entity = new NmtApplicationAttachment();

        /** @var \Application\Entity\FinVendorInvoice $target ;*/
        $target = $this->getTarget();

        $criteria = array(
            "checksum" => $checksum,
            "target_class" => get_class($target),
            "target_id" => $target->getId(),
            "target_token" => $target->getToken()
        );

        $ck = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAttachment')->findby($criteria);

        if (count($ck) > 0) {
            $errors[] = 'Document: "' . $file_name . '"  exits already';
        }

        return $entity;
    }
}
