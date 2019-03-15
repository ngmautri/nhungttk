<?php
namespace Inventory\Service;

use Application\Service\AbstractService;
use Inventory\Model\GI\GIStrategyFactory;
use Zend\Math\Rand;
use Inventory\Model\AbstractTransactionStrategy;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GIService extends AbstractService
{

    /**
     *
     * @param \Application\Entity\NmtInventoryMv $entity
     * @return NULL[]|string[]
     */
    public function validateEntity($entity, $data)
    {

        // do validating
        $errors = array();
        if (! $entity instanceof \Application\Entity\NmtInventoryMv) {
            throw new \Exception("Invalid Argument. Transaction Object not found!");
        }

        return $errors;
    }

    /**
     *
     * @param \Application\Entity\NmtInventoryMv $entity
     * @param \Application\Entity\MlaUsers $u
     */
    public function saveEntity($entity, $u, $isNew = FALSE)
    {
        if (! $entity instanceof \Application\Entity\NmtInventoryMv) {
            throw new \Exception("Invalid Argument!Object can't not be found.");
        }

        if (! $u instanceof \Application\Entity\MlaUsers) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        // GI Strategy.
        $giStrategy = GIStrategyFactory::getGIStrategy($target->getMovementType());

        if (! $giStrategy instanceof \Inventory\Model\GI\AbstractGIStrategy) {
            throw new \Exception("Invalid Argument! No strategy found.");
        }

        // check on-hand quantity.

        $giStrategy->check($trx, $trx->getItem(), $u);

        if ($isNew == TRUE) {} else {}

        $this->getDoctrineEM()->persist($trx);
    }

    /**
     *
     * @param \Application\Entity\NmtInventoryMv $target
     * @param \Application\Entity\NmtInventoryTrx $entity
     * @return NULL[]|string[]
     */
    public function validateRow($target, $entity, $data)
    {

        // do validating
        $errors = array();

        if (! $target instanceof \Application\Entity\NmtInventoryMv) {
            throw new \Exception("Invalid Argument. Inventory Movemement Object not found!");
        }

        if (! $entity instanceof \Application\Entity\NmtInventoryTrx) {
            throw new \Exception("Invalid Argument. Transaction Object not found!");
        }

        $item_id = null;
        if (isset($data['item_id'])) {
            $item_id = $data['item_id'];
        }

        $quantity = null;
        if (isset($data['quantity'])) {
            $quantity = $data['quantity'];
        }

        $convert_factor = 1;
        if (isset($data['convert_factor'])) {
            $convert_factor = $data['convert_factor'];
        }

        $issue_for_id = null;
        if (isset($data['issue_for_id'])) {
            $issue_for_id = $data['issue_for_id'];
        }

        $isActive = 0;
        if (isset($data['isActive'])) {
            $isActive = (int) $data['isActive'];
        }

        $project_id = null;
        if (isset($data['project_id'])) {
            $project_id = (int) $data['project_id'];
        }

        $cost_center_id = null;
        if (isset($data['cost_center_id'])) {
            $cost_center_id = (int) $data['cost_center_id'];
        }

        $remarks = null;
        if (isset($data['remarks'])) {
            $remarks = $data['remarks'];
        }

        if ($isActive != 1) {
            $isActive = 0;
        }

        $entity->setIsActive($isActive);

        /**@var \Application\Entity\NmtInventoryItem $item ;*/
        $item = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->find($item_id);

        if ($item == null) {
            $errors[] = $this->controllerPlugin->translate('No Item is selected');
        } else {
            if ($item->getIsStocked() != 1) {
                $errors[] = $this->controllerPlugin->translate('Item is not kept in stock');
            } else {
                $entity->setItem($item);
            }
        }

        if ($quantity == null) {
            $errors[] = $this->controllerPlugin->translate('Please enter quantity!');
        } else {

            if (! is_numeric($quantity)) {
                $errors[] = $this->controllerPlugin->translate('Quantity must be a number!');
            } else {
                if ($quantity <= 0) {
                    $errors[] = $this->controllerPlugin->translate('Quantity must be greater than 0!');
                } else {
                    $entity->setQuantity($quantity);
                }
            }
        }

        if (! is_numeric($convert_factor)) {
            $errors[] = $this->controllerPlugin->translate('Convert factor must be a number!');
        } else {
            if ($quantity <= 0) {
                $errors[] = $this->controllerPlugin->translate('Convert factor must be greater than 0!');
            } else {
                $entity->setConversionFactor($convert_factor);
            }
        }

        if ($issue_for_id > 0) {
            $for_item = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->find($issue_for_id);
            $entity->setIssueFor($for_item);
        }

        if ($project_id > 0) {
            $project = $this->doctrineEM->getRepository('Application\Entity\NmtPmProject')->find($project_id);
            $entity->setProject($project);
        }

        if ($cost_center_id > 0) {
            $costCenter = $this->doctrineEM->getRepository('Application\Entity\FinCostCenter')->find($cost_center_id);
            $entity->setCostCenter($costCenter);
        }else{
            $entity->setCostCenter(null);            
        }

        $entity->setRemarks($remarks);

        // GI Strategy.
        $check_result = null;

        $giStrategy = GIStrategyFactory::getGIStrategy($target->getMovementType());
        if (! $giStrategy instanceof \Inventory\Model\GI\AbstractGIStrategy) {
            $errors[] = $this->controllerPlugin->translate("Invalid Argument! No strategy found.");
        } else {
            $giStrategy->setContextService($this);
            $check_result = $giStrategy->validateRow($entity);
        }

        if (count($check_result) > 0) {
            $errors = array_merge($errors, $check_result);
        }

        // check on-hand quantity.

        return $errors;
    }

    /**
     *
     * @param \Application\Entity\NmtInventoryMv $target
     * @param \Application\Entity\NmtInventoryTrx $trx
     * @param \Application\Entity\MlaUsers $u
     */
    public function saveRow($target, $trx, $u, $isNew = FALSE)
    {
        if (! $target instanceof \Application\Entity\NmtInventoryMv) {
            throw new \Exception("Invalid Argument! Inventory Moverment Object can't not be found.");
        }

        if (! $trx instanceof \Application\Entity\NmtInventoryTrx) {
            throw new \Exception("Invalid Argument!Object can't not be found.");
        }

        if (! $u instanceof \Application\Entity\MlaUsers) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        // / validated.

        if ($isNew == TRUE) {
            $trx->setTrxDate($target->getMovementDate());
            $trx->setDocCurrency($target->getCurrency());
            $trx->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
            $trx->setCreatedBy($u);
            $trx->setCreatedOn(new \DateTime());
        } else {
            $trx->setDocCurrency($target->getCurrency());
            $trx->setChangeOn($u);
            $trx->setChangeOn(new \DateTime());
        }

        $this->getDoctrineEM()->persist($trx);
        
    }

    /**
     *
     * @param \Application\Entity\NmtInventoryMv $target
     * @param \Application\Entity\NmtInventoryTrx $trx
     * @param \Application\Entity\MlaUsers $u
     */
    public function addRow($target, $trx, $u)
    {
        if (! $target instanceof \Application\Entity\NmtInventoryMv) {
            throw new \Exception("Invalid Argument! Inventory Moverment Object can't not be found.");
        }

        if (! $trx instanceof \Application\Entity\NmtInventoryTrx) {
            throw new \Exception("Invalid Argument!Object can't not be found.");
        }

        if (! $u instanceof \Application\Entity\MlaUsers) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        // GI Strategy.
        $giStrategy = GIStrategyFactory::getGIStrategy($target->getMovementType());

        if (! $giStrategy instanceof \Inventory\Model\GI\AbstractGIStrategy) {
            throw new \Exception("Invalid Argument! No strategy found.");
        }

        // check on-hand quantity.

        $giStrategy->check($trx, $trx->getItem(), $u);

        $trx->setTrxDate($target->getMovementDate());
        $trx->setDocCurrency($target->getCurrency());
        $trx->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
        $trx->setCreatedBy($u);
        $trx->setCreatedOn(new \DateTime());

        $this->getDoctrineEM()->persist($trx);
    }

    /**
     *
     * @param \Application\Entity\NmtInventoryMv $target
     * @param \Application\Entity\NmtInventoryTrx $trx
     * @param \Application\Entity\MlaUsers $u
     */
    public function editRow($target, $trx, $u)
    {
        if (! $target instanceof \Application\Entity\NmtInventoryMv) {
            throw new \Exception("Invalid Argument! Inventory Moverment Object can't not be found.");
        }

        if (! $trx instanceof \Application\Entity\NmtInventoryTrx) {
            throw new \Exception("Invalid Argument!Object can't not be found.");
        }

        if (! $u instanceof \Application\Entity\MlaUsers) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        // GI Strategy.
        $giStrategy = GIStrategyFactory::getGIStrategy($target->getMovementType());

        if (! $giStrategy instanceof \Inventory\Model\GI\AbstractGIStrategy) {
            throw new \Exception("Invalid Argument! No strategy found.");
        }

        // check on-hand quantity.

        $giStrategy->check($trx, $trx->getItem(), $u);

        $trx->setTrxDate($target->getMovementDate());
        $trx->setDocCurrency($target->getCurrency());
        $trx->setLastChangeBy($u);
        $trx->setLastChangeOn(new \DateTime());

        $this->getDoctrineEM()->persist($trx);
    }

    /**
     *
     * @param \Application\Entity\NmtInventoryMv $entity
     * @param \Application\Entity\MlaUsers $u,
     * @param bool $isFlush,
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function post($entity, $u, $isFlush = false)
    {
        if (! $entity instanceof \Application\Entity\NmtInventoryMv) {
            throw new \Exception("Invalid Argument! Inventory Moverment Object can't not be found.");
        }

        if (! $u instanceof \Application\Entity\MlaUsers) {
            throw new \Exception("User can't not be identified for this transaction");
        }

        $postingStrategy = GIStrategyFactory::getGIStrategy($entity->getMovementType());

        if (! $postingStrategy instanceof AbstractTransactionStrategy) {
            throw new \Exception("Posting Strategy can't not be identified for this inventory movement type!");
        }

        // do posting now
        $postingStrategy->setContextService($this);
        $postingStrategy->doPosting($entity, $u);
    }
}
