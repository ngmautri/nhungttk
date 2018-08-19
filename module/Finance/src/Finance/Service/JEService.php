<?php
namespace Finance\Service;

use Application\Entity\NmtInventoryTrx;
use Doctrine\ORM\EntityManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Math\Rand;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class JEService implements EventManagerAwareInterface
{

    protected $doctrineEM;

    protected $eventManager;

    /**
     *
     * @param \Application\Entity\FinVendorInvoice $entity
     *
     * @param \Application\Entity\MlaUsers $u
     *
     * @param \Application\Controller\Plugin\NmtPlugin $nmtPlugin
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function postAP($entity, $rows, $u, $nmtPlugin, $isFlush = false)
    {
        if (! $entity instanceof \Application\Entity\FinVendorInvoice) {
            throw new \Exception("Invalid Argument! Invoice is expected");
        }

        if ($u == null) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        if (count($rows) == 0) {
            throw new \Exception("Invoice is empty. No Posting will be made!");
        }

        /**
         * Debit: Inventory, Expenses
         * Credit: Payable to supplier.
         */

        // Create JE

        $je = new \Application\Entity\FinJe();
        $je->setCurrency($entity->getCurrency());
        $je->setLocalCurrency($entity->getLocalCurrency());
        $je->setExchangeRate($entity->getExchangeRate());

        $je->setPostingDate($entity->getPostingDate());
        $je->setDocumentDate($entity->getPostingDate());
        $je->setPostingPeriod($entity->getPostingPeriod());

        $je->getDocType("JE");
        $je->setCreatedBy($u);
        $je->setCreatedOn($entity->getCreatedOn());
        $je->setSysNumber($nmtPlugin->getDocNumber($je));

        $je->setSourceClass(get_class($entity));
        $je->setSourceId($entity->getId());
        $je->setSourceToken($entity->getToken());

        $this->doctrineEM->persist($je);

        $n = 0;
        $total_credit = 0;
        $total_local_credit = 0;

        foreach ($rows as $r) {
            $n ++;
            /** @var \Application\Entity\FinVendorInvoiceRow $r ; */

            // ignore row with Zero quantity
            if ($r->getQuantity() == 0) {
                $r->setIsActive(0);
                continue;
            }

            if ($r->getUnitPrice() > 0) {

                // Create JE Row - DEBIT
                $je_row = new \Application\Entity\FinJeRow();
                $je_row->setJe($je);

                $je_row->setGlAccount($r->getGlAccount());
                $je_row->setCostCenter($r->getCostCenter());
                $je_row->setPostingKey(\Finance\Model\Constants::POSTING_KEY_DEBIT);
                $je_row->setPostingCode(\Finance\Model\Constants::POSTING_KEY_DEBIT);
                $je_row->setDocAmount($r->getQuantity() * $r->getUnitPrice());
                $je_row->setLocalAmount($r->getQuantity() * $r->getUnitPrice() * $je->getExchangeRate());

                $total_credit = $total_credit + $r->getQuantity() * $r->getUnitPrice();
                $total_local_credit = $total_local_credit + $r->getQuantity() * $r->getUnitPrice() * $je->getExchangeRate();

                $je_row->setSysNumber($je->getSysNumber() . "-" . $n);

                $je_row->setCreatedBy($u);
                $je_row->setCreatedOn($entity->getCreatedOn());

                $this->doctrineEM->persist($je_row);
            }
        }

        if ($total_credit > 0) {

            // Create JE Row - Credit
            $je_row = new \Application\Entity\FinJeRow();

            $je_row->setJe($je);

            /**
             *
             * @todo: Using Control G/L account of Vendor
             */
            $criteria = array(
                'id' => 4
            );
            $gl_account = $this->doctrineEM->getRepository('Application\Entity\FinAccount')->findOneBy($criteria);
            $je_row->setGlAccount($gl_account);
            $je_row->setPostingKey(\Finance\Model\Constants::POSTING_KEY_CREDIT);
            $je_row->setPostingCode(\Finance\Model\Constants::POSTING_KEY_CREDIT);

            $je_row->setDocAmount($total_credit);
            $je_row->setLocalAmount($total_local_credit);

            $je_row->setCreatedBy($u);
            $je_row->setCreatedOn($entity->getCreatedOn());

            $n = $n + 1;
            $je_row->setSysNumber($je->getSysNumber() . "-" . $n);
            $this->doctrineEM->persist($je_row);

            // Create Vendor Balance.

            $vendor_bl_row = new \Application\Entity\NmtBpVendorBl();
            $vendor_bl_row->setVendor($entity->getVendor());
            $vendor_bl_row->setCompany($entity->getCompany());
            $vendor_bl_row->setPostingCode(\Finance\Model\Constants::POSTING_KEY_CREDIT);
            $vendor_bl_row->setDocAmount($total_credit);
            $vendor_bl_row->setLocalAmount($total_local_credit);
            $vendor_bl_row->setExchangeRate($entity->getExchangeRate());
            $vendor_bl_row->setGlAccount($gl_account);
            $vendor_bl_row->setCreatedBy($u);
            $vendor_bl_row->setCreatedOn($entity->getCreatedOn());
            $vendor_bl_row->setPostingDate($entity->getPostingDate());
            $vendor_bl_row->setPostingPeriod($entity->getPostingPeriod());
            $vendor_bl_row->setReference($entity->getInvoiceNo());
            $vendor_bl_row->setDocumentDate($entity->getInvoiceDate());
            $vendor_bl_row->setSysNumber($entity->getSysNumber());

            /**
             *
             * @todo: Set SourceID, Class, etc.
             */

            $this->doctrineEM->persist($vendor_bl_row);
        }

        if ($isFlush == true) {
            $this->doctrineEM->flush();
        }
    }

    /**
     *
     * @param \Application\Entity\NmtProcureGr $entity
     *
     * @param \Application\Entity\MlaUsers $u
     *
     * @param \Application\Controller\Plugin\NmtPlugin $nmtPlugin
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function postGR($entity, $rows, $u, $nmtPlugin, $isFlush = true)
    {
        if (! $entity instanceof \Application\Entity\NmtProcureGr) {
            throw new \Exception("Invalid Argument");
        }

        if ($u == null) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        if ($nmtPlugin == null) {
            throw new \Exception("Invalid Argument! plugin not found.");
        }

        if (count($rows) == 0) {
            throw new \Exception("Good receipt is empty. No Posting will be made!");
        }

        /**
         * Debit: Inventory, Expenses
         * Credit: Payable to supplier.
         */

        // Create JE

        $je = new \Application\Entity\FinJe();
        $je->setCurrency($entity->getCurrency());
        $je->setLocalCurrency($entity->getLocalCurrency());
        $je->setExchangeRate($entity->getExchangeRate());

        $je->setPostingDate($entity->getGrDate());
        $je->setDocumentDate($entity->getGrDate());
        $je->setPostingPeriod($entity->getPostingPeriod());

        $je->getDocType("JE");
        $je->setCreatedBy($u);
        $je->setCreatedOn($entity->getCreatedOn());
        $je->setSysNumber($nmtPlugin->getDocNumber($je));

        $je->setSourceClass(get_class($entity));
        $je->setSourceId($entity->getId());
        $je->setSourceToken($entity->getToken());

        $this->doctrineEM->persist($je);

        $n = 0;
        $total_credit = 0;
        $total_local_credit = 0;

        foreach ($rows as $r) {
            $n ++;
            /** @var \Application\Entity\NmtProcureGrRow $r ; */

            // No need to create JE for ZERO value row.

            if ($r->getUnitPrice() > 0) {

                // Create JE Row - DEBIT
                $je_row = new \Application\Entity\FinJeRow();
                $je_row->setJe($je);

                $je_row->setGlAccount($r->getGlAccount());
                $je_row->setPostingKey(\Finance\Model\Constants::POSTING_KEY_DEBIT);
                $je_row->setPostingCode(\Finance\Model\Constants::POSTING_KEY_DEBIT);

                $je_row->setDocAmount($r->getQuantity() * $r->getUnitPrice());
                $je_row->setLocalAmount($r->getQuantity() * $r->getUnitPrice() * $je->getExchangeRate());

                $total_credit = $total_credit + $r->getQuantity() * $r->getUnitPrice();
                $total_local_credit = $total_local_credit + $r->getQuantity() * $r->getUnitPrice() * $je->getExchangeRate();

                $je_row->setSysNumber($je->getSysNumber() . "-" . $n);

                $je_row->setCreatedBy($u);
                $je_row->setCreatedOn($entity->getCreatedOn());

                $this->doctrineEM->persist($je_row);
            }
        }

        if ($total_credit > 0) {

            // Create JE Row - Credit - AP account
            $je_row = new \Application\Entity\FinJeRow();

            $je_row->setJe($je);

            // Credit on GR - NI for clearing later.
            $criteria = array(
                'id' => 5
            );
            $gl_account = $this->doctrineEM->getRepository('Application\Entity\FinAccount')->findOneBy($criteria);
            $je_row->setGlAccount($gl_account);
            $je_row->setPostingKey(\Finance\Model\Constants::POSTING_KEY_CREDIT);
            $je_row->setPostingCode(\Finance\Model\Constants::POSTING_KEY_CREDIT);
            $je_row->setDocAmount($total_credit);
            $je_row->setLocalAmount($total_local_credit);

            $je_row->setCreatedBy($u);
            $je_row->setCreatedOn($entity->getCreatedOn());
            $n = $n + 1;
            $je_row->setSysNumber($je->getSysNumber() . "-" . $n);

            $this->doctrineEM->persist($je_row);
        }

        if ($isFlush == True) {
            $this->doctrineEM->flush();
        }
    }

    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @return \Procure\Service\GrService
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Zend\EventManager\EventManagerAwareInterface::setEventManager()
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $eventManager->setIdentifiers(array(
            __CLASS__
        ));
        $this->eventManager = $eventManager;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Zend\EventManager\EventsCapableInterface::getEventManager()
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }
}
