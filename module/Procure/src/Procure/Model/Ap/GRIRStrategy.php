<?php
namespace Procure\Model\Ap;

/**
 * GOOD RECEIPT - INVOICE RECEIPT
 *
 * This is standand case
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRIRStrategy extends AbstractAPRowPostingStrategy
{

    /**
     * @param \Application\Entity\FinVendorInvoice $entity ;
     * @param \Application\Entity\FinVendorInvoiceRow $r ;
     * @param \Application\Entity\MlaUsers $u ;
 
     * {@inheritdoc}
     * @see \Procure\Model\Ap\AbstractAPRowPostingStrategy::doPosting()
     */
    public function doPosting($entity, $r, $u=null)
    {
        $createdOn = new \Datetime();

        $procureSV = $this->getProcureService();

        // create procure GR, even no PR, PO.
        $criteria = array(
            'isActive' => 1,
            'apInvoiceRow' => $entity
        );
        $gr_entity_ck = $procureSV->getDoctrineEM()->getRepository('Application\Entity\NmtProcureGrRow')->findOneBy($criteria);

        if (! $gr_entity_ck == null) {
            $gr_entity = $gr_entity_ck;
        } else {
            $gr_entity = new \Application\Entity\NmtProcureGrRow();
        }

        // PROCURE GOOD Receipt to clear PR, PO.
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
        $gr_entity->setToken(\Zend\Math\Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . \Zend\Math\Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
       $procureSV->getDoctrineEM()->persist($gr_entity);

        if ($r->getItem() !== null) {

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
            $stock_gr_entity_ck = $procureSV->getDoctrineEM()->getRepository('Application\Entity\NmtInventoryTrx')->findOneBy($criteria);

            if (! $stock_gr_entity_ck == null) {
                $stock_gr_entity = $stock_gr_entity_ck;
            } else {
                $stock_gr_entity = new \Application\Entity\NmtInventoryTrx();
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
            $stock_gr_entity->setToken(\Zend\Math\Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . \Zend\Math\Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
            $procureSV->getDoctrineEM()->persist($stock_gr_entity);

            /**
             *
             * @todo create serial number
             *       if item with Serial
             *       or Fixed Asset
             */
            if ($r->getItem()->getMonitoredBy() == \Application\Model\Constants::ITEM_WITH_SERIAL_NO or $r->getItem()->getIsFixedAsset() == 1) {

                for ($i = 0; $i < $r->getQuantity(); $i ++) {

                    // create new serial number
                    $sn_entity = new \Application\Entity\NmtInventoryItemSerial();

                    $sn_entity->setItem($r->getItem());
                    $sn_entity->setApRow($r);

                    $sn_entity->setInventoryTrx($stock_gr_entity);
                    $sn_entity->setIsActive(1);
                    $sn_entity->setSysNumber($this->procureService->getControllerPlugin()
                        ->getDocNumber($sn_entity));
                    $sn_entity->setCreatedBy($u);
                    $sn_entity->setCreatedOn($createdOn);
                    $sn_entity->setToken(\Zend\Math\Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . \Zend\Math\Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
                    $procureSV->getDoctrineEM()->persist($sn_entity);
                }
            }

            /**
             *
             * @todo create batch number
             *       if item with Batch
             *       or Fixed Asset
             */
            if ($r->getItem()->getMonitoredBy() == \Application\Model\Constants::ITEM_WITH_BATCH_NO) {}

            /**
             *
             * @todo: Create FIFO Layer
             */
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

                $fifoLayer->setToken(\Zend\Math\Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true));
                $fifoLayer->setCreatedBy($u);
                $fifoLayer->setCreatedOn($r->getCreatedOn());

                $procureSV->getDoctrineEM()->persist($fifoLayer);
            }
        }
    }
}