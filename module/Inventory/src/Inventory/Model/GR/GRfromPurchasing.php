<?php
namespace Inventory\Model\GR;

use Zend\Math\Rand;
use Zend\Validator\Date;
use Inventory\Model\AbstractTransactionStrategy;

/**
 * Machine ID is required.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRfromPurchasing extends AbstractTransactionStrategy
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Model\AbstractTransactionStrategy::validateRow()
     */
    public function validateRow($entity, $data, $u, $isNew, $isPosting)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Model\InventoryTransactionInterface::getFlow()
     */
    public function getFlow()
    {
        return \Inventory\Model\Constants::WH_TRANSACTION_IN;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Model\InventoryTransactionInterface::getTransactionIdentifer()
     */
    public function getTransactionIdentifer()
    {
        return \Inventory\Model\Constants::INVENTORY_GR_FROM_PURCHASING;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Model\AbstractTransactionStrategy::check()
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
     * {@inheritdoc}
     * @see \Inventory\Model\AbstractTransactionStrategy::doPosting()
     */
    public function doPosting($entity, $u, $isFlush = false)
    {        
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Model\AbstractTransactionStrategy::reverse()
     */
    public function reverse($entity, $u, $reversalDate, $reversalReason, $isFlush = false)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Model\AbstractTransactionStrategy::createMovement()
     */
    public function createMovement($rows, $u, $isFlush = false, $movementDate = null, $wareHouse = null, $trigger = null)
    {
        if (! $u instanceof \Application\Entity\MlaUsers) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        if (count($rows) == 0) {
            throw new \Exception("Invalid Argument! Nothing to create.");
        }

        if ($movementDate != null) {
            if (! $movementDate instanceof \DateTime) {
                throw new \Exception('Movement Date is not correct or empty!');
            }
        }
        if ($wareHouse != null) {
            if (! $wareHouse instanceof \Application\Entity\NmtInventoryWarehouse) {
                throw new \Exception('Warehouse is not correct or empty!');
            }
        }

        // =========Validated=======//

        $createdOn = new \DateTime();

        $mv = new \Application\Entity\NmtInventoryMv();

        $default_cur = null;
        if ($u->getCompany() instanceof \Application\Entity\NmtApplicationCompany) {
            $default_cur = $u->getCompany()->getDefaultCurrency();
        }
        $mv->setLocalCurrency($default_cur);

        $mv->setMovementFlow(\Inventory\Model\Constants::WH_TRANSACTION_IN);
        $mv->setMovementType(\Inventory\Model\Constants::INVENTORY_GR_FROM_PURCHASING);
        $mv->setTrxType(\Inventory\Model\Constants::INVENTORY_GR_FROM_PURCHASING);

        $mv->setIsPosted(1);
        $mv->setIsDraft(0);
        $mv->setIsActive(1);
        $mv->setDocStatus(\Application\Model\Constants::DOC_STATUS_POSTED);
        $this->contextService->getDoctrineEM()->persist($mv);

        $n = 0;
        foreach ($rows as $r) {

            /** @var \Application\Entity\NmtInventoryTrx $r ;*/
            if (! $r instanceof \Application\Entity\NmtInventoryTrx) {
                continue;
            }

            if ($r->getQuantity() == 0) {
                continue;
            }

            $n ++;
            $r->setMovement($mv);

            $r->setDocStatus($mv->getDocStatus());
            $r->setDocType(\Inventory\Model\Constants::INVENTORY_GR_FROM_PURCHASING);

            $this->contextService->getDoctrineEM()->persist($r);

            // create fifo layer from line
            $this->createFIFOLayerByLine($r, $u, false);
        }

        if ($n == 0) {
            throw new \Exception('Inventory transaction is not created');
        }

        $mv->setMovementDate($movementDate);
        $mv->setWarehouse($wareHouse);
        $mv->setCreatedBy($u);
        $mv->setCreatedOn($createdOn);
        $mv->setToken(\Zend\Math\Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . \Zend\Math\Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
        $mv->setSysNumber($this->contextService->getControllerPlugin()->getDocNumber($mv));
        $this->contextService->getDoctrineEM()->persist($mv);

        if ($isFlush == TRUE) {
            $this->contextService->getDoctrineEM()->flush();
        }

        if ($trigger == null) {
            $trigger = __METHOD__;
        }

        $m = sprintf('[OK] Warehouse Goods Receipt %s posted', $mv->getSysNumber());
        $this->contextService->getEventManager()->trigger('inventory.activity.log', $trigger, array(
            'priority' => \Zend\Log\Logger::INFO,
            'message' => $m,
            'createdBy' => $u,
            'createdOn' => $createdOn,
            'isFlush' => $isFlush
        ));
    }
}