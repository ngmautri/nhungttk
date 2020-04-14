<?php
namespace Procure\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Procure\Domain\AccountPayable\APDoc;
use Procure\Domain\AccountPayable\APRow;
use Procure\Domain\AccountPayable\Repository\APQueryRepositoryInterface;
use Procure\Domain\Shared\Constants;
use Procure\Infrastructure\Doctrine\SQL\ApSQL;
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

    public function getHeaderById($id, $token = null)
    {}

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

        $rootSnapshot = ApMapper::createDetailSnapshot($rootEntityDoctrine);
        if ($rootSnapshot == null) {
            return null;
        }

        $rows = $this->getRowsById($id);

        if (count($rows) == 0) {
            $rootEntity = APDoc::makeFromSnapshot($rootSnapshot);
            return $rootEntity;
        }

        $completed = True;
        $completedRows = 0;
        $totalRows = 0;
        $totalActiveRows = 0;
        $netAmount = 0;
        $taxAmount = 0;
        $billedAmount = 0;
        $grossAmount = 0;
        foreach ($rows as $r) {

            /**@var \Application\Entity\FinVendorInvoiceRow $localEnityDoctrine ;*/
            $localEnityDoctrine = $r[0];

            $localSnapshot = ApMapper::createRowDetailSnapshot($localEnityDoctrine);

            if ($localSnapshot == null) {
                continue;
            }

            /**
             *
             * @todo
             */
            if ($r['open_ap_qty'] <= 0) {
                $localSnapshot->transactionStatus = Constants::TRANSACTION_STATUS_COMPLETED;
                $completedRows ++;
            } else {
                $completed = false;
                $localSnapshot->transactionStatus = Constants::TRANSACTION_STATUS_UNCOMPLETED;
            }

            $localSnapshot->draftAPQuantity = $r["draft_ap_qty"];
            $localSnapshot->postedAPQuantity = $r["posted_ap_qty"];
            $localSnapshot->openAPQuantity = $r["open_ap_qty"];
            $localSnapshot->billedAmount = $r["billed_amount"];
            $localSnapshot->openAPAmount = $localSnapshot->netAmount - $localSnapshot->billedAmount;

            $totalRows ++;
            $totalActiveRows ++;
            $netAmount = $netAmount + $localSnapshot->netAmount;
            $taxAmount = $taxAmount + $localSnapshot->taxAmount;
            $grossAmount = $grossAmount + $localSnapshot->grossAmount;
            $billedAmount = $billedAmount + $localSnapshot->billedAmount;

            $localEntity = APRow::makeFromSnapshot($localSnapshot);

            $docRowsArray[] = $localEntity;
            $rowIdArray[] = $localEntity->getId();

            // break;
        }

        if ($completed == true) {
            $rootSnapshot->transactionStatus = Constants::TRANSACTION_STATUS_COMPLETED;
        } else {
            $rootSnapshot->transactionStatus = Constants::TRANSACTION_STATUS_UNCOMPLETED;
        }

        $rootSnapshot->totalRows = $totalRows;
        $rootSnapshot->totalActiveRows = $totalActiveRows;
        $rootSnapshot->netAmount = $netAmount;
        $rootSnapshot->taxAmount = $taxAmount;
        $rootSnapshot->grossAmount = $grossAmount;
        $rootSnapshot->billedAmount = $billedAmount;
        $rootSnapshot->completedRows = $completedRows;

        $rootEntity = APDoc::makeFromSnapshot($rootSnapshot);
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
*
FROM nmt_procure_gr_row
            
LEFT JOIN
(%s)
AS fin_vendor_invoice_row
ON fin_vendor_invoice_row.gr_row_id = nmt_procure_gr_row.id
            
WHERE nmt_procure_gr_row.gr_id=%s AND nmt_procure_gr_row.is_active=1 order by row_number";

        /**
         *
         * @todo To add Return and Credit Memo
         */

        $tmp1 = sprintf(" AND nmt_procure_gr_row.gr_id=%s AND nmt_procure_gr_row.is_active=1", $id);
        $sql1 = sprintf(ApSQL::SQL_ROW_AP_GR, $tmp1);

        $sql = sprintf($sql, $sql1, $id);

        // echo $sql;
        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcureGrRow', 'nmt_procure_gr_row');
            $rsm->addScalarResult("draft_ap_qty", "draft_ap_qty");
            $rsm->addScalarResult("posted_ap_qty", "posted_ap_qty");
            $rsm->addScalarResult("confirmed_ap_balance", "confirmed_ap_balance");
            $rsm->addScalarResult("open_ap_qty", "open_ap_qty");
            $rsm->addScalarResult("billed_amount", "billed_amount");
            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            return $query->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }
}
