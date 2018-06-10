<?php
namespace Procure\Service;

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
class GrService implements EventManagerAwareInterface
{

    protected $doctrineEM;
    protected $eventManager = null;

    
    /**
     * 
     *  @param \Application\Entity\NmtProcureGr $entity
     *  @param \Application\Entity\MlaUsers $u
     *  
     *  @return \Doctrine\ORM\EntityManager
     */
    public function postGR($entity, $u)
    {
        if(!$entity instanceof \Application\Entity\NmtProcureGr){
            return;
        }
        
        //Posting
        $oldEntity = clone ($entity);
        
        $lastchangeOn = new \Datetime();
        
        $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_POSTED);
        $entity->setIsDraft(0);
        $entity->getLastchangeBy($u);
        $entity->setLastchangeOn($lastchangeOn);
        
        $this->doctrineEM->persist($entity);
        $this->doctrineEM->flush();
        
        // UPDATE GR ROW & CREATE STOCK GR
        $criteria = array(
            'isActive' => 1,
            'gr' => $entity
        );
        $gr_rows = $this->doctrineEM->getRepository('Application\Entity\NmtProcureGrRow')->findBy($criteria);
        
        if (count($gr_rows) > 0) {
            $n = 0;
            foreach ($gr_rows as $r) {
                /** @var \Application\Entity\NmtProcureGrRow $r ; */
                
                // UPDATE status
                $n ++;
                $r->setIsPosted(1);
                $r->setIsDraft(0);
                $r->setDocStatus($entity->getDocStatus());
                $r->setRowIdentifer($entity->getSysNumber() . '-' . $n);
                $r->setRowNumber($n);
                
                /**
                 *
                 * @todo: only for item in stock.
                 */
                if ($r->getItem()->getIsStocked() == 0) {
                    // continue;
                }
                
                $criteria = array(
                    'isActive' => 1,
                    'grRow' => $r
                );
                $stock_gr_entity_ck = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryTrx')->findOneBy($criteria);
                
                $stock_gr_entity = null;
                
                if ($stock_gr_entity_ck instanceof \Application\Entity\NmtInventoryTrx) {
                    $stock_gr_entity = $stock_gr_entity_ck;
                } else {
                    $stock_gr_entity = new NmtInventoryTrx();
                }
                
                $stock_gr_entity->setGrRow($r);
                
                $stock_gr_entity->setSourceClass(get_class($r));
                $stock_gr_entity->setSourceId($r->getId());
                
                $stock_gr_entity->setTransactionType($r->getTransactionType());
                $stock_gr_entity->setCurrentState($entity->getCurrentState());
                $stock_gr_entity->setDocStatus($r->getDocStatus());
                $stock_gr_entity->setIsActive($r->getIsActive());
                
                $stock_gr_entity->setVendor($entity->getVendor());
                $stock_gr_entity->setFlow(\Application\Model\Constants::WH_TRANSACTION_IN);
                
                $stock_gr_entity->setItem($r->getItem());
                $stock_gr_entity->setPrRow($r->getPrRow());
                $stock_gr_entity->setPoRow($r->getPoRow());
                $stock_gr_entity->setQuantity($r->getQuantity());
                $stock_gr_entity->setVendorItemCode($r->getVendorItemCode());
                $stock_gr_entity->setVendorItemUnit($r->getUnit());
                $stock_gr_entity->setVendorUnitPrice($r->getUnitPrice());
                $stock_gr_entity->setTrxDate($entity->getGrDate());
                $stock_gr_entity->setCurrency($entity->getCurrency());
                $stock_gr_entity->setRemarks('PO Gr ' . $r->getRowIdentifer());
                $stock_gr_entity->setWh($entity->getWarehouse());
                $stock_gr_entity->setCreatedBy($u);
                $stock_gr_entity->setCreatedOn(new \DateTime());
                $stock_gr_entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
                $stock_gr_entity->setChecksum(Rand::getString(32, self::CHAR_LIST, true));
                
                $stock_gr_entity->setTaxRate($r->getTaxRate());
                
                $this->doctrineEM->persist($stock_gr_entity);
            }
            
            $this->doctrineEM->flush();
        }
        
        
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
