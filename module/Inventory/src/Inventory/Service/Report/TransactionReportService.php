<?php
namespace Inventory\Service;

use Application\Service\AbstractService;
use Zend\Math\Rand;
use Zend\Validator\Date;
use Zend\Validator\EmailAddress;

/**
 * Warehouse Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class WarehouseService extends AbstractService
{

    /**
     *
     * @param \Application\Entity\NmtInventoryWarehouse $entity
     * @param array $data
     * @param boolean $isPosting
     */
    public function validateWareHouse(\Application\Entity\NmtInventoryWarehouse $entity, $data, $isNew = TRUE)
    {
        $errors = array();

        if (! $entity instanceof \Application\Entity\NmtInventoryWarehouse) {
            $errors[] = $this->controllerPlugin->translate('WH object is not found!');
        }

        if ($data == null) {
            $errors[] = $this->controllerPlugin->translate('No input given');
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // ====== OK ====== //

        // $company_id = $data['company_id'];

        if ($entity->getCompany() == null) {
            $errors[] = $this->controllerPlugin->translate('Country in not indentified');
        }

        // $is_locked = $data['isLocked'];
        // $is_default = $data['isDefault'];

        $country_id = $data['country_id'];
        $whCode = $data['whCode'];
        $whName = $data['whName'];

        // $whStatus = $data['whStatus'];
        $wh_address = $data['whAddress'];
        $whEmail = $data['whEmail'];
        $wh_contract_person = $data['whContactPerson'];
        $remarks = $data['remarks'];

        /*
         * if ($whStatus != 1) {
         * $whStatus = 0;
         * }
         */

        if ($whCode === '' or $whCode === null) {
            $errors[] = $this->controllerPlugin->translate('Please give warehouse code');
        }

        if ($whName === '' or $whName === null) {
            $errors[] = $this->controllerPlugin->translate('Please give warehouse name');
        }

        if ($whEmail != null) {
            $validator = new EmailAddress();
            if (! $validator->isValid($whEmail)) {
                $errors[] = $this->controllerPlugin->translate('Email addresse is not correct!');
            } else {
                $entity->setWhEmail($whEmail);
            }
        }

        if ($isNew == TRUE) {
            $r = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->findBy(array(
                'whCode' => $whCode
            ));

            if (count($r) >= 1) {
                $errors[] = $whCode . ' exists';
            }
        }

        $entity->setWhCode($whCode);
        $entity->setWhName($whName);
        $entity->setWhAddress($wh_address);

        $country = $this->doctrineEM->find('Application\Entity\NmtApplicationCountry', $country_id);

        if ($country == null) {
            $errors[] = $this->controllerPlugin->translate('Please give country!');
        } else {
            $entity->setWhCountry($country);
        }

        $entity->setWhContactPerson($wh_contract_person);

        // $entity->setIsDefault( $is_default);
        // $entity->setIsLocked( $is_locked);
        $entity->setRemarks($remarks);

        return $errors;
    }

    /**
     *
     * @param \Application\Entity\NmtInventoryWarehouse $entity
     * @param \Application\Entity\MlaUsers $u
     * @param boolean $isNew
     */
    public function saveWarehouse(\Application\Entity\NmtInventoryWarehouse $entity, $u, $isNew = FALSE)
    {
        if ($u == null) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        if (! $entity instanceof \Application\Entity\NmtInventoryWarehouse) {
            throw new \Exception("Invalid Argument. Warehouse Object not found!");
        }

        // validated.

        $changeOn = new \DateTime();

        if ($isNew == TRUE) {

            $entity->setSysNumber(\Application\Model\Constants::SYS_NUMBER_UNASSIGNED);
            $entity->setCreatedBy($u);
            $entity->setCreatedOn($changeOn);
            $entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
        } else {
            $entity->setRevisionNo($entity->getRevisionNo() + 1);
            $entity->setLastchangeBy($u);
            $entity->setLastchangeOn($changeOn);
        }

        $this->doctrineEM->persist($entity);
        $this->doctrineEM->flush();
    }

    /**
     *
     * @param \Application\Entity\NmtInventoryWarehouse $entity
     * @param \Application\Entity\MlaUsers $u
     */
    public function setDefaultWarehouse(\Application\Entity\NmtInventoryWarehouse $entity, $u)
    {
        if ($u == null) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        if (! $entity instanceof \Application\Entity\NmtInventoryWarehouse) {
            throw new \Exception("Invalid Argument. Warehouse Object not found!");
        } else {
            if ($entity->getCompany() !== null) {

                $sql = sprintf("Update nmt_inventory_warehouse set is_default=0 where 
                nmt_inventory_warehouse.is_default=1 and nmt_inventory_warehouse.company_id=%s ", $entity->getCompany()->getId());
                $stmt = $this->doctrineEM->getConnection()->prepare($sql);
                $stmt->execute();

                $entity->setIsDefault(1);
                $entity->getCompany()->setDefaultWarehouse($entity);
                $this->doctrineEM->persist($entity);
                $this->doctrineEM->flush();
            }
        }
        return True;
    }
}
