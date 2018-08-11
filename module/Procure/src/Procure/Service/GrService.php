<?php
namespace Procure\Service;

use Application\Entity\NmtInventoryTrx;
use Zend\Math\Rand;

/**
 * Good Receipt Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GrService extends AbstractProcureService
{

    /**
     *
     * @param \Application\Entity\NmtProcureGr $entity
     * @param \Application\Entity\MlaUsers $u
     *            
     */
    public function doPosting($entity, $u, $isFlush = false)
    {
        if ($u == null) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        if (! $entity instanceof \Application\Entity\NmtProcureGr) {
            throw new \Exception("Invalid Argument");
        }

        $criteria = array(
            'isActive' => 1,
            'gr' => $entity
        );
        $gr_rows = $this->doctrineEM->getRepository('Application\Entity\NmtProcureGrRow')->findBy($criteria);

        if (count($gr_rows) == 0) {
            throw new \Exception("Good receipt is empty. No Posting will be made!");
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
        foreach ($gr_rows as $r) {

            $n ++;
            /** @var \Application\Entity\NmtProcureGrRow $r ; */

            // UPDATE row status
            $r->setIsPosted(1);
            $r->setIsDraft(0);
            $r->setDocStatus($entity->getDocStatus());
            $r->setRowIdentifer($entity->getSysNumber() . '-' . $n);
            $r->setRowNumber($n);
            $r->setLastchangeOn($changeOn);

            if ($r->getItem() !== null) {

                /**
                 * Double check only.
                 * Receipt of ZERO quantity not allowed
                 *
                 * @var \Procure\Controller\GrRowController ;
                 */
                if ($r->getItem()->getItemType() != \Application\Model\Constants::ITEM_TYPE_SERVICE and $r->getQuantity() > 0) {

                    if ($r->getItem()->getIsStocked() == 0) {
                        // continue;
                    }
                    $criteria = array(
                        'isActive' => 1,
                        'grRow' => $r
                    );
                    $stock_gr_entity_ck = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryTrx')->findOneBy($criteria);

                    $stock_gr_entity = null;

                    if ($stock_gr_entity_ck instanceof \Application\Entity\NmtInventoryTrx) {
                        $stock_gr_entity = $stock_gr_entity_ck;
                    } else {
                        $stock_gr_entity = new NmtInventoryTrx();
                    }

                    $stock_gr_entity->setGrRow($r);
                    $stock_gr_entity->setSourceClass(get_class($r));
                    $stock_gr_entity->setSourceId($r->getId());

                    $stock_gr_entity->setTransactionType($r->getTransactionType());
                    $stock_gr_entity->setCurrentState($entity->getCurrentState());
                    $stock_gr_entity->setDocStatus($r->getDocStatus());
                    $stock_gr_entity->setIsActive($r->getIsActive());

                    $stock_gr_entity->setVendor($entity->getVendor());
                    $stock_gr_entity->setFlow(\Application\Model\Constants::WH_TRANSACTION_IN);

                    $stock_gr_entity->setItem($r->getItem());
                    $stock_gr_entity->setPrRow($r->getPrRow());
                    $stock_gr_entity->setPoRow($r->getPoRow());
                    $stock_gr_entity->setQuantity($r->getQuantity());
                    $stock_gr_entity->setVendorItemCode($r->getVendorItemCode());
                    $stock_gr_entity->setVendorItemUnit($r->getUnit());
                    $stock_gr_entity->setVendorUnitPrice($r->getUnitPrice());
                    $stock_gr_entity->setTrxDate($entity->getGrDate());
                    $stock_gr_entity->setCurrency($entity->getCurrency());
                    $stock_gr_entity->setRemarks('PO-GR.' . $r->getRowIdentifer());
                    $stock_gr_entity->setWh($entity->getWarehouse());
                    $stock_gr_entity->setCreatedBy($u);
                    $stock_gr_entity->setCreatedOn(new \DateTime());
                    $stock_gr_entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
                    $stock_gr_entity->setChecksum(Rand::getString(32, \Application\Model\Constants::CHAR_LIST, true));

                    $stock_gr_entity->setTaxRate($r->getTaxRate());
                    $this->doctrineEM->persist($stock_gr_entity);

                    /**
                     *
                     * @todo create serial number
                     *       if item with Serial
                     *       or Fixed Asset
                     */
                    if ($r->getItem()->getMonitoredBy() == \Application\Model\Constants::ITEM_WITH_SERIAL_NO or $r->getItem()->getIsFixedAsset() == 1) {

                        for ($i = 0; $i < $r->getQuantity(); $i ++) {

                            // create serial number
                            $sn_entity = new \Application\Entity\NmtInventoryItemSerial();
                            $sn_entity->setItem($r->getItem());
                            $sn_entity->setInventoryTrx($stock_gr_entity);
                            $sn_entity->setIsActive(1);

                            $sn_entity->setSysNumber($this->controllerPlugin->getDocNumber($sn_entity));
                            $sn_entity->setCreatedBy($u);
                            $sn_entity->setCreatedOn($r->getCreatedOn());
                            $sn_entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
                            $this->doctrineEM->persist($sn_entity);
                        }
                    }

                    /**
                     *
                     * @todo create batch number
                     *       if item with Batch
                     *       or Fixed Asset
                     */
                    if ($r->getItem()->getMonitoredBy() == \Application\Model\Constants::ITEM_WITH_BATCH_NO) {}

                    if ($r->getItem()->getIsStocked() == 1) {

                        /**
                         *
                         * @todo: Create FIFO Layer
                         */
                        $fifoLayer = new \Application\Entity\NmtInventoryFifoLayer();

                        $fifoLayer->setIsClosed(0);
                        $fifoLayer->setItem($r->getItem());
                        $fifoLayer->setQuantity($r->getQuantity());

                        // will be changed uppon inventory transaction.
                        $fifoLayer->setOnhandQuantity($r->getQuantity());

                        $fifoLayer->setDocUnitPrice($r->getUnitPrice());
                        $fifoLayer->setLocalCurrency($r->getGR()
                            ->getCurrency());
                        $fifoLayer->setExchangeRate($r->getGr()
                            ->getExchangeRate());
                        $fifoLayer->setPostingDate($r->getGR()
                            ->getGrDate());
                        $fifoLayer->setSourceClass(get_class($r));
                        $fifoLayer->setSourceId($r->getID());
                        $fifoLayer->setSourceToken($r->getToken());

                        $fifoLayer->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true));
                        $fifoLayer->setCreatedBy($u);
                        $fifoLayer->setCreatedOn($r->getCreatedOn());

                        $this->doctrineEM->persist($fifoLayer);

                    /**
                     *
                     * @todo: Calculate Moving Average Price.
                     */
                    }
                }
            }

            /**
             *
             * @todo: Do Accounting Posting
             */
            $this->jeService->postGR($entity, $gr_rows, $u, $this->controllerPlugin);

            if ($isFlush == true) {
                $this->doctrineEM->flush();
            }
        }
    }

   
}
