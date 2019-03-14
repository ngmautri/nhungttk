<?php
namespace Inventory\Model\GI;


use Inventory\Service\FIFOLayerService;

/**
 * Machine ID is required.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GIforRepairMachine extends \Inventory\Model\AbstractTransactionStrategy
{

    /**
     *
     * {@inheritDoc}
     * @see \Inventory\Model\InventoryTransactionInterface::getFlow()
     */
    public function getFlow()
    {
        return \Application\Model\Constants::WH_TRANSACTION_OUT;
        
    }

    /**
     *
     * {@inheritDoc}
     * @see \Inventory\Model\InventoryTransactionInterface::getTransactionIdentifer()
     */
    public function getTransactionIdentifer()
    {
        return \Inventory\Model\Constants::INVENTORY_GI_FOR_REPAIR_MACHINE;
    }
    
    /**
     * 
     * {@inheritDoc}
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
    * {@inheritDoc}
    * @see \Inventory\Model\AbstractTransactionStrategy::doPosting()
    */
    public function doPosting($entity, $u, $isFlush = false)
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

        $fifoLayerService = new FIFOLayerService();
        $fifoLayerService->setDoctrineEM($this->contextService->getDoctrineEM());

        $n = 0;
        $total_credit = 0;
        $total_local_credit = 0;

        // Create JE     
        $je = new \Application\Entity\FinJe();
        $je->setCurrency($entity->getCurrency());
        $je->setLocalCurrency($entity->getCurrency());
        $je->setExchangeRate($entity->getExchangeRate());

        $je->setPostingDate($entity->getMovementDate());
        $je->setDocumentDate($entity->getMovementDate());
        $je->setPostingPeriod($entity->getPostingPeriod());

        $je->getDocType("JE");
        $je->setCreatedBy($u);
        $je->setCreatedOn($entity->getCreatedOn());
        $je->setSysNumber($this->contextService->getControllerPlugin()
            ->getDocNumber($je));

        $je->setSourceClass(get_class($entity));
        $je->setSourceId($entity->getId());
        $je->setSourceToken($entity->getToken());

        $this->contextService->getDoctrineEM()->persist($je);

        foreach ($rows as $r) {
            /** @var \Application\Entity\NmtInventoryTrx $r */

            if ($r->getQuantity() == 0) {
                continue;
            }

            $r->setDocStatus($entity->getDocStatus());
            $this->contextService->getDoctrineEM()->persist($r);

            // update FIFO Layer
            $cogs = $fifoLayerService->valuateTrx($r, $r->getItem(), $r->getQuantity(), $u);
            $r->setCogsLocal($cogs);
            
            // Exchanging Part.
            $item_ex = new \Application\Entity\NmtInventoryItemExchange();
            $item_ex->setItem($r->getItem());
            $item_ex->setMovementType($entity->getMovementType());
            $item_ex->setFlow(\Inventory\Model\Constants::WH_TRANSACTION_IN);
            $item_ex->setQuantity($r->getQuantity());
            $item_ex->setCreatedBy($u);
            $item_ex->setCreatedOn($r->getTrxDate());
            $item_ex->setWh($r->getWh());
            $item_ex->setTrx($r);
            $item_ex->setRemarks("Auto receipt old/defect part back into store!");
            $this->contextService->getDoctrineEM()->persist($item_ex);

            // generate JE voucher.
            // Create JE Row - DEBIT

            $je_row = new \Application\Entity\FinJeRow();
            $je_row->setJe($je);

            $je_row->setPostingKey(\Finance\Model\Constants::POSTING_KEY_DEBIT);
            $je_row->setPostingCode(\Finance\Model\Constants::POSTING_KEY_DEBIT);

            // Debit on Cost Account
            $criteria = array(
                'id' => 6
            );
            $gl_account = $this->contextService->getDoctrineEM()
                ->getRepository('Application\Entity\FinAccount')
                ->findOneBy($criteria);
            $je_row->setGlAccount($gl_account);

            $je_row->setDocAmount($cogs);
            $je_row->setLocalAmount($cogs);

            $total_credit = $total_credit + $cogs;
            $total_local_credit = $total_credit;

            $je_row->setSysNumber($je->getSysNumber() . "-" . $n);

            $je_row->setCreatedBy($u);
            $je_row->setCreatedOn($entity->getCreatedOn());

            $this->contextService->getDoctrineEM()->persist($je_row);
        }

        if ($total_credit > 0) {

            // Create JE Row - Credit
            $je_row = new \Application\Entity\FinJeRow();

            $je_row->setJe($je);

            /**
             *
             * @todo: Using Control G/L account of Vendor
             */

            // credit on inventory
            $criteria = array(
                'id' => 3
            );
            $gl_account = $this->contextService->getDoctrineEM()
                ->getRepository('Application\Entity\FinAccount')
                ->findOneBy($criteria);
            $je_row->setGlAccount($gl_account);
            $je_row->setPostingKey(\Finance\Model\Constants::POSTING_KEY_CREDIT);
            $je_row->setPostingCode(\Finance\Model\Constants::POSTING_KEY_CREDIT);

            $je_row->setDocAmount($total_credit);
            $je_row->setLocalAmount($total_local_credit);

            $je_row->setCreatedBy($u);
            $je_row->setCreatedOn($entity->getCreatedOn());

            $n = $n + 1;
            $je_row->setSysNumber($je->getSysNumber() . "-" . $n);
            $this->contextService->getDoctrineEM()->persist($je_row);
        }
        
        if($entity->getSysNumber()==\Application\Model\Constants::SYS_NUMBER_UNASSIGNED){
            $entity->setSysNumber($this->contextService->getControllerPlugin()->getDocNumber($entity));
        }

        $this->contextService->getDoctrineEM()->flush();
    }

   /**
    * 
    * {@inheritDoc}
    * @see \Inventory\Model\AbstractTransactionStrategy::reverse()
    */
    public function reverse($entity, $u, $reversalDate,$isFlush = false)
    {}
    
    /**
     * 
     * @param string $trx
     */
    public function validateRow($trx)
    {}
    
    
    /**
     * 
     * {@inheritDoc}
     * @see \Inventory\Model\AbstractTransactionStrategy::createMovement()
     */
    public function createMovement($rows, $u, $isFlush = false, $movementDate = null, $wareHouse = null)
    {}


}