<?php
namespace Procure\Service;

use Application\Service\AbstractService;
use Zend\Math\Rand;
use Zend\Validator\Date;

/**
 * PR Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrService extends AbstractService
{

    /**
     *
     * @param \Application\Entity\NmtProcurePr $entity
     * @param array $data
     * @/param boolean $isPosting
     */
    public function validateHeader(\Application\Entity\NmtProcurePr $entity, $data, $isPosting = FALSE)
    {
        $errors = array();

        if (! $entity instanceof \Application\Entity\NmtProcurePr) {
            $errors[] = $this->controllerPlugin->translate('PR is not found!');
        }

        if ($data == null) {
            $errors[] = $this->controllerPlugin->translate('No input given');
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // ====== VALIDATED 1 ====== //

        if (isset($data['remarks'])) {
            $remarks = $data['remarks'];
            $entity->setRemarks($remarks);
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "remarks"');
        }

        // only update remark posible, when posted.
        if ($entity->getDocStatus() == \Application\Model\Constants::DOC_STATUS_POSTED) {
            return null;
        }

        if (isset($data['prNumber'])) {
            $prNumber = $data['prNumber'];
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "prNumber"');
        }

        if (isset($data['prName'])) {
            $prName = $data['prName'];
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "prName"');
        }

        if (isset($data['keywords'])) {
            $keywords = $data['keywords'];
            $entity->setKeywords($keywords);
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "$keywords"');
        }

        if (isset($data['submittedOn'])) {
            $submittedOn = $data['submittedOn'];
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "$submittedOn"');
        }

        if (isset($data['totalRowManual'])) {
            $totalRowManual = (int) $data['totalRowManual'];
            $entity->setTotalRowManual($totalRowManual);
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "$totalRowManual"');
        }

        if (isset($data['isDraft'])) {
            $isDraft = (int) $data['isDraft'];

            if ($isDraft != 1) {
                $isDraft = 0;
            }
            $entity->setIsDraft($isDraft);
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "$isDraft"');
        }

        if (isset($data['isActive'])) {
            $isActive = (int) $data['isActive'];
            if ($isActive != 1) {
                $isActive = 0;
            }
            $entity->setIsActive($isActive);
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "$isActive"');
        }

        if (isset($data['department_id'])) {
            $department_id = (int) $data['department_id'];
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "$department_id"');
        }

        if (isset($data['target_wh_id'])) {
            $target_wh_id = (int) $data['target_wh_id'];
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "$target_wh_id"');
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // ====== VALIDATED 2 ====== //

        if ($prNumber == null) {
            $errors[] = $this->controllerPlugin->translate('Please enter PR Number!');
        } else {
            $entity->setPrNumber($prName);
        }

        if ($prName == null) {
            $errors[] = $this->controllerPlugin->translate('Please enter PR Name!');
        } else {
            $entity->setPrName($prName);
        }

        $validator = new Date();

        // Empty is OK
        if ($submittedOn !== null) {
            if ($submittedOn !== "") {

                if (! $validator->isValid($submittedOn)) {
                    $errors[] = $this->controllerPlugin->translate('PR Date is not correct or empty!');
                } else {
                    $entity->setSubmittedOn(new \DateTime($submittedOn));
                }
            }
        }

        if ($department_id > 0) {
            $department = $this->doctrineEM->find('Application\Entity\NmtApplicationDepartment', $department_id);

            if ($department instanceof \Application\Entity\NmtApplicationDepartment) {
                $entity->setDepartment($department);
            }
        }

        if ($target_wh_id > 0) {
            $wh = $this->doctrineEM->find('Application\Entity\NmtInventoryWarehouse', $target_wh_id);

            $entity->setWarehouse($wh);
        }

        return $errors;
    }

    /**
     *
     * @param \Application\Entity\NmtProcurePr $entity
     * @param array $data
     *
     * @param \Application\Entity\MlaUsers $u
     * @param boolean $isNew
     * @param string $trigger
     * @throws \Exception
     */
    public function saveHeader($entity, $data, $u, $isNew = FALSE, $trigger = null)
    {
        $errors = array();

        if (! $u instanceof \Application\Entity\MlaUsers) {
            $m = $this->controllerPlugin->translate("Invalid Argument! User is not identified for this transaction.");
            $errors[] = $m;
        }

        if (! $entity instanceof \Application\Entity\NmtProcurePr) {
            $m = $this->controllerPlugin->translate("Invalid Argument. PR Object not found!");
            $errors[] = $m;
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // ====== VALIDATED 1 ====== //

        if ($isNew == FALSE) {
            $oldEntity = clone ($entity);
        }

        $ck = $this->validateHeader($entity, $data, $isNew);

        if (count($ck) > 0) {
            return $ck;
        }

        // ====== VALIDATED 2 ====== //

        Try {

            $changeOn = new \DateTime();
            $changeArray = array();

            if ($isNew == TRUE) {

                // Assign doc number
                if ($entity->getPrAutoNumber() == \Application\Model\Constants::SYS_NUMBER_UNASSIGNED or $entity->getPrAutoNumber() == null) {
                    $entity->setPrAutoNumber($this->controllerPlugin->getDocNumber($entity));
                }

                $entity->setCreatedBy($u);
                $entity->setCreatedOn($changeOn);
                $entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
            } else {
                
        /*         if ($entity->getPrAutoNumber() == \Application\Model\Constants::SYS_NUMBER_UNASSIGNED or $entity->getPrAutoNumber() == null) {
                    $entity->setPrAutoNumber($this->controllerPlugin->getDocNumber($entity));
                }
       */          

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

            // LOGGING
            if ($isNew == TRUE) {
                $m = sprintf('[OK] PR #%s created.', $entity->getId());
            } else {

                $m = sprintf('[OK] PR #%s updated.', $entity->getId());

                $this->getEventManager()->trigger('procure.change.log', $trigger, array(
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

            $this->getEventManager()->trigger('procure.activity.log', $trigger, array(
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
     * @param \Application\Entity\NmtProcurePr $target
     * @return NULL[]|string[]
     */
    public function priceMatching($entity)
    {
        $criteria = array(
            'isActive' => 1,
            'pr' => $entity
        );
        $rows = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->findBy($criteria);

        if (count($rows) == 0) {
            throw new \Exception("PR is empty. No Posting will be made!");
        }

        foreach ($rows as $r) {

            /** @var \Application\Entity\NmtProcurePrRow $r ;*/
            $item = $r->getItem();
            if ($item != null) {}
        }
    }

    /**
     *
     * @param \Application\Entity\NmtProcurePr $target
     * @param \Application\Entity\NmtProcurePrRow $entity
     * @return NULL[]|string[]
     */
    public function validateRow($target, $entity, $data, $isNew = false)
    {
        var_dump($data);
        $errors = array();

        if (! $entity instanceof \Application\Entity\NmtProcurePrRow) {
            $m = $this->controllerPlugin->translate("Invalid Argument. PR Row Object not found!");
            $errors[] = $m;
        }

        if (! $target instanceof \Application\Entity\NmtProcurePr) {
            $m = $this->controllerPlugin->translate("Invalid Argument. PR Object not found!");
            $errors[] = $m;
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // ====== VALIDATED 1 ====== //

        if (isset($data['remarks'])) {
            $remarks = $data['remarks'];
            $entity->setRemarks($remarks);
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "remarks"');
        }

        // only update remark posible, when posted.
        if ($entity->getDocStatus() == \Application\Model\Constants::DOC_STATUS_POSTED) {
            return null;
        }

        if (isset($data['edt'])) {
            $edt = $data['edt'];
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "edt"');
        }

        if (isset($data['isActive1'])) {
            $isActive = (int) $data['isActive1'];

            if ($isActive != 1) {
                $isActive = 0;
            }
            $entity->setIsActive($isActive);
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "$isActive"...');
        }

        if (isset($data['rowNumber'])) {
            $rowNumber = (int) $data['rowNumber'];
            $entity->setRowNumber($rowNumber);
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "$rowNumber"');
        }

        if (isset($data['priority'])) {
            $priority = $data['priority'];
            $entity->setPriority($priority);
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "$priority"');
        }

        if (isset($data['quantity'])) {
            $quantity = $data['quantity'];
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "$quantity"');
        }

        if (isset($data['rowCode'])) {
            $rowCode = $data['rowCode'];
            $entity->setRowCode($rowCode);
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "$rowCode"');
        }

        if (isset($data['rowName'])) {
            $rowName = $data['rowName'];
            $entity->setRowName($rowName);
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "$rowName"');
        }

        if (isset($data['rowUnit'])) {
            $rowUnit = $data['rowUnit'];
            $entity->setRowUnit($rowUnit);
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "$rowUnit"');
        }

        if (isset($data['conversionFactor'])) {
            $conversionFactor = $data['conversionFactor'];
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "$conversionFactor"');
        }

        if (isset($data['item_id'])) {
            $item_id = $data['item_id'];
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "$item_id"');
        }
        
        if (isset($data['target_wh_id'])) {
            $target_wh_id = (int) $data['target_wh_id'];
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "$target_wh_id"');
        }

        if (isset($data['project_id'])) {
            $project_id = $data['project_id'];
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "$project_id"');
        }
        
        if (count($errors) > 0) {
            return $errors;
        }
        
        // ====== VALIDATED 2 ====== //

        if ($item_id > 0) {
            $item = $this->doctrineEM->find('Application\Entity\NmtInventoryItem', $item_id);
            $entity->setItem($item);
        }

        if ($project_id > 0) {
            $project = $this->doctrineEM->find('Application\Entity\NmtPmProject', $project_id);
            if ($project != null) {
                $entity->setProject($project);
            }
        }

        if ($quantity == null) {
            $errors[] = $this->controllerPlugin->translate('Please  enter order quantity!');
        } else {

            if (! is_numeric($quantity)) {
                $errors[] = $this->controllerPlugin->translate("Quantity must be a number.");
            } else {
                if ($quantity <= 0) {
                    $errors[] = $this->controllerPlugin->translate('Quantity must be greater than 0!');
                } else {
                    $entity->setQuantity($quantity);
                    $entity->setDocQuantity($quantity);
                }
            }
        }

        if ($conversionFactor == null) {
            $conversionFactor = 1;
        } else {

            if (! is_numeric($conversionFactor)) {
                $errors[] = $this->controllerPlugin->translate('Conversion factor must be a number.');
            } else {
                if ($conversionFactor <= 0) {
                    $errors[] = $this->controllerPlugin->translate('Conversion factor must be greater than 0!');
                } else {
                    $entity->setConversionFactor($conversionFactor);
                }
            }
        }
        
        if ($target_wh_id > 0) {
            $wh = $this->doctrineEM->find('Application\Entity\NmtInventoryWarehouse', $target_wh_id);
            
            $entity->setWarehouse($wh);
        }

        $validator = new Date();

        // Empty is OK
        if ($edt !== null) {
            if ($edt !== "") {

                if (! $validator->isValid($edt)) {
                    $errors[] = $this->controllerPlugin->translate('Date is not valid!');
                } else {
                    $entity->setEdt(new \DateTime($edt));
                }
            }
        }

        return $errors;
    }

    /**
     *
     * @param \Application\Entity\NmtProcurePr $target
     * @param \Application\Entity\NmtProcurePrRow $entity
     * @param \Application\Entity\MlaUsers $u
     * @param boolean $isNew
     */
    public function saveRow($target, $entity, $data, $u, $isNew = FALSE, $trigger = null)
    {
        $errors = array();

        if (! $u instanceof \Application\Entity\MlaUsers) {
            $m = $this->controllerPlugin->translate("Invalid Argument! User is not identified for this transaction.");
            $errors[] = $m;
        }

        if (! $entity instanceof \Application\Entity\NmtProcurePrRow) {
            $m = $this->controllerPlugin->translate("Invalid Argument. PR Row Object not found!");
            $errors[] = $m;
        }

        if (! $target instanceof \Application\Entity\NmtProcurePr) {
            $m = $this->controllerPlugin->translate("Invalid Argument. PR Object not found!");
            $errors[] = $m;
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // ====== VALIDATED 1 ====== //

        if ($isNew == FALSE) {
            $oldEntity = clone ($entity);
        }

        $ck = $this->validateRow($target, $entity, $data, $isNew);

        if (count($ck) > 0) {
            return $ck;
        }

        // ====== VALIDATED 2 ====== //

        $changeOn = new \DateTime();
        $changeArray = array();

        Try {
            if ($entity->getItem() != null) {

                $inventoryCF = $entity->getItem()->getStockUomConvertFactor();
                if ($inventoryCF == null) {
                    $inventoryCF = 1;
                }

                $entity->setConvertedStandardQuantiy($entity->getConversionFactor() * $entity->getQuantity());
                $entity->setConvertedStockQuantity($entity->getQuantity() * $entity->getConversionFactor() / $inventoryCF);
            }

            if ($isNew == TRUE) {

                $entity->setCurrentState($target->getCurrentState());
                $entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
                $entity->setChecksum(md5(uniqid("pr_row_" . microtime())));
                $entity->setCreatedBy($u);
                $entity->setCreatedOn($changeOn);
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
            
            // Time to flush
            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();
            

            // LOGGING
            if ($isNew == TRUE) {
                $m = sprintf('[OK] PR Row #%s created.', $entity->getId());
            } else {

                $m = sprintf('[OK] PR Row  #%s updated.', $entity->getRowIdentifer());

                $this->getEventManager()->trigger('procure.change.log', $trigger, array(
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

            $this->getEventManager()->trigger('procure.activity.log', $trigger, array(
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
     * @param \Application\Entity\NmtProcurePo $entity
     * @param \Application\Entity\MlaUsers $u
     *
     */
    public function doPosting($entity, $u, $isFlush = false)
    {}

    /**
     *
     * @param \Application\Entity\NmtProcurePo $entity
     * @param \Application\Entity\MlaUsers $u
     *
     */
    public function updateStatus($entity, $u, $isFlush = false)
    {}

    /**
     *
     * @param \Application\Entity\NmtProcurePo $entity
     * @param \Application\Entity\MlaUsers $u
     *
     */
    public function copyToGR($entity, $u, $isFlush = false)
    {}
}
