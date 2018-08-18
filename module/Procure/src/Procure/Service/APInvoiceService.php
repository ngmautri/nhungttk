<?php
namespace Procure\Service;

use Procure\Model\Ap\AbstractAPRowPostingStrategy;
use Application\Entity\FinVendorInvoiceRow;
use Zend\Math\Rand;

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

    /**
     *
     * @param \Application\Entity\FinVendorInvoice $entity
     * @param \Application\Entity\NmtProcurePo $target
     * @param \Application\Entity\MlaUsers $u
     *
     */
    public function copyFromPO($entity, $target, $u, $isFlush = false)
    {
        if (! $entity instanceof \Application\Entity\FinVendorInvoice) {
            throw new \Exception("Invalid Argument! Invoice is not found.");
        }

        if (! $target instanceof \Application\Entity\NmtProcurePo) {
            throw new \Exception("Invalid Argument! PO Object is not found.");
        }

        if (! $u instanceof \Application\Entity\MlaUsers) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');

        $po_rows = $res->getPOStatus($target->getId(), $target->getToken());

        if ($po_rows == null) {
            throw new \Exception("PO Object is empty. Nothing is copied");
        }

        $entity->setPo($target);
        $n = 0;

        foreach ($po_rows as $l) {

            // if all received, ignore it.
            if ($l['open_ap_qty'] == 0) {
                continue;
            }

            /** @var \Application\Entity\NmtProcurePoRow $l ; */
            $r = $l[0];

            $n ++;
            $row_tmp = new FinVendorInvoiceRow();
            $row_tmp->setDocStatus($entity->getDocStatus());

            // Goods and Invoice receipt
            $row_tmp->setTransactionType(\Application\Model\Constants::PROCURE_TRANSACTION_TYPE_GRIR);

            $row_tmp->setInvoice($entity);
            $row_tmp->setIsDraft(1);
            $row_tmp->setIsActive(1);
            $row_tmp->setIsPosted(0);

            $row_tmp->setRowNumber($n);

            $row_tmp->setCurrentState("DRAFT");
            $row_tmp->setPoRow($r);
            $row_tmp->setPrRow($r->getPrRow());
            $row_tmp->setItem($r->getItem());
            $row_tmp->setQuantity($l['open_ap_qty']);

            $row_tmp->setUnit($r->getUnit());
            $row_tmp->setUnitPrice($r->getUnitPrice());
            $row_tmp->setTaxRate($r->getTaxRate());

            $netAmount = $row_tmp->getQuantity() * $row_tmp->getUnitPrice();
            $taxAmount = $netAmount * $row_tmp->getTaxRate() / 100;
            $grossAmount = $netAmount + $taxAmount;

            $row_tmp->setNetAmount($netAmount);
            $row_tmp->setTaxAmount($taxAmount);
            $row_tmp->setGrossAmount($grossAmount);

            $row_tmp->setCreatedBy($u);
            $row_tmp->setCreatedOn(new \DateTime());
            $row_tmp->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
            $row_tmp->setRemarks("Ref: PO#" . $r->getRowIdentifer());
            
            $row_tmp->setExwUnitPrice($r->getExwUnitPrice());
            $row_tmp->setDiscountRate($r->getDiscountRate());
            
            $this->doctrineEM->persist($row_tmp);
        }

        if ($n == 0) {
            throw new \Exception("P/O is billed fully!");
        }

        if ($isFlush == true) {
            $this->doctrineEM->flush();
        }
    }

    /**
     *
     * @param \Application\Entity\FinVendorInvoice $entity
     * @param \Application\Entity\NmtProcureGr $target
     * @param \Application\Entity\MlaUsers $u
     *
     */
    public function copyFromGR($entity, $target, $u, $isFlush = false)
    {
        if (! $entity instanceof \Application\Entity\FinVendorInvoice) {
            throw new \Exception("Invalid Argument! Invoice is not found.");
        }

        if (! $target instanceof \Application\Entity\NmtProcureGr) {
            throw new \Exception("Invalid Argument! GR Object is not found.");
        }

        if (! $u instanceof \Application\Entity\MlaUsers) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        $gr_rows = $res->getGRStatus($target->getId(), $target->getToken());

        if ($gr_rows == null) {
            throw new \Exception("GR Object is empty. Nothing is copied");
        }

        $n = 0;
        foreach ($gr_rows as $l) {

            // if all received, ignore it.
            if ($l['open_ap_qty'] == 0) {
                continue;
            }

            /** @var \Application\Entity\NmtProcureGrRow $l ; */
            $r = $l[0];

            $n ++;
            $row_tmp = new FinVendorInvoiceRow();
            $row_tmp->setDocStatus($entity->getDocStatus());
            // $row_tmp->setTransactionType($entity->getTransactionStatus());

            // Goods receipt, Invoice Not receipt
            $row_tmp->setTransactionType(\Application\Model\Constants::PROCURE_TRANSACTION_TYPE_GRNI);

            /**
             *
             * @todo Change entity
             */
            $row_tmp->setInvoice($entity);
            $row_tmp->setIsDraft(1);
            $row_tmp->setIsActive(1);
            $row_tmp->setIsPosted(0);

            $row_tmp->setCurrentState("DRAFT");
            $row_tmp->setGrRow($r);
            $row_tmp->setPrRow($r->getPrRow());
            $row_tmp->setPoRow($r->getPoRow());

            $row_tmp->setItem($r->getItem());
            $row_tmp->setQuantity($l['open_ap_qty']);

            $row_tmp->setUnit($r->getUnit());
            $row_tmp->setUnitPrice($r->getUnitPrice());
            $row_tmp->setTaxRate($r->getTaxRate());

            $netAmount = $row_tmp->getQuantity() * $row_tmp->getUnitPrice();
            $taxAmount = $netAmount * $row_tmp->getTaxRate() / 100;
            $grossAmount = $netAmount + $taxAmount;

            $row_tmp->setNetAmount($netAmount);
            $row_tmp->setTaxAmount($taxAmount);
            $row_tmp->setGrossAmount($grossAmount);

            $row_tmp->setCreatedBy($u);
            $row_tmp->setCreatedOn(new \DateTime());
            $row_tmp->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
            $row_tmp->setRemarks("Ref: GR #" . $r->getRowIdentifer());

            $this->doctrineEM->persist($row_tmp);
        }

        if ($n == 0) {
            throw new \Exception("GR is received fully!");
        }

        if ($isFlush == true) {
            $this->doctrineEM->flush();
        }
    }
}
