<?php
namespace Inventory\Service;

use Application\Service\AbstractService;
use Inventory\Model\GI\GIStrategyFactory;
use Inventory\Model\GI\AbstractGIStrategy;
use Zend\Math\Rand;
use Zend\Validator\Date;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class InventoryTransactionService extends AbstractService
{

    /**
     *
     * @param \Application\Entity\NmtInventoryMv $entity
     * @param array $data
     * @param boolean $isNew
     * @param $isPosting $isNew
     *      
     * @return array
     */
    public function validateHeader(\Application\Entity\NmtInventoryMv $entity, $data, $isNew = TRUE, $isPosting=false)
    {
        $errors = array();

        if (! $entity instanceof \Application\Entity\NmtInventoryMv) {
            $errors[] = $this->controllerPlugin->translate('Inventory Transaction object is not found!');
        }

        if ($data == null) {
            $errors[] = $this->controllerPlugin->translate('No input given');
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // ====== Validated 1 ====== //

        if (isset($data['movementType'])) {
            $movementType = $data['movementType'];
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given movementType');
        }

        if (isset($data['movementDate'])) {
            $movementDate = $data['movementDate'];
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given movementDate');
        }

        if (isset($data['source_wh_id'])) {
            $source_wh_id = $data['source_wh_id'];
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given source_wh_id');
        }

        if (isset($data['isActive'])) {
            $isActive = $data['isActive'];
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given isActive');
        }

        if (isset($data['remarks'])) {
            $remarks = $data['remarks'];
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given remarks');
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // ====== Validated 2 ====== //

        if ($isActive != 1) {
            $isActive = 0;
        }

        $entity->setIsActive($isActive);

        if ($movementType == null) {
            $errors[] = $this->controllerPlugin->translate('Inventory movement is not selected!');
        } else {
            
            // validate movement Type.
            $movementStrategy = \Inventory\Model\InventoryTransactionStrategyFactory::getMovementStrategy($movementType);

            if (! $movementStrategy instanceof \Inventory\Model\AbstractTransactionStrategy) {
                $errors[] = $this->controllerPlugin->translate('Inventory movement strategy is not implemented yet!');
            } else {

                if ($movementStrategy->getFlow() == null) {
                    $errors[] = $this->controllerPlugin->translate('Inventory movement strategy is not implemented correctly!');
                } else {
                    $entity->setMovementType($movementType);
                    $entity->setMovementFlow($movementStrategy->getFlow());
                }
            }
        }

        $validator = new Date();
        if (! $validator->isValid($movementDate)) {
            $errors[] = $this->controllerPlugin->translate('Goods Issue Date is not correct or empty!');
        } else {
            $entity->setMovementDate(new \DateTime($movementDate));
        }

        $warehouse = null;
        if ($source_wh_id > 0) {
            $warehouse = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->find($source_wh_id);
        }

        if ($warehouse instanceof \Application\Entity\NmtInventoryWarehouse) {
            $entity->setWarehouse($warehouse);
        } else {
            $errors[] = $this->controllerPlugin->translate('Warehouse is not selected');
        }

        $entity->setRemarks($remarks);

        return $errors;
    }

    /**
     *
     * @param \Application\Entity\NmtInventoryMv $entity
     * @param array $data
     * @param \Application\Entity\MlaUsers $u,
     * @param boolean $isNew
     */
    public function saveHeader($entity, $data, $u, $isNew = FALSE)
    {
        $errors = array();

        if (! $u instanceof \Application\Entity\MlaUsers) {
            $errors[] = $this->controllerPlugin->translate("Invalid Argument! User is not indentided for this transaction");
        }

        if (! $entity instanceof \Application\Entity\NmtInventoryMv) {
            $errors[] = $this->controllerPlugin->translate("Invalid Argument. Inventory Transaction object is not found!");
        } else {
            if ($entity->getLocalCurrency() == null) {
                $errors[] = $this->controllerPlugin->translate("Invalid Argument. Local currency is not defined!");
            }
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // ====== VALIDATED 1====== //

        $ck = $this->validateHeader($entity, $data, $isNew);

        if (count($ck) > 0) {
            return $ck;
        }

        // ====== VALIDATED 2 ====== //
        Try {

            $changeOn = new \DateTime();

            if ($isNew == TRUE) {

                $entity->setSysNumber(\Application\Model\Constants::SYS_NUMBER_UNASSIGNED);
                $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_DRAFT);
                
                $entity->setCreatedBy($u);
                $entity->setCreatedOn($changeOn);
                $entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
            } else {
                
                $entity->setRevisionNo($entity->getRevisionNo() + 1);
                //$entity->setLastchangeBy($u);
                $entity->setLastchangeOn($changeOn);
            }
           $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();
            
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }
        return $errors;
    }

  
}
