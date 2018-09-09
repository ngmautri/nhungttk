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
     * @param \Application\Entity\NmtProcurePr $target
     * @param \Application\Entity\NmtProcurePrRow $entity
     * @return NULL[]|string[]
     */
    public function validateRow($target, $entity, $data)
    {

        // do validating
        $errors = array();

        if (! $target instanceof \Application\Entity\NmtProcurePr) {
            throw new \Exception("Invalid Argument. PR Object not found!");
        }

        if (! $entity instanceof \Application\Entity\NmtProcurePrRow) {
            throw new \Exception("Invalid Argument. PR Line Object not found!");
        }

        $edt = $data['edt'];
        $isActive = (int) $data['isActive'];
        $rowNumber = (int) $data['rowNumber'];
        $priority = $data['priority'];
        $quantity = $data['quantity'];
        $rowCode = $data['rowCode'];
        $rowName = $data['rowName'];
        $rowUnit = $data['rowUnit'];
        $remarks = $data['remarks'];
        
        // converstion factor to standard unit.
        $conversionFactor = $data['conversionFactor'];

        $item_id = $data['item_id'];
        $project_id = $data['project_id'];

        if ($isActive != 1) {
            $isActive = 0;
        }

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

        $entity->setRowNumber($rowNumber);
        $entity->setIsActive($isActive);
        $entity->setPriority($priority);
        $entity->setRemarks($remarks);
        $entity->setRowCode($rowCode);
        $entity->setRowName($rowName);
        $entity->setRowUnit($rowUnit);
        $entity->setConversionFactor($conversionFactor);

    
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
                    $errors[] =  $this->controllerPlugin->translate('Conversion factor must be greater than 0!');
                } else {
                    $entity->setConversionFactor($conversionFactor);
                }
            }
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
    public function saveRow($target, $entity, $u, $isNew = FALSE)
    {
        if ($u == null) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        if (! $target instanceof \Application\Entity\NmtProcurePr) {
            throw new \Exception("Invalid Argument. PR Object not found!");
        }

        if (! $entity instanceof \Application\Entity\NmtProcurePrRow) {
            throw new \Exception("Invalid Argument. PR Line Object not found!");
        }

        // validated.

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
            $entity->setCreatedOn(new \DateTime());
        } else {
            $entity->setRevisionNo($entity->getRevisionNo() + 1);
            $entity->setLastchangeBy($u);
            $entity->setLastchangeOn(new \DateTime());
        }

        $this->doctrineEM->persist($entity);
        $this->doctrineEM->flush();
    }

    /**
     *
     * @param \Application\Entity\NmtProcurePo $entity
     * @param \Application\Entity\MlaUsers $u
     *
     */
    public function doPosting($entity, $u, $isFlush = false)
    {
        if ($u == null) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        if (! $entity instanceof \Application\Entity\NmtProcurePo) {
            throw new \Exception("Invalid Argument. PO Object not found!");
        }

        $criteria = array(
            'isActive' => 1,
            'po' => $entity
        );
        $po_rows = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePoRow')->findBy($criteria);

        if (count($po_rows) == 0) {
            throw new \Exception("PO is empty. No Posting will be made!");
        }

        // OK to post
        // ++++++++++++++++++++++++++++

        /**
         *
         * @todo Update Entitiy!
         */

        $changeOn = new \DateTime();

        // Assign doc number
        if ($entity->getSysNumber() == \Application\Model\Constants::SYS_NUMBER_UNASSIGNED) {
            $entity->setSysNumber($this->controllerPlugin->getDocNumber($entity));
        }

        // set posted
        $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_POSTED);
        $entity->setRevisionNo($entity->getRevisionNo() + 1);
        $entity->setLastchangeBy($u);
        $entity->setLastchangeOn($changeOn);
        $this->doctrineEM->persist($entity);

        $n = 0;
        foreach ($po_rows as $r) {

            /** @var \Application\Entity\NmtProcurePoRow $r ; */

            /**
             * Double check only.
             * Receipt of ZERO quantity not allowed
             */
            if ($r->getQuantity() == 0) {
                continute;
            }

            $n ++;

            // UPDATE row status
            $r->setIsPosted(1);
            $r->setIsDraft(0);
            $r->setDocStatus($entity->getDocStatus());
            $r->setRowIdentifer($entity->getSysNumber() . '-' . $n);
            $r->setRowNumber($n);
            $r->setLastchangeOn($changeOn);
        }

        if ($isFlush == true) {
            $this->doctrineEM->flush();
        }
    }

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
