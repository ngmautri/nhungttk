<?php
namespace Procure\Service;

use Procure\Model\Ap\AbstractAPRowPostingStrategy;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class APInvoiceService extends AbstractProcureService
{

    /**
     *
     * @param \Application\Entity\FinVendorInvoice $entity
     * @param \Application\Entity\MlaUsers $u,
     * @param bool $isFlush,
     * 
     * @return \Doctrine\ORM\EntityManager
     */
    public function post($entity, $u, $isFlush = false)
    {
        if (! $entity instanceof \Application\Entity\FinVendorInvoice) {
            throw new \Exception("Invalid Argument! Invoice can't not found.");
        }

        if (! $u instanceof \Application\Entity\MlaUsers) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        $criteria = array(
            'isActive' => 1,
            'invoice' => $entity
        );
        $ap_rows = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoiceRow')->findBy($criteria);

        if (count($ap_rows) == 0) {
            throw new \Exception("Invoice is empty. No Posting will be made!");
        }

        // OK to post
        // +++++++++++++++++++

        $changeOn = new \DateTime();

        // Assign doc number
        if ($entity->getSysNumber() == \Application\Model\Constants::SYS_NUMBER_UNASSIGNED) {
            $entity->setSysNumber($this->controllerPlugin->getDocNumber($entity));
        }

        $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_POSTED);
        $entity->setTransactionType(\Application\Model\Constants::TRANSACTION_TYPE_PURCHASED);
        $entity->setRevisionNo($entity->getRevisionNo() + 1);
        $entity->setLastchangeBy($u);
        $entity->setLastchangeOn($changeOn);
        $this->doctrineEM->persist($entity);

        $n = 0;
        foreach ($ap_rows as $r) {

            /** @var \Application\Entity\FinVendorInvoiceRow $r ; */

            // ignore row with Zero quantity
            if ($r->getQuantity() == 0) {
                $r->setIsActive(0);
                continue;
            }

            $netAmount = $r->getQuantity() * $r->getUnitPrice();
            $taxAmount = $netAmount * $r->getTaxRate() / 100;
            $grossAmount = $netAmount + $taxAmount;

            // UPDATE status
            $n ++;
            $r->setIsPosted(1);
            $r->setIsDraft(0);

            $r->setNetAmount($netAmount);
            $r->setTaxAmount($taxAmount);
            $r->setGrossAmount($grossAmount);
            $r->setDocStatus($entity->getDocStatus());
            $r->setRowIdentifer($entity->getSysNumber() . '-' . $n);
            $r->setRowNumber($n);
            $this->doctrineEM->persist($r);

            $tTransaction = $r->getTransactionType();
            $rowPostingStrategy = \Procure\Model\Ap\APRowPostingFactory::getPostingStrategy($tTransaction);

            if ($rowPostingStrategy instanceof AbstractAPRowPostingStrategy) {
                $rowPostingStrategy->setProcureService($this);
                $rowPostingStrategy->doPosting($entity, $r, $u);
            }
        }

        /**
         *
         * @todo: Do Accounting Posting
         */
        $this->jeService->postAP($entity, $ap_rows, $u, $this->controllerPlugin);

        if ($isFlush == true) {
            $this->doctrineEM->flush();
        }
    }
}
