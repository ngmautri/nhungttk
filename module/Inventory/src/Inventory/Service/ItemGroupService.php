<?php
namespace Inventory\Service;

use Application\Service\AbstractService;
use Zend\Math\Rand;
use Zend\Validator\Date;
use Zend\Validator\EmailAddress;

/**
 * Item Group Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemGroupService extends AbstractService
{

    /**
     *
     * @param \Application\Entity\NmtInventoryItemGroup $entity
     * @param array $data
     * @param boolean $isPosting
     */
    public function validateEntity(\Application\Entity\NmtInventoryItemGroup $entity, $data, $isNew = TRUE)
    {
        $errors = array();

        if (! $entity instanceof \Application\Entity\NmtInventoryItemGroup) {
            $errors[] = $this->controllerPlugin->translate('Item Groupp is not found!');
        }

        if ($data == null) {
            $errors[] = $this->controllerPlugin->translate('No input given');
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // ====== VALIDATED 1 ====== //

        if (isset($data['groupName'])) {
            $groupName = $data['groupName'];
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given groupName');
        }

        if (isset($data['revenue_account_id'])) {
            $revenue_account_id = (int) $data['revenue_account_id'];
        } else {
            $errors[] = $this->controllerPlugin->translate('invalide input revenue_account_id');
        }

        if (isset($data['revenue_account_id'])) {
            $inventory_account_id = (int) $data['inventory_account_id'];
        } else {
            $errors[] = $this->controllerPlugin->translate('invalide input revenue_account_id');
        }

        if (isset($data['expense_account_id'])) {
            $expense_account_id = (int) $data['expense_account_id'];
        } else {
            $errors[] = $this->controllerPlugin->translate('invalide input expense_account_id');
        }

        if (isset($data['cogs_account_id'])) {
            $cogs_account_id = (int) $data['cogs_account_id'];
        } else {
            $errors[] = $this->controllerPlugin->translate('invalide input cogs_account_id');
        }

        if (isset($data['cogs_account_id'])) {
            $cost_center_id = (int) $data['cost_center_id'];
        } else {
            $errors[] = $this->controllerPlugin->translate('invalide input cogs_account_id');
        }

        if (isset($data['default_warehouse_id'])) {
            $default_warehouse_id = (int) $data['default_warehouse_id'];
        } else {
            $errors[] = $this->controllerPlugin->translate('invalide input default_warehouse_id');
        }

        if (isset($data['isActive'])) {
            $isActive = (int) $data['isActive'];
        } else {
            $errors[] = $this->controllerPlugin->translate('invalide input isActive');
        }

        if (isset($data['description'])) {
            $decription = $data['description'];
        } else {
            $errors[] = $this->controllerPlugin->translate('invalide input description');
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // ====== VALIDATED 1 ====== //

        if ($isActive !== 1) {
            $isActive = 0;
        }

        $entity->setIsActive($isActive);

        $revenue_account = null;
        if ($revenue_account_id > 0) {
            $revenue_account = $this->doctrineEM->getRepository('Application\Entity\FinAccount')->find($revenue_account_id);
            $entity->setRevenueAccount($revenue_account);
        }

        $inventory_account = null;
        if ($inventory_account_id > 0) {
            $inventory_account = $this->doctrineEM->getRepository('Application\Entity\FinAccount')->find($inventory_account_id);
            $entity->setInventoryAccount($inventory_account);
        }

        $expense_account = null;
        if ($expense_account_id > 0) {
            $expense_account = $this->doctrineEM->getRepository('Application\Entity\FinAccount')->find($expense_account_id);
            $entity->setExpenseAccount($expense_account);
        }

        $cogs_account = null;
        if ($cogs_account_id > 0) {
            $cogs_account = $this->doctrineEM->getRepository('Application\Entity\FinAccount')->find($cogs_account_id);
            $entity->setCogsAccount($cogs_account);
        }

        $cost_center = null;
        if ($cost_center_id > 0) {
            $cost_center = $this->doctrineEM->getRepository('Application\Entity\FinCostCenter')->find($cost_center_id);
            $entity->setCostCenter($cost_center);
        }

        $default_warehouse = null;
        if ($default_warehouse_id > 0) {
            $default_warehouse = $this->doctrineEM->getRepository('\Application\Entity\NmtInventoryWarehouse')->find($default_warehouse_id);
            $entity->setDefaultWarehouse($default_warehouse);
        }

        $entity->setDescription($decription);

        if ($groupName == "") {
            $errors[] = $this->controllerPlugin->translate('Please give group name!');
        } else {

            if ($isNew == TRUE) {
                $criteria = array(
                    'groupName' => $groupName,
                    'isActive' => 1
                );

                /** @var \Application\Entity\NmtInventoryItemGroup $entity_ck ; */
                $entity_ck = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemGroup')->findOneBy($criteria);
                if ($entity_ck == null) {
                    $entity->setGroupName($groupName);
                } else {
                    $errors[] = $this->controllerPlugin->translate('Record exists already!');
                }
            }
        }
        return $errors;
    }

    /**
     *
     * @param \Application\Entity\NmtInventoryItemGroup $entity
     * @param array $data
     * @param \Application\Entity\MlaUsers $u
     * @param boolean $isNew
     */
    public function saveEntity(\Application\Entity\NmtInventoryItemGroup $entity, $data, $u, $isNew = FALSE)
    {
        $errors = array();

        if (! $u instanceof \Application\Entity\MlaUsers) {
            $errors[] = $this->controllerPlugin->translate("Invalid Argument! User is not indentided for this transaction");
        }

        if (! $entity instanceof \Application\Entity\NmtInventoryItemGroup) {
            $errors[] = $this->controllerPlugin->translate("Invalid Argument. Item Group object is not found!");
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // ====== VALIDATED ====== //

        $ck = $this->validateEntity($entity, $data, $isNew);

        if (count($ck) > 0) {
            return $ck;
        }

        // ====== VALIDATED ====== //

        Try {

            $changeOn = new \DateTime();

            if ($isNew == TRUE) {

                // $entity->setSysNumber(\Application\Model\Constants::SYS_NUMBER_UNASSIGNED);
                $entity->setCreatedBy($u);
                $entity->setCreatedOn($changeOn);
                $entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
            } else {
                // $entity->setRevisionNo($entity->getRevisionNo() + 1);
                // $entity->setLastchangeBy($u);
                // $entity->setLastchangeOn($changeOn);
            }

            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }
        return $errors;
    }
}
