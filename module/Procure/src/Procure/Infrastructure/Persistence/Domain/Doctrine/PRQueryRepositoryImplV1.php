<?php
namespace Procure\Infrastructure\Persistence\Domain\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Procure\Domain\PurchaseRequest\PRRow;
use Procure\Domain\PurchaseRequest\Factory\PrFactory;
use Procure\Domain\PurchaseRequest\Repository\PrQueryRepositoryInterface;
use Procure\Infrastructure\Mapper\PrMapper;
use Procure\Infrastructure\Persistence\Domain\Doctrine\Helper\PrRowHelper;
use Procure\Infrastructure\Persistence\SQL\Filter\PrRowReportSqlFilter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PRQueryRepositoryImplV1 extends AbstractDoctrineRepository implements PrQueryRepositoryInterface
{

    /**
     *
     * @param int $id
     * @return NULL|\Application\Entity\NmtProcurePr
     */
    private function _getHeaderEntityById($id)
    {
        if ($id == null) {
            return null;
        }

        $criteria = array(
            'id' => $id
        );

        /**
         *
         * @var \Application\Entity\NmtProcurePr $entity ;
         */
        $entity = $this->doctrineEM->getRepository('\Application\Entity\NmtProcurePr')->findOneBy($criteria);
        return $entity;
    }

    /**
     *
     * @param int $rowId
     * @return NULL|\Application\Entity\NmtProcurePr
     */
    private function _getHeaderEntityByRowId($rowId)
    {
        $criteria = array(
            'id' => $rowId
        );

        /**
         *
         * @var \Application\Entity\NmtProcurePrRow $doctrineEntity ;
         */
        $doctrineEntity = $this->doctrineEM->getRepository('\Application\Entity\NmtProcurePrRow')->findOneBy($criteria);
        if ($doctrineEntity == null) {
            return null;
        }

        return $doctrineEntity->getPr();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseRequest\Repository\PrQueryRepositoryInterface::getHeaderSnapshotById()
     */
    public function getHeaderSnapshotById($id)
    {
        return PrMapper::createSnapshot($this->getDoctrineEM(), $this->_getHeaderEntityById($id));
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseRequest\Repository\PrQueryRepositoryInterface::getHeaderSnapshotByRowId()
     */
    public function getHeaderSnapshotByRowId($rowId)
    {
        $doctrineEntity = $this->_getHeaderEntityByRowId($rowId);

        if ($doctrineEntity == null) {
            return null;
        }

        return PrMapper::createSnapshot($this->getDoctrineEM(), $doctrineEntity);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseRequest\Repository\PrQueryRepositoryInterface::getHeaderIdByRowId()
     */
    public function getHeaderIdByRowId($id)
    {
        $doctrineEntity = $this->_getHeaderEntityByRowId($id);

        if ($doctrineEntity == null) {
            return null;
        }

        return $doctrineEntity->getId();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseRequest\Repository\PrQueryRepositoryInterface::getVersion()
     */
    public function getVersion($id, $token = null)
    {
        $doctrineEntity = $this->_getHeaderEntityById($id);
        if ($doctrineEntity != null) {
            return $doctrineEntity->getRevisionNo();
        }
        return null;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseRequest\Repository\PrQueryRepositoryInterface::getVersionArray()
     */
    public function getVersionArray($id, $token = null)
    {
        $doctrineEntity = $this->_getHeaderEntityById($id);
        if ($doctrineEntity !== null) {
            return [
                "revisionNo" => $doctrineEntity->getRevisionNo(),
                "docVersion" => $doctrineEntity->getRevisionNo() // Todo
            ];
        }

        return null;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseRequest\Repository\PrQueryRepositoryInterface::getHeaderById()
     */
    public function getHeaderById($id, $token = null)
    {
        $criteria = array(
            'id' => $id,
            'token' => $token
        );

        /**
         *
         * @var \Application\Entity\NmtProcurePr $entity ;
         */
        $entity = $this->doctrineEM->getRepository('\Application\Entity\NmtProcurePr')->findOneBy($criteria);
        $snapshot = PrMapper::createSnapshot($this->doctrineEM, $entity);

        if ($snapshot == null) {
            return null;
        }

        return PrFactory::constructFromDB($snapshot);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseRequest\Repository\PrQueryRepositoryInterface::getRootEntityByTokenId()
     */
    public function getRootEntityByTokenId($id, $token = null)
    {
        if ($id == null || $token == null) {
            return null;
        }

        $criteria = array(
            'id' => $id,
            'token' => $token
        );

        $rootEntityDoctrine = $this->getDoctrineEM()
            ->getRepository('\Application\Entity\NmtProcurePr')
            ->findOneBy($criteria);

        return $this->_createRootEntity($rootEntityDoctrine, $id);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseRequest\Repository\PrQueryRepositoryInterface::getRootEntityById()
     */
    public function getRootEntityById($id)
    {
        $rootEntityDoctrine = $this->_getHeaderEntityById($id);
        return $this->_createRootEntity($rootEntityDoctrine, $id);
    }

    /**
     *
     * @param object $rootEntityDoctrine
     * @param int $id
     * @return NULL|void|\Procure\Domain\GoodsReceipt\GRDoc
     */
    private function _createRootEntity($rootEntityDoctrine, $id)
    {
        if ($rootEntityDoctrine == null) {
            return null;
        }

        $rootSnapshot = PrMapper::createSnapshot($this->getDoctrineEM(), $rootEntityDoctrine);
        if ($rootSnapshot == null) {
            return null;
        }

        $rows = $this->getRowsById($id);

        if (count($rows) == 0) {
            $rootEntity = PrFactory::constructFromDB($rootSnapshot);
            return $rootEntity;
        }

        foreach ($rows as $r) {

            /**@var \Application\Entity\NmtProcurePrRow $localEnityDoctrine ;*/
            $localEnityDoctrine = $r[0];

            $localSnapshot = PrMapper::createRowSnapshot($this->getDoctrineEM(), $localEnityDoctrine);

            if ($localSnapshot == null) {
                continue;
            }

            $localSnapshot->draftPoQuantity = $r["po_qty"];
            $localSnapshot->postedPoQuantity = $r["posted_po_qty"];

            $localSnapshot->draftGrQuantity = $r["gr_qty"];
            $localSnapshot->postedGrQuantity = $r["posted_gr_qty"];

            $localSnapshot->draftApQuantity = $r["ap_qty"];
            $localSnapshot->postedApQuantity = $r["posted_ap_qty"];

            $localSnapshot->draftStockQrQuantity = $r["stock_gr_qty"];
            $localSnapshot->postedStockQrQuantity = $r["posted_stock_gr_qty"];

            $localSnapshot->setLastVendorName($r["last_vendor_name"]);
            $localSnapshot->setLastUnitPrice($r["last_standard_unit_price"]);
            $localSnapshot->setLastCurrency($r["last_currency_iso3"]);
            $localEntity = PRRow::makeFromSnapshot($localSnapshot);
        }

        $rootEntity = PrFactory::constructFromDB($rootSnapshot);
        return $rootEntity;
    }

    private function getRowsById($id)
    {
        $filter = new PrRowReportSqlFilter();
        $filter->setPrId($id);
        $filter->setSortBy('itemName');
        $filter->setSort('desc');
        return PrRowHelper::getRowsByPrId($this->getDoctrineEM(), $filter);
    }
}
