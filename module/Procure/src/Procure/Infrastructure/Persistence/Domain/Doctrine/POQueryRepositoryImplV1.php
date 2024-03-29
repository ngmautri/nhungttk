<?php
namespace Procure\Infrastructure\Persistence\Domain\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Procure\Application\DTO\Po\PoDocMapDTO;
use Procure\Domain\PurchaseOrder\Factory\POFactory;
use Procure\Domain\PurchaseOrder\Repository\POQueryRepositoryInterface;
use Procure\Domain\PurchaseRequest\Factory\PrFactory;
use Procure\Infrastructure\Mapper\PrMapper;
use Procure\Infrastructure\Persistence\Domain\Doctrine\Helper\PoHeaderHelper;
use Procure\Infrastructure\Persistence\Domain\Doctrine\Helper\PoRowHelper;
use Procure\Infrastructure\Persistence\Domain\Doctrine\Helper\PrHeaderHelper;
use Procure\Infrastructure\Persistence\Domain\Doctrine\Mapper\PoMapper;
use Procure\Infrastructure\Persistence\SQL\Filter\PoHeaderReportSqlFilter;
use Procure\Infrastructure\Persistence\SQL\Filter\PoRowReportSqlFilter;
use Procure\Infrastructure\Persistence\SQL\Filter\PrHeaderReportSqlFilter;
use Procure\Infrastructure\Persistence\SQL\Filter\PrRowReportSqlFilter;
use Generator;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class POQueryRepositoryImplV1 extends AbstractDoctrineRepository implements POQueryRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseOrder\Repository\POQueryRepositoryInterface::getDocMap()
     */
    public function getDocMap($id, $token = null, $offset = null, $limit = null)
    {
        $filter = new poheaderreportsqlfilter();
        $filter->setPoId($id);
        $filter->setLimit($limit);
        $filter->setOffset($offset);
        $results = PoHeaderHelper::getDocMapFor($this->getDoctrineEM(), $filter);

        if ($results == null) {
            yield null;
        }

        foreach ($results as $result) {
            $dto = new PoDocMapDTO();
            $dto->setPoId($result["po_id"]);
            $dto->setPoSysNumber($result["po_sys_number"]);
            $dto->setDocType($result["doc_type"]);
            $dto->setDocId($result["doc_id"]);
            $dto->setDocToken($result["doc_token"]);
            $dto->setDocSysNumber($result["doc_sys_number"]);
            $dto->setDocCurrency($result["doc_currency"]);
            $dto->setDocNetAmount($result["doc_net_amount"]);
            $dto->setLocalNetAmount($result["local_net_amount"]);
            $dto->setDocPostingDate($result["doc_posting_date"]);
            $dto->setDocDate($result["doc_date"]);
            $dto->setDocCreatedDate($result["doc_created_date"]);
            yield $dto;
        }
    }

    public function getDocMapTotal($id, $token = null, $offset = null, $limit = null)
    {
        $filter = new PoHeaderReportSqlFilter();
        $filter->setPoId($id);
        return PoHeaderHelper::getDocMapTotalFor($this->getDoctrineEM(), $filter);
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
            ->getRepository('\Application\Entity\NmtProcurePo')
            ->findOneBy($criteria);

        return $this->_createRootEntity($rootEntityDoctrine, $id);
    }

    public function getOpenItems($id, $token = null)
    {}

    public function getById($id, $outputStragegy = null)
    {}

    public function getHeaderDTO($id, $token = null)
    {}

    public function getByUUID($uuid)
    {}

    public function findAll()
    {}

    public function getPODetailsById($id, $token = null)
    {}

    /*
     * |=============================
     * | PRIVATE
     * | HELPER
     * |=============================
     */

    /**
     *
     * @param int $id
     * @return Generator
     */
    private function getRowsById($id)
    {
        $filter = new PoRowReportSqlFilter();
        $filter->setPoId($id);
        $filter->setBalance(1000);
        $filter->setSortBy('itemName');
        $filter->setSort('desc');
        $rows = PoRowHelper::getRowsByPoId($this->getDoctrineEM(), $filter);
        return PoRowHelper::createRowsGenerator($this->getDoctrineEM(), $rows);
    }

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
     * @see \Procure\Domain\PurchaseRequest\Repository\PrQueryRepositoryInterface::getRootEntityById()
     */
    public function getRootEntityById($id)
    {
        $rootEntityDoctrine = $this->_getHeaderEntityById($id);
        return $this->_createRootEntity($rootEntityDoctrine, $id);
    }

    private function _createRootEntity($rootEntityDoctrine, $id)
    {
        if ($rootEntityDoctrine == null) {
            return null;
        }

        $rootSnapshot = PoMapper::createSnapshot($this->getDoctrineEM(), $rootEntityDoctrine);
        if ($rootSnapshot == null) {
            return null;
        }

        $rootEntity = POFactory::constructFromDB($rootSnapshot);
        $rootEntity->setRowsGenerator($this->getRowsById($id)); // Important
        return $rootEntity;
    }
}
