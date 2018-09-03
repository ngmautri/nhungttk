<?php
namespace Procure\Service;

use Application\Service\AbstractService;
use Zend\Math\Rand;

/**
 * PO Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoService extends AbstractService
{

    /**
     *
     * @param \Application\Entity\NmtProcurePoRow $entity
     * @return NULL[]|string[]
     */
    protected function validateRow($entity)
    {

        // do validating
        $errors = array();

        if (! $entity instanceof \Application\Entity\NmtProcurePoRow) {
            $errors[] = $this->controllerPlugin->translate('PO row is empty.');
        }

        if ($entity->getItem() == null) {
            $errors[] = $this->controllerPlugin->translate('Item can\'t be empty!');
        }

        if (! is_numeric($entity->getDocQuantity())) {
            $errors[] = $this->controllerPlugin->translate('Quantity must be a number.');
        } else {
            if ($entity->getDocQuantity() <= 0) {
                $errors[] = $this->controllerPlugin->translate('Quantity must be > 0!');
            }
        }

        if (! is_numeric($entity->getDocUnitPrice())) {
            $errors[] = $this->controllerPlugin->translate('Price is not valid. It must be a number.');
        } else {
            if ($entity->getDocUnitPrice() < 0) {
                $errors[] = $this->controllerPlugin->translate('Price must be >   0!');
            }
        }

        if ($entity->getExwUnitPrice() != null) {
            if (! is_numeric($entity->getExwUnitPrice())) {
                $errors[] = $this->controllerPlugin->translate('Exw Price is not valid. It must be a number.');
            } else {
                if ($entity->getExwUnitPrice() <= 0) {
                    $errors[] = $this->controllerPlugin->translate('Exw Price must be >=0!');
                }
            }
        }

        if (! is_numeric($entity->getTaxRate())) {
            $errors[] = $this->controllerPlugin->translate('TaxRate is not valid. It must be a number.');
        } else {
            if ($entity->getTaxRate() < 0) {
                $errors[] = $this->controllerPlugin->translate('TaxRate must be >0');
            }
        }

        if ($entity->getConversionFactor() == null) {
            $entity->setConversionFactor(1);
        }

        if (! is_numeric($entity->getConversionFactor())) {
            $errors[] = $this->controllerPlugin->translate('conversion factor must be a number.');
        } else {
            if ($entity->getConversionFactor() <= 0) {
                $errors[] = $this->controllerPlugin->translate('converstion factor must be greater than 0!');
            }
        }

        /*
         * $errors = $this->validateRow($entity);
         * if (count($errors) > 0) {
         *
         * $m = '';
         * foreach ($errors as $e) {
         * $m.= $e . '<br>';
         * }
         *
         * $m1 = sprintf('[ERROR] %s', $m);
         * $this->getEventManager()->trigger('procure.activity.log', __METHOD__, array(
         * 'priority' => \Zend\Log\Logger::ERR,
         * 'message' => $m1,
         * 'createdBy' => $u,
         * 'createdOn' => new \DateTime()
         * ));
         *
         * throw new \Exception($m);
         * }
         */

        return $errors;
    }

    /**
     *
     * @param \Application\Entity\NmtProcurePo $target
     * @param \Application\Entity\NmtProcurePoRow $entity
     * @param \Application\Entity\MlaUsers $u
     * @param boolean $isNew
     */
    public function saveRow($target, $entity, $u, $isNew = FALSE)
    {
        if ($u == null) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        if (! $target instanceof \Application\Entity\NmtProcurePo) {
            throw new \Exception("Invalid Argument. PO Object not found!");
        }

        if (! $entity instanceof \Application\Entity\NmtProcurePoRow) {
            throw new \Exception("Invalid Argument. PO Line Object not found!");
        }

        // validated.

        $netAmount = $entity->getDocQuantity() * $entity->getDocUnitPrice();
        $entity->setNetAmount($netAmount);

        $taxAmount = $entity->getNetAmount() * $entity->getTaxRate() / 100;
        $grossAmount = $entity->getNetAmount() + $taxAmount;

        $entity->setTaxAmount($taxAmount);
        $entity->setGrossAmount($grossAmount);

        $convertedPurchaseQuantity = $entity->getDocQuantity();
        $convertedPurchaseUnitPrice = $entity->getDocUnitPrice();

        $conversionFactor = $entity->getConversionFactor();
        $standardCF = $entity->getConversionFactor();

        $pr_row = $entity->getPrRow();

        if ($pr_row != null) {
            $convertedPurchaseQuantity = $convertedPurchaseQuantity * $conversionFactor;
            $convertedPurchaseUnitPrice = $convertedPurchaseUnitPrice / $conversionFactor;
            $standardCF = $standardCF * $pr_row->getConversionFactor();
        }

        // quantity /unit price is converted purchase quantity to clear PR

        $entity->setQuantity($convertedPurchaseQuantity);
        $entity->setUnitPrice($convertedPurchaseUnitPrice);

        $convertedStandardQuantity = $entity->getDocQuantity();
        $convertedStandardUnitPrice = $entity->getDocUnitPrice();

        $item = $entity->getItem();
        if ($item != null) {
            $convertedStandardQuantity = $convertedStandardQuantity * $standardCF;
            $convertedStandardUnitPrice = $convertedStandardUnitPrice / $standardCF;
        }

        // calculate standard quantity
        $entity->setConvertedPurchaseQuantity($convertedPurchaseQuantity);
        $entity->setConvertedPurchaseUnitPrice($convertedPurchaseUnitPrice);

        $entity->setConvertedStandardQuantity($convertedStandardQuantity);
        $entity->setConvertedStandardUnitPrice($convertedStandardUnitPrice);

        if ($isNew == TRUE) {
            $entity->setCurrentState($target->getCurrentState());
            $entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
            $entity->setCreatedBy($u);
            $entity->setCreatedOn(new \DateTime());
        } else {
            $changeOn = new \DateTime();
            $entity->setRevisionNo($entity->getRevisionNo() + 1);
            $entity->setLastchangeBy($u);
            $entity->setLastchangeOn($changeOn);
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
