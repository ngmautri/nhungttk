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
    public function validateHeader(\Application\Entity\NmtProcurePr $entity, $data, $isPosting = FALSE, $isPosted = FALSE)
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
        
        $remarks = $data['remarks'];
        
        // only remarks changeble, when posted.
        if ($isPosted == TRUE) {
            $entity->setRemarks($remarks);
            return null;
        }
        
        $contractDate = $data['contractDate'];
        $contractNo = $data['contractNo'];
        $vendor_id = (int) $data['vendor_id'];
        $isActive = (int) $data['isActive'];
        $paymentTerm = $data['paymentTerm'];
        
        
        if ($isActive !== 1) {
            $isActive = 0;
        }
        
        $entity->setIsActive($isActive);
        
        $vendor = null;
        if ($vendor_id > 0) {
            /** @var \Application\Entity\NmtBpVendor $vendor ; */
            $vendor = $this->doctrineEM->getRepository('Application\Entity\NmtBpVendor')->find($vendor_id);
        }
        
        if ($vendor !== null) {
            $entity->setVendor($vendor);
            $entity->setVendorName($vendor->getVendorName());
        } else {
            $errors[] = 'Vendor can\'t be empty. Please select a vendor!';
        }
        
        // check currency and exchange rate
        $ck = $this->checkCurrency($entity, $data, $isPosting);
        if (count($ck) > 0) {
            $errors = array_merge($errors, $ck);
        }
        
        
        
        if ($contractNo == "") {
            $errors[] = 'Contract is not correct or empty!';
        } else {
            $entity->setContractNo($contractNo);
        }
        
        $validator = new Date();
        
        if (! $validator->isValid($contractDate)) {
            $errors[] = 'Contract Date is not correct or empty!';
        } else {
            $entity->setContractDate(new \DateTime($contractDate));
        }
        
        // check currency and exchange rate
        $ck = $this->checkIncoterm($entity, $data, $isPosting);
        if (count($ck) > 0) {
            $errors = array_merge($errors, $ck);
        }
        
        
        if ($paymentTerm == null) {
            $errors[] = $this->controllerPlugin->translate('Please given payment term');
        } else {
            $entity->setPaymentTerm($paymentTerm);
        }
        
        $entity->setRemarks($remarks);
        
        return $errors;
    }
    
    
    /**
     *
     * @param \Application\Entity\NmtProcurePr $entity
     * @param \Application\Entity\MlaUsers $u
     * @param boolean $isNew
     */
    public function saveHeader($entity, $u, $isNew = FALSE)
    {
        if ($u == null) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }
        
        if (! $entity instanceof \Application\Entity\NmtProcurePr) {
            throw new \Exception("Invalid Argument. PR Object not found!");
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
        
        foreach($rows as $r){
            
            /** @var \Application\Entity\NmtProcurePrRow $r ;*/
            $item = $r->getItem();
            if($item !=null){
                
            }
        }
        
    
    }

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
