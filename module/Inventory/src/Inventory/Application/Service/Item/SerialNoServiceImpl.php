<?php
namespace Inventory\Application\Service\Item;

use Application\Service\AbstractService;
use Inventory\Domain\Service\Contracts\SerialNoServiceInterface;
use Procure\Domain\GoodsReceipt\GRRow;
use Procure\Domain\GoodsReceipt\GRSnapshot;
use Ramsey\Uuid\Uuid;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class SerialNoServiceImpl extends AbstractService implements SerialNoServiceInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Service\Contracts\SerialNoServiceInterface::createSerialNoFor()
     */
    public function createSerialNoFor(GRSnapshot $trx)
    {
        if ($trx == null) {
            throw new InvalidArgumentException("GRSnapshot not found");
        }

        $rows = $trx->getDocRows();

        if (count($rows) == 0) {
            throw new InvalidArgumentException("GRSnapshot have no lines");
        }

        foreach ($rows as $row) {

            /**
             *
             * @var GRRow $row ;
             */

            if ($row->getItemMonitorMethod() == \Application\Model\Constants::ITEM_WITH_SERIAL_NO || $row->getIsFixedAsset() == 1) {

                for ($i = 0; $i < $row->getQuantity(); $i ++) {

                    // create new serial number
                    $sn_entity = new \Application\Entity\NmtInventoryItemSerial();

                    if ($row->getItem() > 0) {

                        /**
                         *
                         * @var \Application\Entity\NmtInventoryItem $obj ;
                         */
                        $obj = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->find($row->getItem());
                        $sn_entity->setItem($obj);
                    }

                    if ($row->getApInvoiceRow() > 0) {

                        /**
                         *
                         * @var \Application\Entity\FinVendorInvoiceRow $obj ;
                         */
                        $obj = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoiceRow')->find($row->getApInvoiceRow());
                        $sn_entity->setApRow($obj);
                    }

                    if ($row->getId() > 0) {

                        /**
                         *
                         * @var \Application\Entity\NmtProcureGrRow $obj ;
                         */
                        $obj = $this->doctrineEM->getRepository('Application\Entity\NmtProcureGrRow')->find($row->getId());
                        $sn_entity->setGrRow($obj);
                    }

                    if ($row->getCreatedBy() > 0) {

                        /**
                         *
                         * @var \Application\Entity\MlaUsers $obj ;
                         */
                        $obj = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->find($row->getCreatedBy());
                        $sn_entity->setCreatedBy($obj);
                    }

                    if ($row->getCreatedOn()) {
                        $sn_entity->setCreatedOn(new \DateTime($row->getCreatedOn()));
                    }

                    $sn_entity->setIsActive(1);
                    $sn_entity->setToken(Uuid::uuid4()->toString());
                    $this->getDoctrineEM()->persist($sn_entity);
                }

                $this->getDoctrineEM()->flush();
            }
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Service\Contracts\SerialNoServiceInterface::reverseSerialNoFor()
     */
    public function reverseSerialNoFor(GRSnapshot $trx)
    {}
}
