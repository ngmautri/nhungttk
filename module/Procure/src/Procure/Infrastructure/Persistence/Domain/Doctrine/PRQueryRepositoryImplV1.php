<?php
namespace Procure\Infrastructure\Persistence\Domain\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Procure\Domain\PurchaseRequest\Factory\PrFactory;
use Procure\Domain\PurchaseRequest\Repository\PrQueryRepositoryInterface;
use Procure\Infrastructure\Mapper\PrMapper;
use Procure\Infrastructure\Persistence\Domain\Doctrine\Helper\PrHeaderHelper;
use Procure\Infrastructure\Persistence\Domain\Doctrine\Helper\PrRowHelper;
use Procure\Infrastructure\Persistence\SQL\Filter\PrHeaderReportSqlFilter;
use Procure\Infrastructure\Persistence\SQL\Filter\PrRowReportSqlFilter;
use Generator;

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

        /**
         *
         * @var \Application\Entity\NmtProcurePr $entity ;
         */
        $filterHeader = new PrHeaderReportSqlFilter();
        $filterHeader->setPrId($id);

        $filterRows = new PrRowReportSqlFilter();

        $snapshot = PrHeaderHelper::getPRSnapshot($this->getDoctrineEM(), $filterHeader, $filterRows);
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

        $rootEntity = PrFactory::constructFromDB($rootSnapshot);
        $rootEntity->setRowsGenerator($this->getRowsById($id)); // Important
        return $rootEntity;
    }

    /**
     *
     * @param int $id
     * @return Generator
     */
    private function getRowsById($id)
    {
        $filter = new PrRowReportSqlFilter();
        $filter->setPrId($id);
        $filter->setBalance(1000);
        $filter->setSortBy('itemName');
        $filter->setSort('desc');
        $rows = PrRowHelper::getRowsByPrId($this->getDoctrineEM(), $filter);
        return PrRowHelper::createRowsGenerator($this->getDoctrineEM(), $rows);
    }
}
