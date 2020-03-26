<?php
namespace Inventory\Model\GI;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GIforCostCenter extends \Inventory\Model\AbstractTransactionStrategy
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Model\AbstractTransactionStrategy::validateRow()
     */
    public function validateRow($entity, $data, $u, $isNew, $isPosting)
    {
        $errors = array();

        // addtional information.
        if (isset($data['cost_center_id'])) {
            $cost_center_id = $data['cost_center_id'];
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "$cost_center_id"');
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // ====== VALIDATED 2 ====== //
        /**@var \Application\Entity\FinCostCenter $cc ;*/
        $cc = $this->getContextService()
            ->getDoctrineEM()
            ->getRepository('Application\Entity\FinCostCenter')
            ->find($cost_center_id);

        if ($cc == null) {
            $errors[] = $this->getContextService()
                ->getControllerPlugin()
                ->translate('Cost Center is required');
        } else {
            $entity->setCostCenter($cc);
        }
        return $errors;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Model\InventoryTransactionInterface::getFlow()
     */
    public function getFlow()
    {
        return \Inventory\Model\Constants::WH_TRANSACTION_OUT;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Model\InventoryTransactionInterface::getTransactionIdentifer()
     */
    public function getTransactionIdentifer()
    {
        return \Inventory\Model\Constants::INVENTORY_GI_FOR_COST_CENTER;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Model\AbstractTransactionStrategy::doPosting()
     */
    public function doPosting($entity, $u, $isFlush = false)
    {
        // no need to do any thing.
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Model\AbstractTransactionStrategy::createMovement()
     */
    public function createMovement($rows, $u, $isFlush = false, $movementDate = null, $wareHouse = null, $trigger = null)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Model\AbstractTransactionStrategy::check()
     */
    public function check($trx, $item, $u)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Model\AbstractTransactionStrategy::reverse()
     */
    public function reverse($entity, $u, $reversalDate, $isFlush = false)
    {}
}