<?php
namespace Procure\Model\Ap;

/**
 * GOOD RECEIPT - INVOICE RECEIPT RESERVAL
 *
 * This is standand case
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRIRReversalStrategy extends AbstractAPRowPostingStrategy
{

    /**
     *
     * @param \Application\Entity\FinVendorInvoice $entity
     * @param \Application\Entity\FinVendorInvoiceRow $r
     * @param \Application\Entity\MlaUsers $u
     * {@inheritdoc}
     * @see \Procure\Model\Ap\AbstractAPRowPostingStrategy::doPosting()
     */
    public function doPosting($entity, $r, $u = null, $isPosting = 1)
    {
        if (! $entity instanceof \Application\Entity\FinVendorInvoice) {
            throw new \Exception("Invalid Argument! Invoice is not found.");
        }

        if (! $r instanceof \Application\Entity\FinVendorInvoiceRow) {
            throw new \Exception("Invalid Argument! Invoice row is not found.");
        }

        if (! $u instanceof \Application\Entity\MlaUsers) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        $createdOn = new \Datetime();

        $procureSV = $this->getContextService();

        /**
         *
         * @todo: Reverse GR Procure
         * =========================
         */
        $criteria = array(
            'targetObject' => get_class($entity),
            'targetObjectId' => $entity->getId(),
        );

        /** @var \Application\Entity\NmtProcureGrRow $gr_entity ; */
        $gr_entity = $procureSV->getDoctrineEM()
            ->getRepository('Application\Entity\NmtProcureGrRow')
            ->findOneBy($criteria);
        
         if ($gr_entity !== null) {
        
            $gr_entity->setIsActive(0);
            $gr_entity->setIsReversed(1);
            $gr_entity->setReversalDate($r->getReversalDate());
            $gr_entity->setReversalReason($r->getReversalReason());

            $gr_entity->setLastchangedBy($u);
            $gr_entity->setLastchangeOn($createdOn);
            $procureSV->getDoctrineEM()->persist($gr_entity);
        }
        /**
         *
         * @todo: Reverse Serial Item
         */
        if ($r->getItem() != null) {

            $criteria = array(
                'isActive' => 1,
                'apRow' => $r,
            );

            $serial_list = $procureSV->getDoctrineEM()
                ->getRepository('Application\Entity\NmtInventoryItemSerial')
                ->findBy($criteria);

            if (count($serial_list) > 0) {
                foreach ($serial_list as $s) {

                    /** @var \Application\Entity\NmtInventoryItemSerial $s ; */
                    $s->setIsActive(0);
                    $s->setIsReversed(1);
                    $s->setReversalDate($r->getReversalDate());
                    $s->setReversalReason($r->getReversalReason());
                    $s->setLastChangeBy($u);
                    $s->setLastchangeOn($createdOn);
                    $procureSV->getDoctrineEM()->persist($s);
                }
            }

        /**
         *
         * @todo: Reversal Good Receipt WH
         * no need. it will be done in GRFromPurchaningReversal.
         */
        }
    }
}