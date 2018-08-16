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

            /** @todo: Using Control G/L account of Vendor*/
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
            
            /** @todo: Set SourceID, Class, etc. */            

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

        $criteria = array(
            'isActive' => 1,
            'gr' => $entity
        );
        $rows = $this->doctrineEM->getRepository('Application\Entity\NmtProcureGrRow')->findBy($criteria);

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
     * @param \Application\Entity\FinVendorInvoice $entity
     * @param \Application\Entity\MlaUsers $u,
     * @param \Application\Controller\Plugin\NmtPlugin $nmtPlugin
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function post($entity, $u, $nmtPlugin)
    {
        if (! $entity instanceof \Application\Entity\FinVendorInvoice) {
            throw new \Exception("Invalid Argument! Invoice can't not found.");
        }

        if ($u == null) {
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

        $n = 0;
        foreach ($ap_rows as $r) {

            /** @var \Application\Entity\FinVendorInvoiceRow $r ; */

            // ignore row with Zero quantity
            if ($r->getQuantity() == 0) {
                $r->setIsActive(0);
                continue;
            }

            $createdOn = new \DateTime();

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

            // Posting upon transaction type.
            /**
             * GR-NI
             * ++++++++++++++++++++++
             */
            if ($r->getTransactionType() === \Application\Model\Constants::PROCURE_TRANSACTION_TYPE_GRNI) :
            //clearing            
            endif;

            /**
             * Good receipt and Invoice receipt
             * GR-IR
             * ++++++++++++++++++++++
             */
            if ($r->getTransactionType() === \Application\Model\Constants::PROCURE_TRANSACTION_TYPE_GRIR) :

                // create procure GR, even no PR, PO.
                $criteria = array(
                    'isActive' => 1,
                    'apInvoiceRow' => $r
                );
                $gr_entity_ck = $this->doctrineEM->getRepository('Application\Entity\NmtProcureGrRow')->findOneBy($criteria);

                if (! $gr_entity_ck == null) {
                    $gr_entity = $gr_entity_ck;
                } else {
                    $gr_entity = new \Application\Entity\NmtProcureGrRow();
                }

                $gr_entity->setIsActive(1);
                $gr_entity->setInvoice($entity);
                $gr_entity->setApInvoiceRow($r);

                $gr_entity->setItem($r->getItem());
                $gr_entity->setPrRow($r->getPrRow());
                $gr_entity->setPoRow($r->getPoRow());

                $gr_entity->setTargetObject(get_class($entity));
                $gr_entity->setTargetObjectId($entity->getId());
                $gr_entity->setTransactionType($r->getTransactionType());

                $gr_entity->setIsDraft($r->getIsDraft());
                $gr_entity->setIsPosted($r->getIsPosted());
                $gr_entity->setDocStatus($r->getDocStatus());

                $gr_entity->setQuantity($r->getQuantity());
                $gr_entity->setUnit($r->getUnit());
                $gr_entity->setUnitPrice($r->getUnitPrice());
                $gr_entity->setNetAmount($r->getNetAmount());
                $gr_entity->setTaxRate($r->getTaxRate());
                $gr_entity->setTaxAmount($r->getTaxAmount());
                $gr_entity->setGrossAmount($r->getGrossAmount());
                $gr_entity->setDiscountRate($r->getDiscountRate());

                $gr_entity->setCreatedBy($u);
                $gr_entity->setCreatedOn($createdOn);
                $gr_entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
                $this->doctrineEM->persist($gr_entity);

                /**
                 * create stock good receipt.
                 * only for item controlled inventory
                 * ===================
                 */

                /**
                 *
                 * @todo: only for item with stock control.
                 */
                if ($r->getItem()->getIsStocked() == 0) {
                    // continue;
                }

                $criteria = array(
                    'isActive' => 1,
                    'invoiceRow' => $r
                );
                $stock_gr_entity_ck = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryTrx')->findOneBy($criteria);

                if (! $stock_gr_entity_ck == null) {
                    $stock_gr_entity = $stock_gr_entity_ck;
                } else {
                    $stock_gr_entity = new NmtInventoryTrx();
                }

                $stock_gr_entity->setIsActive(1);
                $stock_gr_entity->setTrxDate($entity->getGrDate());

                $stock_gr_entity->setVendorInvoice($entity);
                $stock_gr_entity->setInvoiceRow($r);
                $stock_gr_entity->setItem($r->getItem());
                $stock_gr_entity->setPrRow($r->getPrRow());
                $stock_gr_entity->setPoRow($r->getPoRow());
                $stock_gr_entity->setGrRow($gr_entity);

                $stock_gr_entity->setIsDraft($r->getIsDraft());
                $stock_gr_entity->setIsPosted($r->getIsPosted());
                $stock_gr_entity->setDocStatus($r->getDocStatus());

                $stock_gr_entity->setSourceClass(get_class($r));
                $stock_gr_entity->setSourceId($r->getId());

                $stock_gr_entity->setTransactionType($r->getTransactionType());
                $stock_gr_entity->setCurrentState($entity->getCurrentState());

                $stock_gr_entity->setVendor($entity->getVendor());
                $stock_gr_entity->setFlow(\Application\Model\Constants::WH_TRANSACTION_IN);

                $stock_gr_entity->setQuantity($r->getQuantity());
                $stock_gr_entity->setVendorItemCode($r->getVendorItemCode());
                $stock_gr_entity->setVendorItemUnit($r->getUnit());
                $stock_gr_entity->setVendorUnitPrice($r->getUnitPrice());
                $stock_gr_entity->setTrxDate($entity->getGrDate());
                $stock_gr_entity->setCurrency($entity->getCurrency());
                $stock_gr_entity->setTaxRate($r->getTaxRate());

                $stock_gr_entity->setRemarks('AP Row' . $r->getRowIdentifer());
                $stock_gr_entity->setWh($entity->getWarehouse());
                $stock_gr_entity->setCreatedBy($u);
                $stock_gr_entity->setCreatedOn($createdOn);
                $stock_gr_entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
                $this->doctrineEM->persist($stock_gr_entity);

                /**
                 *
                 * @todo create serial number
                 *       if item with Serial
                 *       or Fixed Asset
                 */
                if ($r->getItem() !== null) {
                    if ($r->getItem()->getMonitoredBy() == \Application\Model\Constants::ITEM_WITH_SERIAL_NO or $r->getItem()->getIsFixedAsset() == 1) {

                        for ($i = 0; $i < $r->getQuantity(); $i ++) {

                            // create new serial number
                            $sn_entity = new \Application\Entity\NmtInventoryItemSerial();

                            $sn_entity->setItem($r->getItem());
                            $sn_entity->setApRow($r);

                            $sn_entity->setInventoryTrx($stock_gr_entity);
                            $sn_entity->setIsActive(1);
                            $sn_entity->setSysNumber($nmtPlugin->getDocNumber($sn_entity));
                            $sn_entity->setCreatedBy($u);
                            $sn_entity->setCreatedOn($createdOn);
                            $sn_entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
                            $this->doctrineEM->persist($sn_entity);
                        }
                    }
                }

                /**
                 *
                 * @todo: Create FIFO Layer
                 */
                if ($r->getItem() !== null) {
                    if ($r->getItem()->getIsStocked() == 1) {
                        $fifoLayer = new \Application\Entity\NmtInventoryFifoLayer();

                        $fifoLayer->setIsClosed(0);
                        $fifoLayer->setItem($r->getItem());
                        $fifoLayer->setQuantity($r->getQuantity());

                        // will be changed uppon inventory transaction.
                        $fifoLayer->setOnhandQuantity($r->getQuantity());

                        $fifoLayer->setDocUnitPrice($r->getUnitPrice());
                        $fifoLayer->setLocalCurrency($r->getInvoice()
                            ->getCurrency());
                        $fifoLayer->setExchangeRate($r->getInvoice()
                            ->getExchangeRate());
                        $fifoLayer->setPostingDate($r->getInvoice()
                            ->getPostingDate());
                        $fifoLayer->setSourceClass(get_class($r));
                        $fifoLayer->setSourceId($r->getID());
                        $fifoLayer->setSourceToken($r->getToken());

                        $fifoLayer->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true));
                        $fifoLayer->setCreatedBy($u);
                        $fifoLayer->setCreatedOn($r->getCreatedOn());

                        $this->doctrineEM->persist($fifoLayer);
                    }
                }
            
            endif;

        }

        $this->doctrineEM->flush();
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
