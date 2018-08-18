<?php
namespace Procure\Service;

use Application\Entity\NmtInventoryTrx;
use Zend\Math\Rand;

/**
 * PO Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoService extends AbstractProcureService
{

    /**
     *
     * @param \Application\Entity\NmtProcurePo $entity
     * @param \Application\Entity\MlaUsers $u
     *
     */
    public function doPosting($entity, $u, $isFlush = false)
    {
        if ($u == null) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        if (! $entity instanceof \Application\Entity\NmtProcurePo) {
            throw new \Exception("Invalid Argument. PO Object not found!");
        }

        $criteria = array(
            'isActive' => 1,
            'po' => $entity
        );
        $po_rows = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePoRow')->findBy($criteria);

        if (count($po_rows) == 0) {
            throw new \Exception("PO is empty. No Posting will be made!");
        }

        // OK to post
        // ++++++++++++++++++++++++++++

        /**
         *
         * @todo Update Entitiy!
         */

        $changeOn = new \DateTime();

        // Assign doc number
        if ($entity->getSysNumber() == \Application\Model\Constants::SYS_NUMBER_UNASSIGNED) {
            $entity->setSysNumber($this->controllerPlugin->getDocNumber($entity));
        }

        // set posted
        $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_POSTED);
        $entity->setRevisionNo($entity->getRevisionNo() + 1);
        $entity->setLastchangeBy($u);
        $entity->setLastchangeOn($changeOn);
        $this->doctrineEM->persist($entity);

        $n = 0;
        foreach ($po_rows as $r) {

            /** @var \Application\Entity\NmtProcurePoRow $r ; */

            /**
             * Double check only.
             * Receipt of ZERO quantity not allowed
             */
            if ($r->getQuantity() == 0) {
                continute;
            }

            $n ++;

            // UPDATE row status
            $r->setIsPosted(1);
            $r->setIsDraft(0);
            $r->setDocStatus($entity->getDocStatus());
            $r->setRowIdentifer($entity->getSysNumber() . '-' . $n);
            $r->setRowNumber($n);
            $r->setLastchangeOn($changeOn);
        }

        if ($isFlush == true) {
            $this->doctrineEM->flush();
        }
    }

    
    /**
     *
     * @param \Application\Entity\NmtProcurePo $entity
     * @param \Application\Entity\MlaUsers $u
     *
     */
    public function updateStatus($entity, $u, $isFlush = false){
        
    }
    
    
    /**
     *
     * @param \Application\Entity\NmtProcurePo $entity
     * @param \Application\Entity\MlaUsers $u
     *
     */
    public function copyToGR($entity, $u, $isFlush = false){
        
    }
   
}
