<?php
namespace Procure\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Procure\Domain\QuotationRequest\QRRow;
use Procure\Domain\QuotationRequest\Factory\QRFactory;
use Procure\Domain\QuotationRequest\Repository\QrQueryRepositoryInterface;
use Procure\Infrastructure\Mapper\QrMapper;

/**
 *
 * @deprecated
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class QRQueryRepositoryImpl extends AbstractDoctrineRepository implements QrQueryRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\QuotationRequest\Repository\QrQueryRepositoryInterface::getHeaderIdByRowId()
     */
    public function getHeaderIdByRowId($id)
    {
        $sql = "
SELECT
nmt_procure_qo_row.qo_id AS qoId
FROM nmt_procure_qo_row
WHERE id = %s";

        $sql = sprintf($sql, $id);

        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcureQoRow', 'nmt_procure_qo_row');
            $rsm->addScalarResult("qoId", "qoId");
            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            return $query->getSingleResult()["qoId"];
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\QuotationRequest\Repository\QrQueryRepositoryInterface::getVersion()
     */
    public function getVersion($id, $token = null)
    {
        $criteria = array(
            'id' => $id
        );

        /**
         *
         * @var \Application\Entity\NmtProcureQo $doctrineEntity ;
         */

        $doctrineEntity = $this->doctrineEM->getRepository('\Application\Entity\NmtProcureQo')->findOneBy($criteria);
        if ($doctrineEntity !== null) {
            return $doctrineEntity->getRevisionNo();
        }
        return null;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\QuotationRequest\Repository\QrQueryRepositoryInterface::getVersionArray()
     */
    public function getVersionArray($id, $token = null)
    {
        $criteria = array(
            'id' => $id
        );

        /**
         *
         * @var \Application\Entity\NmtProcureQo $doctrineEntity ;
         */

        $doctrineEntity = $this->doctrineEM->getRepository('\Application\Entity\NmtProcureQo')->findOneBy($criteria);
        if ($doctrineEntity !== null) {
            return [
                "revisionNo" => $doctrineEntity->getRevisionNo(),
                "docVersion" => $doctrineEntity->getDocVersion()
            ];
        }

        return null;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\QuotationRequest\Repository\QrQueryRepositoryInterface::getHeaderById()
     */
    public function getHeaderById($id, $token = null)
    {
        $criteria = array(
            'id' => $id,
            'token' => $token
        );

        /**
         *
         * @var \Application\Entity\NmtProcureQo $entity ;
         */
        $entity = $this->doctrineEM->getRepository('\Application\Entity\NmtProcureQo')->findOneBy($criteria);
        $snapshot = QrMapper::createSnapshot($this->doctrineEM, $entity);

        if ($snapshot == null) {
            return null;
        }

        return QRFactory::constructFromDB($snapshot);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\QuotationRequest\Repository\QrQueryRepositoryInterface::getRootEntityByTokenId()
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
            ->getRepository('\Application\Entity\NmtProcureQo')
            ->findOneBy($criteria);

        if ($rootEntityDoctrine == null) {
            return null;
        }

        $rootSnapshot = QrMapper::createSnapshot($this->getDoctrineEM(), $rootEntityDoctrine);
        if ($rootSnapshot == null) {
            return null;
        }

        $rows = $this->getRowsById($id);

        if (count($rows) == 0) {
            $rootEntity = QRFactory::constructFromDB($rootSnapshot);
            return $rootEntity;
        }

        $totalRows = 0;
        $totalActiveRows = 0;
        $netAmount = 0;
        $taxAmount = 0;
        $grossAmount = 0;
        foreach ($rows as $r) {

            /**@var \Application\Entity\NmtProcureQoRow $localEnityDoctrine ;*/
            $localEnityDoctrine = $r;

            $localSnapshot = QrMapper::createRowSnapshot($this->getDoctrineEM(), $localEnityDoctrine);

            if ($localSnapshot == null) {
                continue;
            }
            $netAmount = $netAmount + $localSnapshot->getNetAmount();
            $taxAmount = $taxAmount + $localSnapshot->getTaxAmount();
            $grossAmount = $grossAmount + $localSnapshot->getGrossAmount();

            $totalRows ++;
            $localEntity = QRRow::makeFromSnapshot($localSnapshot);
            $docRowsArray[] = $localEntity;
            $rowIdArray[] = $localEntity->getId();
            // break;
        }

        $rootSnapshot->totalRows = $totalRows;
        $rootSnapshot->totalActiveRows = $totalActiveRows;

        $rootSnapshot->netAmount = $netAmount;
        $rootSnapshot->taxAmount = $taxAmount;
        $rootSnapshot->grossAmount = $grossAmount;

        $rootEntity = QRFactory::constructFromDB($rootSnapshot);
        $rootEntity->setDocRows($docRowsArray);
        $rootEntity->setRowIdArray($rowIdArray);
        return $rootEntity;
    }

    private function getRowsById($id)
    {
        $sql = "select
*
from nmt_procure_qo_row
where 1%s";

        $tmp1 = sprintf(" AND nmt_procure_qo_row.qo_id=%s AND nmt_procure_qo_row.is_active=1 ORDER BY row_number", $id);

        $sql = sprintf($sql, $tmp1);

        // echo $sql;
        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcureQoRow', 'nmt_procure_qo_row');
            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            return $query->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }
}
