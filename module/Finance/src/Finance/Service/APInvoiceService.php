<?php
namespace Finance\Service;

use Doctrine\ORM\EntityManager;
use ZendSearch\Lucene\Lucene;
use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Document\Field;
use ZendSearch\Lucene\Analysis\Analyzer\Common\TextNum\CaseInsensitive;
// use ZendSearch\Lucene\Analysis\Analyzer\Common\Utf8Num\CaseInsensitive; // Not worked
use ZendSearch\Lucene\Analysis\Analyzer\Analyzer;
use ZendSearch\Lucene\Index\Term;
use ZendSearch\Lucene\Search\Query\MultiTerm;
use ZendSearch\Lucene\Search\Query\Wildcard;
use Application\Entity\NmtInventoryItem;
use ZendSearch\Lucene\Search\Query\Boolean;
use ZendSearch\Lucene\Search\QueryParser;
use Exception;
use Application\Entity\NmtProcurePrRow;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Application\Entity\NmtInventoryTrx;
use Zend\Math\Rand;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class APInvoiceService implements EventManagerAwareInterface
{

    protected $doctrineEM;

    protected $eventManager = null;

    /**
     *
     * @param \Application\Entity\FinVendorInvoice $entity
     * @param \Application\Entity\MlaUsers $u
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function post($entity, $u)
    {
        if (! $entity instanceof \Application\Entity\FinVendorInvoice) {
            return;
        }
        
        // Posting
        $oldEntity = clone ($entity);
        
        $changeOn = new \DateTime();
        $oldEntity = clone ($entity);
        
        $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_POSTED);
        $entity->setTransactionType(\Application\Model\Constants::TRANSACTION_TYPE_PURCHASED);
        $entity->setRevisionNo($entity->getRevisionNo() + 1);
        $entity->setLastchangeBy($u);
        $entity->setLastchangeOn($changeOn);
        $this->doctrineEM->persist($entity);
        $this->doctrineEM->flush();
        
        // UPDATE AP ROW, CREATE GR & CREATE STOCK GR
        // total rows checked.
        
        $n = 0;
        foreach ($ap_rows as $r) {
            
            /** @var \Application\Entity\FinVendorInvoiceRow $r ; */
            
            // ignore row with 0 quantity
            if ($r->getQuantity() == 0) {
                $r->setIsActive(0);
                continue;
            }
            
            $createdOn = new \DateTime();
            
            $netAmount = $r->getQuantity() * $r->getUnitPrice();
            $taxAmount = $netAmount * $r->getTaxRate() / 100;
            $grossAmount = $netAmount + $taxAmount;
            
            // UPDATE status
            $n ++;
            $r->setIsPosted(1);
            $r->setIsDraft(0);
            
            $r->setNetAmount($netAmount);
            $r->setTaxAmount($taxAmount);
            $r->setGrossAmount($grossAmount);
            
            $r->setDocStatus($entity->getDocStatus());
            $r->setTransactionType($entity->getTransactionType());
            $r->setRowIdentifer($entity->getSysNumber() . '-' . $n);
            $r->setRowNumber($n);
            
            /**
             * create procure good receipt.
             * ============================
             */
            
            $criteria = array(
                'isActive' => 1,
                'apInvoiceRow' => $r
            );
            $gr_entity_ck = $this->doctrineEM->getRepository('Application\Entity\NmtProcureGrRow')->findOneBy($criteria);
            
            if ($gr_entity_ck instanceof \Application\Entity\NmtProcureGrRow) {
                $gr_entity = $gr_entity_ck;
            } else {
                $gr_entity = new \Application\Entity\NmtProcureGrRow();
            }
            
            $gr_entity->setIsActive(1);
            $gr_entity->setInvoice($entity);
            $gr_entity->setApInvoiceRow($r);
            
            $gr_entity->setItem($r->getItem());
            $gr_entity->setPrRow($r->getPrRow());
            $gr_entity->setPoRow($r->getPoRow());
            
            $gr_entity->setTargetObject(get_class($entity));
            $gr_entity->setTargetObjectId($entity->getId());
            $gr_entity->setTransactionType($entity->getTransactionType());
            
            $gr_entity->setIsDraft($r->getIsDraft());
            $gr_entity->setIsPosted($r->getIsPosted());
            $gr_entity->setDocStatus($r->getDocStatus());
            
            $gr_entity->setQuantity($r->getQuantity());
            $gr_entity->setUnit($r->getUnit());
            $gr_entity->setUnitPrice($r->getUnitPrice());
            $gr_entity->setNetAmount($r->getNetAmount());
            $gr_entity->setTaxRate($r->getTaxRate());
            $gr_entity->setTaxAmount($r->getTaxAmount());
            $gr_entity->setGrossAmount($r->getGrossAmount());
            $gr_entity->setDiscountRate($r->getDiscountRate());
            
            $gr_entity->setCreatedBy($u);
            $gr_entity->setCreatedOn($createdOn);
            $gr_entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
            $this->doctrineEM->persist($gr_entity);
            
            /**
             * create procure good receipt.
             * only for item controlled inventory
             * ===================
             */
            
            /**
             *
             * @todo: only for item with stock control.
             */
            if ($r->getItem()->getIsStocked() == 0) {
                // continue;
            }
            
            $criteria = array(
                'isActive' => 1,
                'invoiceRow' => $r
            );
            $stock_gr_entity_ck = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryTrx')->findOneBy($criteria);
            
            if ($stock_gr_entity_ck instanceof \Application\Entity\NmtInventoryTrx) {
                $stock_gr_entity = $stock_gr_entity_ck;
            } else {
                $stock_gr_entity = new NmtInventoryTrx();
            }
            
            $stock_gr_entity->setIsActive(1);
            $stock_gr_entity->setTrxDate($entity->getGrDate());
            
            $stock_gr_entity->setVendorInvoice($entity);
            $stock_gr_entity->setInvoiceRow($r);
            $stock_gr_entity->setItem($r->getItem());
            $stock_gr_entity->setPrRow($r->getPrRow());
            $stock_gr_entity->setPoRow($r->getPoRow());
            $stock_gr_entity->setGrRow($gr_entity);
            
            $stock_gr_entity->setIsDraft($r->getIsDraft());
            $stock_gr_entity->setIsPosted($r->getIsPosted());
            $stock_gr_entity->setDocStatus($r->getDocStatus());
            $stock_gr_entity->setTransactionType($entity->getTransactionType());
            
            $stock_gr_entity->setSourceClass(get_class($r));
            $stock_gr_entity->setSourceId($r->getId());
            
            $stock_gr_entity->setTransactionType($r->getTransactionType());
            $stock_gr_entity->setCurrentState($entity->getCurrentState());
            
            $stock_gr_entity->setVendor($entity->getVendor());
            $stock_gr_entity->setFlow(\Application\Model\Constants::WH_TRANSACTION_IN);
            
            $stock_gr_entity->setQuantity($r->getQuantity());
            $stock_gr_entity->setVendorItemCode($r->getVendorItemCode());
            $stock_gr_entity->setVendorItemUnit($r->getUnit());
            $stock_gr_entity->setVendorUnitPrice($r->getUnitPrice());
            $stock_gr_entity->setTrxDate($entity->getGrDate());
            $stock_gr_entity->setCurrency($entity->getCurrency());
            $stock_gr_entity->setTaxRate($r->getTaxRate());
            
            $stock_gr_entity->setRemarks('AP Row' . $r->getRowIdentifer());
            $stock_gr_entity->setWh($entity->getWarehouse());
            $stock_gr_entity->setCreatedBy($u);
            $stock_gr_entity->setCreatedOn($createdOn);
            $stock_gr_entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
            $this->doctrineEM->persist($stock_gr_entity);
        }
        
        $this->doctrineEM->flush();
        
        // LOGGING
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $changeArray = $nmtPlugin->objectsAreIdentical($oldEntity, $entity);
        
        $m = sprintf('[OK] AP Invoice #%s - %s posted.', $entity->getId(), $entity->getSysNumber());
        
        // Trigger Change Log. AbtractController is EventManagerAware.
        $this->getEventManager()->trigger('finance.change.log', __METHOD__, array(
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

    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     * 
     *  @param EntityManager $doctrineEM
     *  @return \Procure\Service\GrService
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Zend\EventManager\EventManagerAwareInterface::setEventManager()
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $eventManager->setIdentifiers(array(__CLASS__));
        $this->eventManager = $eventManager;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Zend\EventManager\EventsCapableInterface::getEventManager()
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }

}
