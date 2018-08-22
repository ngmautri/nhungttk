<?php
namespace Inventory\Service;

use Application\Service\AbstractService;
use Inventory\Model\GI\GIStrategyFactory;
use Inventory\Model\GI\AbstractGIStrategy;
use Zend\Math\Rand;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class OpeningBalanceService extends AbstractService
{

    /**
     *
     * @param \Application\Entity\NmtInventoryOpeningBalance $entity
     * @param \Application\Entity\MlaUsers $u,
     * @param bool $isFlush,
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function doPosting($entity, $u, $isFlush = false)
    {
        if (! $entity instanceof \Application\Entity\NmtInventoryOpeningBalance) {
            throw new \Exception("Invalid Argument! Inventory Moverment Object can't not be found.");
        }

        if (! $u instanceof \Application\Entity\MlaUsers) {
            throw new \Exception("User can't not be identified for this transaction");
        }

        // =============

        $criteria = array(
            'isActive' => 1,
            'openingBalance' => $entity
        );
        $rows = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryOpeningBalanceRow')->findBy($criteria);

        if (count($rows) == 0) {
            throw new \Exception("Opening Balace is empty. No Posting will be made!");
        }

        // OK to post
        // +++++++++++++++++++

        // Assign doc number
        if ($entity->getSysNumber() == \Application\Model\Constants::SYS_NUMBER_UNASSIGNED) {
            $entity->setSysNumber($this->controllerPlugin->getDocNumber($entity));
        }

        $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_POSTED);
        $this->doctrineEM->persist($entity);

        $n = 0;
        foreach ($rows as $r) {
            /** @var \Application\Entity\NmtInventoryOpeningBalanceRow $r ; */

            // ignore row with Zero quantity
            if ($r->getQuantity() == 0) {
                $r->setIsActive(0);
                continue;
            }

            if ($r->getItem() == null) {
                $r->setIsActive(0);
                continue;
            }

            if ($r->getItem()->getIsStocked() == 0) {
                $r->setIsActive(0);
                continue;
            }

            $n ++;
            $r->setDocStatus($entity->getDocStatus());
            $r->setIsPosted(1);

            $this->doctrineEM->persist($r);

            // Set all current FIFO Layer is closed.
            /*
             * $sql = sprintf("Update nmt_inventory_fifo_layer set is_closed=1, is_active=0 where nmt_inventory_fifo_layer.item_id=%s", $r->getItem()->getId());
             * $stmt = $this->doctrineEM->getConnection()->prepare($sql);
             * $stmt->execute();
             */

            $criteria = array(
                'item' => $r->getItem()
            );
            $layers = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryFifoLayer')->findBy($criteria);
            if (count($layers > 0)) {

                foreach ($layers as $l) {
                    /** @var \Application\Entity\NmtInventoryFifoLayer $l ; */
                    $l->setIsClosed(1);
                    $l->setClosedOn($entity->getPostingDate());
                    $this->doctrineEM->persist($l);
                }
            }

            // Create new FIFO.

            /**
             *
             * @todo: Create FIFO Layer
             */
            $fifoLayer = new \Application\Entity\NmtInventoryFifoLayer();

            $fifoLayer->setIsClosed(0);
            $fifoLayer->setIsOpenBalance(1);
            $fifoLayer->setItem($r->getItem());
            $fifoLayer->setQuantity($r->getQuantity());

            // will be changed uppon inventory transaction.
            $fifoLayer->setOnhandQuantity($r->getQuantity());
            $fifoLayer->setDocUnitPrice($r->getUnitPrice());
            $fifoLayer->setLocalCurrency($r->getCurrency());
            $fifoLayer->setExchangeRate($r->getExchangeRate());
            $fifoLayer->setPostingDate($entity->getPostingDate());
            $fifoLayer->setSourceClass(get_class($r));
            $fifoLayer->setSourceId($r->getID());
            $fifoLayer->setSourceToken($r->getToken());

            $fifoLayer->setToken(Rand::getString(22, \Application\Model\Constants::CHAR_LIST, true));
            $fifoLayer->setCreatedBy($u);
            $fifoLayer->setCreatedOn($r->getCreatedOn());
            $fifoLayer->setRemarks("Opening Balance");
            $this->doctrineEM->persist($fifoLayer);

            // Create Journal Entry.
            
            // Create JE Row - DEBIT
            $je_row = new \Application\Entity\FinJeRow();
       
            $je_row->setPostingKey(\Finance\Model\Constants::POSTING_KEY_DEBIT);
            $je_row->setPostingCode(\Finance\Model\Constants::POSTING_KEY_DEBIT);
            $je_row->setDocAmount($r->getQuantity() * $r->getUnitPrice());
            $je_row->setLocalAmount($r->getQuantity() * $r->getUnitPrice() * $r->getExchangeRate());
   
            $je_row->setSysNumber();
            $je_row->setCreatedBy($u);
            $je_row->setCreatedOn($entity->getCreatedOn());

            $je_row->setJeDescription("Opening Balance");

            $this->doctrineEM->persist($je_row);
        }

        if ($n == 0) {
            throw new \Exception("Opening Balace is empty. No Posting will be made!");
        }

        if ($isFlush == true) {
            $this->doctrineEM->flush();
        }
    }
}
