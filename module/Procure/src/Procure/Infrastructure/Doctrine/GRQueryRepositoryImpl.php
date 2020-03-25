<?php
namespace Procure\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\PurchaseOrder\PORow;
use Procure\Domain\QuotationRequest\QRQueryRepositoryInterface;
use Procure\Domain\Shared\Constants;
use Procure\Infrastructure\Doctrine\SQL\PoSQL;
use Procure\Infrastructure\Mapper\PoMapper;
use Procure\Infrastructure\Mapper\GrMapper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRQueryRepositoryImpl extends AbstractDoctrineRepository implements QRQueryRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\QuotationRequest\QRQueryRepositoryInterface::getGrDetailsByTokenId()
     */
    public function getGrDetailsByTokenId($id, $token = null)
    {
        if ($id == null || $token == null) {
            return null;
        }

        $criteria = array(
            'id' => $id,
            'token' => $token
        );

        $doctrineRootEntity = $this->getDoctrineEM()
            ->getRepository('\Application\Entity\NmtProcureGr')
            ->findOneBy($criteria);

        $rootSnapshot = GrMapper::createDetailSnapshot($doctrineRootEntity);

        if ($rootSnapshot == null) {
            return null;
        }

        $rows = $this->getRowsDetails($id);

        if (count($rows) == 0) {
            $rootEntity = PODoc::makeFromDetailsSnapshot($poDetailsSnapshot);
            return $rootEntity;
        }

        $completed = True;
        $docRowsArray = array();
        $rowIdArray = array();
        $totalRows = 0;
        $totalActiveRows = 0;
        $netAmount = 0;
        $taxAmount = 0;
        $grossAmount = 0;
        $discountAmount = 0;
        $billedAmount = 0;
        $completedRows = 0;

        foreach ($rows as $r) {

            /**@var \Application\Entity\NmtProcurePoRow $poRowEntity ;*/
            $po_row = $r[0];

            $poRowDetailSnapshot = PoMapper::createRowDetailSnapshot($po_row);

            if ($poRowDetailSnapshot == null) {
                continue;
            }

            /**
             *
             * @todo
             */
            if ($r['open_gr_qty'] <= 0 and $r['open_ap_qty'] <= 0) {
                $poRowDetailSnapshot->transactionStatus = Constants::TRANSACTION_STATUS_COMPLETED;
                $completedRows ++;
            } else {
                $completed = false;
                $poRowDetailSnapshot->transactionStatus = Constants::TRANSACTION_STATUS_UNCOMPLETED;
            }

            $poRowDetailSnapshot->draftGrQuantity = $r["draft_gr_qty"];
            $poRowDetailSnapshot->postedGrQuantity = $r["posted_gr_qty"];
            $poRowDetailSnapshot->confirmedGrBalance = $r["confirmed_gr_balance"];
            $poRowDetailSnapshot->openGrBalance = $r["open_gr_qty"];
            $poRowDetailSnapshot->draftAPQuantity = $r["draft_ap_qty"];
            $poRowDetailSnapshot->postedAPQuantity = $r["posted_ap_qty"];
            $poRowDetailSnapshot->openAPQuantity = $r["open_ap_qty"];
            $poRowDetailSnapshot->billedAmount = $r["billed_amount"];
            $poRowDetailSnapshot->openAPAmount = $poRowDetailSnapshot->netAmount - $poRowDetailSnapshot->billedAmount;

            $totalRows ++;
            $totalActiveRows ++;
            $netAmount = $netAmount + $poRowDetailSnapshot->netAmount;
            $taxAmount = $taxAmount + $poRowDetailSnapshot->taxAmount;
            $grossAmount = $grossAmount + $poRowDetailSnapshot->grossAmount;
            $billedAmount = $billedAmount + $poRowDetailSnapshot->billedAmount;

            $poRow = PORow::makeFromDetailsSnapshot($poRowDetailSnapshot);
            $docRowsArray[] = $poRow;
            $rowIdArray[] = $poRow->getId();
        }

        if ($completed == true) {
            $poDetailsSnapshot->transactionStatus = Constants::TRANSACTION_STATUS_COMPLETED;
        } else {
            $poDetailsSnapshot->transactionStatus = Constants::TRANSACTION_STATUS_UNCOMPLETED;
        }

        $poDetailsSnapshot->totalRows = $totalRows;
        $poDetailsSnapshot->totalActiveRows = $totalActiveRows;
        $poDetailsSnapshot->netAmount = $netAmount;
        $poDetailsSnapshot->taxAmount = $taxAmount;
        $poDetailsSnapshot->grossAmount = $grossAmount;
        $poDetailsSnapshot->discountAmount = $discountAmount;
        $poDetailsSnapshot->billedAmount = $billedAmount;
        $poDetailsSnapshot->completedRows = $completedRows;

        // $currencies = new ISOCurrencies();
        // $numberFormatter = new \NumberFormatter('en', \NumberFormatter::CURRENCY_SYMBOL);
        // $moneyFormatter = new DecimalMoneyFormatter($currencies);
        // $moneyParser = new DecimalMoneyParser($currencies);
        // var_dump($poDetailsSnapshot->currencyIso3);

        // $netMoney = $moneyParser->parse("$netAmount", $poDetailsSnapshot->currencyIso3);
        // $billedMoney = $moneyParser->parse("$billedAmount", $poDetailsSnapshot->currencyIso3);

        // $poDetailsSnapshot->openAPAmount = $netMoney->subtract($billedMoney);

        $rootEntity = PODoc::makeFromDetailsSnapshot($poDetailsSnapshot);
        $rootEntity->setDocRows($docRowsArray);
        $rootEntity->setRowIdArray($rowIdArray);
        return $rootEntity;
    }

    public function getHeaderById($id, $token = null)
    {
        if ($token == null) {
            $criteria = array(
                'id' => $id
            );
        } else {
            $criteria = array(
                'id' => $id,
                'token' => $token
            );
        }

        $entity = $this->doctrineEM->getRepository('\Application\Entity\NmtProcureGr')->findOneBy($criteria);
        $snapshot = GrMapper::createDetailSnapshot($entity);

        if ($snapshot == null) {
            return null;
        }

        return $snapshot;
    }

    public function getById($id, $outputStragegy = null)
    {}

    public function getByUUID($uuid)
    {}

    public function findAll()
    {}
    
    /**
     *
     * @param int $id
     * @return array|mixed|\Doctrine\DBAL\Driver\Statement|NULL|NULL
     */
    private function getRowsDetails($id)
    {
        $sql = "
SELECT
*
FROM nmt_procure_gr_row
            
LEFT JOIN
(%s)
AS fin_vendor_invoice_row
ON fin_vendor_invoice_row.gr_row_id = nmt_procure_gr_row.id
            
LEFT JOIN
(%s)
AS nmt_procure_gr_row
ON nmt_procure_gr_row.po_row_id = nmt_procure_po_row.id
            
WHERE nmt_procure_po_row.po_id=%s AND nmt_procure_po_row.is_active=1 order by row_number";
        
        /**
         *
         * @todo To add Return and Credit Memo
         */
        
        $tmp1 = sprintf(" AND nmt_procure_po_row.po_id=%s AND nmt_procure_po_row.is_active=1", $id);
        $sql1 = sprintf(PoSQL::SQL_ROW_PO_AP, $tmp1);
        $sql2 = sprintf(PoSQL::SQL_ROW_PO_GR, $tmp1);
        
        $sql = sprintf($sql, $sql1, $sql2, $id);
        
        // echo $sql;
        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePoRow', 'nmt_procure_po_row');
            $rsm->addScalarResult("draft_gr_qty", "draft_gr_qty");
            $rsm->addScalarResult("posted_gr_qty", "posted_gr_qty");
            $rsm->addScalarResult("confirmed_gr_balance", "confirmed_gr_balance");
            $rsm->addScalarResult("open_gr_qty", "open_gr_qty");
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
