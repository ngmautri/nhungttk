<?php
namespace Inventory\Model\GR;

use Zend\Math\Rand;

/**
 * Machine ID is required.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRfromPurchasing extends AbstractGRStrategy
{

    /**
     *
     * @param \Application\Entity\NmtInventoryTrx $trx
     * @param \Application\Entity\NmtInventoryItem $item
     * @param \Application\Entity\MlaUsers $u
     *
     * {@inheritdoc}
     * @see \Inventory\Model\GI\AbstractGIStrategy::check()
     */
    public function check($trx, $item, $u)
    {
        if (! $trx instanceof \Application\Entity\NmtInventoryTrx) {
            throw new \Exception("Invalid Argument! Inventory Moverment can't not be found.");
        }

        if (! $item instanceof \Application\Entity\NmtInventoryItem) {
            throw new \Exception("Invalid Argument! Item can't not be found.");
        }

        if (! $u instanceof \Application\Entity\MlaUsers) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        // OK to post
        // +++++++++++++++++++

        // Required machine
        if ($trx->getIssueFor() == null) {
            throw new \Exception("Invalid Argument! Machine is not give.");
        }

        if ($trx->getIssueFor() === $item) {
            throw new \Exception("Invalid Argument! It is not posible to use the same item. Please select other!");
        }
    }

    /**
     *
     * @param \Application\Entity\NmtInventoryMv $entity
     * @param \Application\Entity\MlaUsers $u
     * {@inheritdoc}
     * @see \Inventory\Model\GI\AbstractGIStrategy::doPosting()
     */
    public function doPosting($entity, $u)
    {
        $criteria = array(
            'movement' => $entity
        );

        $sort = array();

        $rows = $this->contextService->getDoctrineEM()
            ->getRepository('Application\Entity\NmtInventoryTrx')
            ->findBy($criteria, $sort);

        if (count($rows) == 0) {
            throw new \Exception("Movement is empty");
        }

        $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_POSTED);
        $entity->setIsDraft(0);
        $entity->setIsPosted(1);

        $this->contextService->getDoctrineEM()->persist($entity);
        $this->contextService->getDoctrineEM()->flush();
    }

    /**
     *
     * @param \Application\Entity\NmtInventoryMv $entity
     * @param \Application\Entity\MlaUsers $u
     * @param \DateTime $reversalDate
     *
     * {@inheritdoc}
     * @see \Inventory\Model\GI\AbstractGIStrategy::reverse()
     */
    public function reverse($entity, $u, $reversalDate, $isFlush = false)
    {}

    /**
     *
     * @param array $rows
     * @param \Application\Entity\MlaUsers $u
     * {@inheritdoc}
     * @see \Inventory\Model\GR\AbstractGRStrategy::createMovement()
     */
    public function createMovement($rows, $u, $isFlush = false)
    {
        if (! $u instanceof \Application\Entity\MlaUsers) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        if ($rows == null) {
            throw new \Exception("Invalid Argument! Nothing to create.");
        }

        $mv = new \Application\Entity\NmtInventoryMv();
        $mv->getMovementFlow(\Inventory\Model\Constants::WH_TRANSACTION_IN);
        $mv->setMovementType(\Inventory\Model\Constants::INVENTORY_GR_FROM_PURCHASING);
        $this->contextService->getDoctrineEM()->persist($mv);

        $createdOn = new \DateTime();

        $n = 0;
        foreach ($rows as $r) {

            /** @var \Application\Entity\NmtInventoryTrx $r */
            if (! $r instanceof \Application\Entity\NmtInventoryTrx) {
                continue;
            }

            $n ++;
            $r->setMovement($mv);

            // created FIFO is needed
            if ($r->getItem() !== null) {

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
                        $sn_entity->setApRow($r->getInvoiceRow());
                        $sn_entity->getGrRow($r->getGrRow());
                        $sn_entity->setInventoryTrx($r);
                        $sn_entity->setIsActive(1);
                        $sn_entity->setSysNumber($this->contextService->getControllerPlugin()->getDocNumber($sn_entity));
                        $sn_entity->setCreatedBy($u);
                        $sn_entity->setCreatedOn($createdOn);
                        $sn_entity->setToken(\Zend\Math\Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . \Zend\Math\Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
                        $this->contextService->getDoctrineEM()->persist($sn_entity);
                    }
                }

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
                    $fifoLayer->setLocalCurrency($r->getCurrency());
                    $fifoLayer->setExchangeRate($r->getExchangeRate());
                    $fifoLayer->setPostingDate($r->getTrxDate());
                    $fifoLayer->setSourceClass(get_class($r));
                    $fifoLayer->setSourceId($r->getID());
                    $fifoLayer->setSourceToken($r->getToken());

                    $fifoLayer->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true));
                    $fifoLayer->setCreatedBy($u);
                    $fifoLayer->setCreatedOn($r->getCreatedOn());
                    $this->contextService->getDoctrineEM()->persist($fifoLayer);

                /**
                 *
                 * @todo: Calculate Moving Average Price.
                 */
                }
            }
        }

        if ($n == 0) {
            throw new \Exception("Invalid Argument! Nothing to create.");
        }

        if ($isFlush == true) {
            $this->contextService->getDoctrineEM()->flush();
        }
    }
}