<?php
namespace Procure\Service;

use Application\Entity\NmtInventoryTrx;
use Zend\Math\Rand;
use Application\Entity\NmtProcureGrRow;
use Application\Service\AbstractService;
use Inventory\Model\GR\AbstractGRStrategy;
use Inventory\Model\GR\GRStrategyFactory;

/**
 * Good Receipt Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GrService extends AbstractService
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
            throw new \Exception("Invalid Argument. Good receipt is not found!");
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
        $inventory_trx_rows = array();
        foreach ($gr_rows as $r) {

            /** @var \Application\Entity\NmtProcureGrRow $r ; */

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

            if ($r->getItem() !== null) {

                if ($r->getItem()->getIsStocked() == 1) {
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

                    $stock_gr_entity->setGr($entity);
                    $stock_gr_entity->setGrRow($r);
                    $stock_gr_entity->setExchangeRate($r->getGr()
                        ->getExchangeRate());
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

                    $inventory_trx_rows[] = $stock_gr_entity;
                }
            }

            if ($n == 0) {
                throw new \Exception("No Posting will be made!");
            }

            /**
             *
             * @todo: Do Accounting Posting
             */
            $this->jeService->postGR($entity, $gr_rows, $u, $this->controllerPlugin);

            $this->doctrineEM->flush();

            try {
                $inventoryPostingStrategy = GRStrategyFactory::getGRStrategy(\Inventory\Model\Constants::INVENTORY_GR_FROM_PURCHASING);

                if (! $inventoryPostingStrategy instanceof AbstractGRStrategy) {
                    throw new \Exception("Posting strategy is not identified for this inventory movement type!");
                }

                // do posting now
                $inventoryPostingStrategy->setContextService($this);
                $inventoryPostingStrategy->createMovement($inventory_trx_rows, $u, true, $entity->getGrDate(),$entity->getWarehouse());
            } catch (\Exception $e) {
                // left bank.
                
                $m = sprintf('[ERROR] %s', $e->getMessage());
                $this->getEventManager()->trigger('inventory.activity.log', __METHOD__, array(
                    'priority' => \Zend\Log\Logger::INFO,
                    'message' => $m,
                    'createdBy' => $u,
                    'createdOn' => $changeOn
                ));
            }
            
            $m = sprintf('[OK] Goods Receipt %s posted', $entity->getSysNumber());
            $this->getEventManager()->trigger('procure.activity.log', __METHOD__, array(
                'priority' => \Zend\Log\Logger::INFO,
                'message' => $m,
                'createdBy' => $u,
                'createdOn' => $changeOn
            ));
        }
    }

    /**
     *
     * @param \Application\Entity\NmtProcureGr $entity
     * @param \Application\Entity\NmtProcurePo $target
     * @param \Application\Entity\MlaUsers $u
     *
     */
    public function copyFromPO($entity, $target, $u, $isFlush = false)
    {
        if (! $entity instanceof \Application\Entity\NmtProcureGr) {
            throw new \Exception("Invalid Argument! GR Object is not found.");
        }

        if (! $target instanceof \Application\Entity\NmtProcurePo) {
            throw new \Exception("Invalid Argument! PO Object is not found.");
        }

        if (! $u instanceof \Application\Entity\MlaUsers) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        $createdOn = new \DateTime();

        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        $po_rows = $res->getPOStatus($target->getId(), $target->getToken());

        if ($po_rows == null) {
            throw new \Exception("PO is empty!");
        }

        $n = 0;
        foreach ($po_rows as $l) {

            // if all received, ignore it.
            if ($l['open_gr_qty'] == 0) {
                continue;
            }

            /** @var \Application\Entity\NmtProcurePoRow $l ; */
            $r = $l[0];

            $n ++;
            $row_tmp = new NmtProcureGrRow();
            $row_tmp->setDocStatus($entity->getDocStatus());

            // Goods receipt, Invoice Not receipt
            $row_tmp->setTransactionType(\Application\Model\Constants::PROCURE_TRANSACTION_TYPE_GRNI);
            $row_tmp->setTransactionStatus(\Application\Model\Constants::PROCURE_TRANSACTION_STATUS_PENDING);

            $row_tmp->setGr($entity);
            $row_tmp->setIsDraft(1);
            $row_tmp->setIsPosted(0);
            $row_tmp->setIsActive(1);
            $row_tmp->setCurrentState("DRAFT");

            $row_tmp->setPoRow($r);
            $row_tmp->setPrRow($r->getPrRow());
            $row_tmp->setItem($r->getItem());

            $row_tmp->setQuantity($l['open_gr_qty']);

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
            $row_tmp->setCreatedOn($createdOn);

            $row_tmp->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
            $row_tmp->setRemarks("Ref: PO #" . $r->getRowIdentifer());
            $row_tmp->setExwUnitPrice($r->getExwUnitPrice());

            $this->doctrineEM->persist($row_tmp);
        }

        if ($n == 0) {
            throw new \Exception("PO is empty!");
        }

        if ($isFlush == True) {
            $this->doctrineEM->flush();
        }
    }


    /**
     *
     * @param \Application\Entity\NmtProcureGr $entity
     * @param \Application\Entity\NmtProcurePo $target
     * @param \Application\Entity\MlaUsers $u
     *
     */
    public function selectFromPO ($entity, $target, $u, $isFlush = false){
        
    }

}
