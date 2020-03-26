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
    public function validateEntity(\Application\Entity\NmtInventoryWarehouse $entity, $data, $isNew = TRUE)
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

        // ====== VALIDATED 1 ====== //

        if (isset($data['whCode'])) {
            $whCode = $data['whCode'];
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "whCode"');
        }

        if (isset($data['whName'])) {
            $whName = $data['whName'];
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "whName"');
        }

        if (isset($data['whAddress'])) {
            $whAddress = $data['whAddress'];
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "whAddress"');
        }

        if (isset($data['country_id'])) {
            $country_id = $data['country_id'];
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "country_id"');
        }

        if (isset($data['wh_controller_id'])) {
            $wh_controller_id = $data['wh_controller_id'];
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "wh_controller_id"');
        }

        if (isset($data['whEmail'])) {
            $whEmail = $data['whEmail'];
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "whEmail"');
        }

        if (isset($data['whContactPerson'])) {
            $wh_contract_person = $data['whContactPerson'];
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "whContactPerson"');
        }

        if (isset($data['remarks'])) {
            $remarks = $data['remarks'];
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "remarks"');
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // ====== VALIDATED 2 ====== //

        if ($entity->getCompany() == null) {
            $errors[] = $this->controllerPlugin->translate('Company in not indentified');
        }

        if ($whCode === '' or $whCode === null) {
            $errors[] = $this->controllerPlugin->translate('Please give warehouse code');
        } else {
            if ($isNew == TRUE) {
                $r = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->findBy(array(
                    'whCode' => $whCode
                ));

                if (count($r) >= 1) {
                    $errors[] = $whCode . ' exists';
                } else {
                    $entity->setWhCode($whCode);
                }
            }
        }

        if ($wh_controller_id > 0) {
            $wh_controller = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->find($wh_controller_id);
            $entity->setWhController($wh_controller);
        }

        if ($whName === '' or $whName === null) {
            $errors[] = $this->controllerPlugin->translate('Please give warehouse name');
        } else {
            $entity->setWhName($whName);
        }

        $country = $this->doctrineEM->find('Application\Entity\NmtApplicationCountry', $country_id);
        if ($country == null) {
            $errors[] = $this->controllerPlugin->translate('Please give country!');
        } else {
            $entity->setWhCountry($country);
        }

        if ($whEmail != null) {
            $validator = new EmailAddress();
            if (! $validator->isValid($whEmail)) {
                $errors[] = $this->controllerPlugin->translate('Email addresse is not correct!');
            } else {
                $entity->setWhEmail($whEmail);
            }
        }

        $entity->setWhAddress($whAddress);

        $entity->setWhContactPerson($wh_contract_person);

        $entity->setRemarks($remarks);

        return $errors;
    }

    /**
     *
     * @param \Application\Entity\NmtInventoryWarehouse $entity
     * @param \Application\Entity\MlaUsers $u
     * @param boolean $isNew
     */
    public function saveEntity(\Application\Entity\NmtInventoryWarehouse $entity, $data, $u, $isNew = FALSE)
    {
        $errors = array();

        if (! $u instanceof \Application\Entity\MlaUsers) {
            $errors[] = $this->controllerPlugin->translate("Invalid Argument! User is not indentided for this transaction");
        }

        if (! $entity instanceof \Application\Entity\NmtInventoryWarehouse) {
            $errors[] = $this->controllerPlugin->translate("Invalid Argument. Warehouse object is not found!");
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // ====== VALIDATED 1 ====== //

        if ($isNew == FALSE) {
            $oldEntity = clone ($entity);
        }

        $ck = $this->validateEntity($entity, $data, $isNew);

        if (count($ck) > 0) {
            return $ck;
        }

        // ====== VALIDATED 2 ====== //

        Try {

            $changeOn = new \DateTime();
            $changeArray = array();

            if ($isNew == TRUE) {

                $entity->setSysNumber(\Application\Model\Constants::SYS_NUMBER_UNASSIGNED);
                $entity->setCreatedBy($u);
                $entity->setCreatedOn($changeOn);
                $entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
            } else {

                $changeArray = $this->controllerPlugin->objectsAreIdentical($oldEntity, $entity);
                if (count($changeArray) == 0) {
                    $errors[] = sprintf('Nothing changed.');
                    return $errors;
                }

                $entity->setRevisionNo($entity->getRevisionNo() + 1);
                $entity->setLastchangeBy($u);
                $entity->setLastchangeOn($changeOn);
            }

            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();

            // to created Defaul Location
            if ($entity->getLocation() == null) {

                $rootLocation = new \Application\Entity\NmtInventoryWarehouseLocation();
                $rootLocation->setWarehouse($entity);
                $rootLocation->setLocationCode($entity->getId() . '-ROOT-LOCATION');
                $rootLocation->setCreatedBy($u);
                $rootLocation->setCreatedOn($changeOn);
                $rootLocation->setIsActive(1);
                $rootLocation->setIsRootLocation(1);
                $rootLocation->setIsSystemLocation(1);
                $rootLocation->setLocationName($entity->getId() . '-ROOT-LOCATION');
                $rootLocation->setToken(Rand::getString(15, \Application\Model\Constants::CHAR_LIST, true));

                $this->doctrineEM->persist($rootLocation);
                $this->doctrineEM->flush();

                $rootLocation->setPath($rootLocation->getId() . '/');
                $rootLocation->setPathDepth(1);
                $this->doctrineEM->persist($rootLocation);
                $this->doctrineEM->flush();

                $entity->setLocation($rootLocation);

                $returnLocation = new \Application\Entity\NmtInventoryWarehouseLocation();
                $returnLocation->setWarehouse($entity);
                $returnLocation->setLocationCode($entity->getId() . '-RETURN-LOCATION');
                $returnLocation->setCreatedBy($u);
                $returnLocation->setCreatedOn($changeOn);
                $returnLocation->setIsActive(1);
                $returnLocation->setIsRootLocation(0);
                $returnLocation->setIsSystemLocation(0);
                $returnLocation->setIsReturnLocation(1);
                $returnLocation->setLocationName($entity->getId() . '-RETURN-LOCATION');
                $returnLocation->setParentId($rootLocation->getId()); // important

                $returnLocation->setToken(Rand::getString(15, \Application\Model\Constants::CHAR_LIST, true));
                $this->doctrineEM->persist($returnLocation);
                $this->doctrineEM->flush();

                $returnLocation->setPath($rootLocation->getPath() . $returnLocation->getId() . '/');
                $returnLocation->setPathDepth($rootLocation->getPathDepth() + 1);
                $this->doctrineEM->persist($returnLocation);

                $scrapLocation = new \Application\Entity\NmtInventoryWarehouseLocation();
                $scrapLocation->setWarehouse($entity);
                $scrapLocation->setLocationCode($entity->getId() . '-SCRAP-LOCATION');
                $scrapLocation->setCreatedBy($u);
                $scrapLocation->setCreatedOn($changeOn);
                $scrapLocation->setIsActive(1);
                $scrapLocation->setIsRootLocation(0);
                $scrapLocation->setIsSystemLocation(0);
                $scrapLocation->setIsReturnLocation(0);
                $scrapLocation->setIsScrapLocation(1);
                $scrapLocation->setLocationName($entity->getId() . '-SCRAP-LOCATION');
                $scrapLocation->setParentId($rootLocation->getId()); // important

                $scrapLocation->setToken(Rand::getString(15, \Application\Model\Constants::CHAR_LIST, true));

                $this->doctrineEM->persist($scrapLocation);
                $this->doctrineEM->flush();

                $scrapLocation->setPath($rootLocation->getPath() . $scrapLocation->getId() . '/');
                $scrapLocation->setPathDepth($rootLocation->getPathDepth() + 1);
                $this->doctrineEM->persist($scrapLocation);
                $this->doctrineEM->flush();
            }

            // LOGGING
            if ($isNew == TRUE) {
                $m = sprintf('[OK] Warehouse #%s created.', $entity->getId());
            } else {

                $m = sprintf('[OK] Warehouse #%s updated.', $entity->getId());

                $this->getEventManager()->trigger('inventory.change.log', __METHOD__, array(
                    'priority' => 7,
                    'message' => $m,
                    'objectId' => $entity->getId(),
                    'objectToken' => $entity->getToken(),
                    'changeArray' => $changeArray,
                    'changeBy' => $u,
                    'changeOn' => $changeOn,
                    'revisionNumber' => $entity->getRevisionNo(),
                    'changeDate' => $changeOn,
                    'changeValidFrom' => $changeOn
                ));
            }

            $this->getEventManager()->trigger('inventory.activity.log', __METHOD__, array(
                'priority' => \Zend\Log\Logger::INFO,
                'message' => $m,
                'createdBy' => $u,
                'createdOn' => $changeOn
            ));
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }
        return $errors;
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
