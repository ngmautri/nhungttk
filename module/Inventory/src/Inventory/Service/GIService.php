<?php
namespace Inventory\Service;

use Application\Entity\NmtInventoryTrx;
use Doctrine\ORM\EntityManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Math\Rand;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GIService implements EventManagerAwareInterface
{

    protected $doctrineEM;

    protected $eventManager;

    /**
     *
     * @param \Application\Entity\NmtProcureGr $entity
     *            ;
     * @param \Application\Entity\MlaUsers $u
     *            ;
     * @param \Application\Controller\Plugin\NmtPlugin $nmtPlugin
     *            ;
     * @return \Doctrine\ORM\EntityManager
     */
    public function doPosting($entity, $u, $nmtPlugin)
    {
        if (! $entity instanceof \Application\Entity\NmtProcureGr) {
            return;
        }

        $criteria = array(
            'isActive' => 1,
            'gr' => $entity
        );
        $gr_rows = $this->doctrineEM->getRepository('Application\Entity\NmtProcureGrRow')->findBy($criteria);

        if (count($gr_rows) > 0) {

            $n = 0;
            foreach ($gr_rows as $r) {

                $n ++;

                /** @var \Application\Entity\NmtProcureGrRow $r ; */

                // UPDATE status
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
                $stock_gr_entity->setRemarks('PO-GR.' . $r->getRowIdentifer());
                $stock_gr_entity->setWh($entity->getWarehouse());
                $stock_gr_entity->setCreatedBy($u);
                $stock_gr_entity->setCreatedOn(new \DateTime());
                $stock_gr_entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
                $stock_gr_entity->setChecksum(Rand::getString(32, \Application\Model\Constants::CHAR_LIST, true));

                $stock_gr_entity->setTaxRate($r->getTaxRate());

                $this->doctrineEM->persist($stock_gr_entity);

                /**
                 *
                 * @todo create serial number
                 *       if item with Serial
                 *       or Fixed Asset
                 */
                if ($r->getItem() !== null) {
                    if ($r->getItem()->getMonitoredBy() == \Application\Model\Constants::ITEM_WITH_SERIAL_NO or $r->getItem()->getIsFixedAsset() == 1) {

                        for ($i = 0; $i < $r->getQuantity(); $i ++) {

                            // create serial number
                            $sn_entity = new \Application\Entity\NmtInventoryItemSerial();
                            $sn_entity->setItem($r->getItem());
                            $sn_entity->setInventoryTrx($stock_gr_entity);
                            $sn_entity->setIsActive(1);

                            $sn_entity->setSysNumber($nmtPlugin->getDocNumber($sn_entity));
                            $sn_entity->setCreatedBy($u);
                            $sn_entity->setCreatedOn($r->getCreatedOn());
                            $sn_entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
                            $this->doctrineEM->persist($sn_entity);
                        }
                    }
                }

                /**
                 *
                 * @todo: Create FIFO Layer
                 */
                if ($r->getItem() !== null) {
                    if ($r->getItem()->getIsStocked() == 1) {
                        $fifoLayer = new \Application\Entity\NmtInventoryFifoLayer();
                        $fifoLayer->setIsClosed(0);

                        $fifoLayer->setItem($r->getItem());
                        $fifoLayer->setQuantity($r->getQuantity());

                        // will be changed uppon inventory transaction.
                        $fifoLayer->setOnhandQuantity($r->getQuantity());

                        $fifoLayer->setDocUnitPrice($r->getUnitPrice());
                        $fifoLayer->setLocalCurrency($r->getGR()
                            ->getCurrency());
                        $fifoLayer->setExchangeRate($r->getGr()
                            ->getExchangeRate());
                        $fifoLayer->setPostingDate($r->getGR()
                            ->getGrDate());
                        $fifoLayer->setSourceClass(get_class($r));
                        $fifoLayer->setSourceId($r->getID());
                        $fifoLayer->setSourceToken($r->getToken());

                        $fifoLayer->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true));
                        $fifoLayer->setCreatedBy($u);
                        $fifoLayer->setCreatedOn($r->getCreatedOn());

                        $this->doctrineEM->persist($fifoLayer);
                    }
                }
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
     * @param EntityManager $doctrineEM
     * @return \Procure\Service\GrService
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Zend\EventManager\EventManagerAwareInterface::setEventManager()
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $eventManager->setIdentifiers(array(
            __CLASS__
        ));
        $this->eventManager = $eventManager;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Zend\EventManager\EventsCapableInterface::getEventManager()
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }
}
