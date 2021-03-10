<?php
namespace Procure\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Procure\Domain\AccountPayable\APRow;
use Procure\Domain\AccountPayable\Factory\APFactory;
use Procure\Domain\AccountPayable\Repository\APQueryRepositoryInterface;
use Procure\Infrastructure\Mapper\ApMapper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class APQueryRepositoryImpl extends AbstractDoctrineRepository implements APQueryRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\AccountPayable\Repository\APQueryRepositoryInterface::getHeaderIdByRowId()
     */
    public function getHeaderIdByRowId($id)
    {
        $criteria = array(
            'id' => $id
        );

        $doctrineEntity = $this->doctrineEM->getRepository('\Application\Entity\FinVendorInvoiceRow')->findOneBy($criteria);
        if ($doctrineEntity == null) {
            return null;
        }

        if ($doctrineEntity->getPr() != null) {
            return $doctrineEntity->getPr()->getId();
        }

        return null;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\AccountPayable\Repository\APQueryRepositoryInterface::getVersion()
     */
    public function getVersion($id, $token = null)
    {
        $criteria = array(
            'id' => $id
        );

        /**
         *
         * @var \Application\Entity\FinVendorInvoice $doctrineEntity ;
         */

        $doctrineEntity = $this->doctrineEM->getRepository('\Application\Entity\FinVendorInvoice')->findOneBy($criteria);
        if ($doctrineEntity !== null) {
            return $doctrineEntity->getRevisionNo();
        }
        return null;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\AccountPayable\Repository\APQueryRepositoryInterface::getVersionArray()
     */
    public function getVersionArray($id, $token = null)
    {
        $criteria = array(
            'id' => $id
        );

        /**
         *
         * @var \Application\Entity\FinVendorInvoice $doctrineEntity ;
         */

        $doctrineEntity = $this->doctrineEM->getRepository('\Application\Entity\FinVendorInvoice')->findOneBy($criteria);
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
     * @see \Procure\Domain\AccountPayable\Repository\APQueryRepositoryInterface::getHeaderById()
     */
    public function getHeaderById($id, $token = null)
    {
        $criteria = array(
            'id' => $id,
            'token' => $token
        );

        /**
         *
         * @var \Application\Entity\FinVendorInvoice $entity ;
         */
        $entity = $this->doctrineEM->getRepository('\Application\Entity\FinVendorInvoice')->findOneBy($criteria);
        $snapshot = ApMapper::createSnapshot($this->doctrineEM, $entity);

        if ($snapshot == null) {
            return null;
        }

        return APFactory::constructFromDB($snapshot);
    }

    public function getById($id, $outputStragegy = null)
    {}

    public function getHeaderDTO($id, $token = null)
    {}

    public function getByUUID($uuid)
    {}

    public function findAll()
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\AccountPayable\Repository\APQueryRepositoryInterface::getRootEntityByTokenId()
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
            ->getRepository('\Application\Entity\FinVendorInvoice')
            ->findOneBy($criteria);

        if ($rootEntityDoctrine == null) {
            return null;
        }

        $rootSnapshot = ApMapper::createSnapshot($this->getDoctrineEM(), $rootEntityDoctrine);
        if ($rootSnapshot == null) {
            return null;
        }

        $rows = $this->getRowsById($id);

        if (count($rows) == 0) {
            $rootEntity = APFactory::constructFromDB($rootSnapshot);
            return $rootEntity;
        }

        $totalRows = 0;
        $totalActiveRows = 0;
        $netAmount = 0;
        $taxAmount = 0;
        $grossAmount = 0;
        foreach ($rows as $r) {

            /**@var \Application\Entity\FinVendorInvoiceRow $localEnityDoctrine ;*/
            $localEnityDoctrine = $r;

            $localSnapshot = ApMapper::createRowSnapshot($this->getDoctrineEM(), $localEnityDoctrine);

            if ($localSnapshot == null) {
                continue;
            }

            $totalRows ++;
            $totalActiveRows ++;
            $netAmount = $netAmount + $localSnapshot->netAmount;
            $taxAmount = $taxAmount + $localSnapshot->taxAmount;
            $grossAmount = $grossAmount + $localSnapshot->grossAmount;

            $localEntity = APRow::makeFromSnapshot($localSnapshot);

            $docRowsArray[] = $localEntity;
            $rowIdArray[] = $localEntity->getId();

            // break;
        }

        $rootSnapshot->totalRows = $totalRows;
        $rootSnapshot->totalActiveRows = $totalActiveRows;
        $rootSnapshot->netAmount = $netAmount;
        $rootSnapshot->taxAmount = $taxAmount;
        $rootSnapshot->grossAmount = $grossAmount;

        $rootEntity = APFactory::constructFromDB($rootSnapshot);
        $rootEntity->setDocRows($docRowsArray);
        $rootEntity->setRowIdArray($rowIdArray);
        return $rootEntity;
    }

    /**
     *
     * @param int $id
     * @return array|mixed|\Doctrine\DBAL\Driver\Statement|NULL|NULL
     */
    private function getRowsById($id)
    {
        $sql = "
SELECT
fin_vendor_invoice_row.*
FROM fin_vendor_invoice_row
LEFT JOIN fin_vendor_invoice
ON fin_vendor_invoice.id = fin_vendor_invoice_row.invoice_id
WHERE fin_vendor_invoice_row.invoice_id=%s AND fin_vendor_invoice_row.is_active=1 order by row_number";

        $sql = sprintf($sql, $id);

        // echo $sql;
        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\FinVendorInvoiceRow', 'fin_vendor_invoice_row');
            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            return $query->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }
}
