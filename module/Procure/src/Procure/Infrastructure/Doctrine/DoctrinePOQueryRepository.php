<?php
namespace Procure\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\PurchaseOrder\PORow;
use Procure\Domain\PurchaseOrder\Repository\POQueryRepositoryInterface;
use Procure\Infrastructure\Doctrine\SQL\PoSQL;
use Procure\Infrastructure\Mapper\PoMapper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DoctrinePOQueryRepository extends AbstractDoctrineRepository implements POQueryRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseOrder\Repository\POQueryRepositoryInterface::getPOEventLog()
     */
    public function getPOEventLog($id, $token = null)
    {
        $sql = "
SELECT * FROM message_store
where entity_id=%s and entity_id=%s
";
        $sql = sprintf($sql, $id, $token);

        // echo $sql;
        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\MessageStore', 'message_store');
            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            return $query->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseOrder\Repository\POQueryRepositoryInterface::getHeaderById()
     */
    public function getHeaderById($id, $token = null)
    {
        $po = $this->getDoctrineEM()
            ->getRepository('Application\Entity\NmtProcurePo')
            ->find($id);
        $poDetailsSnapshot = PoMapper::createDetailSnapshot($po);

        if ($poDetailsSnapshot == null) {
            return null;
        }

        return PODoc::makeFromDetailsSnapshot($poDetailsSnapshot);
    }

    public function getById($id, $outputStragegy = null)
    {}

    public function getByUUID($uuid)
    {}

    public function findAll()
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseOrder\Repository\POQueryRepositoryInterface::getPODetailsById()
     */
    public function getPODetailsById($id, $token = null)
    {
        $po = $this->getDoctrineEM()
            ->getRepository('Application\Entity\NmtProcurePo')
            ->find($id);
        $poDetailsSnapshot = PoMapper::createDetailSnapshot($po);

        if ($poDetailsSnapshot == null) {
            return null;
        }

        $rows = $this->getPoRowsDetails($id);

        if (count($rows) == 0) {
            $rootEntity = PODoc::makeFromDetailsSnapshot($poDetailsSnapshot);
            return $rootEntity;
        }

        $completed = True;
        $docRowsArray = array();
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
                $poRowDetailSnapshot->transactionStatus = \Application\Model\Constants::TRANSACTION_STATUS_COMPLETED;
                $completedRows ++;
            } else {
                $completed = false;
                $poRowDetailSnapshot->transactionStatus = \Application\Model\Constants::TRANSACTION_STATUS_UNCOMPLETED;
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
        }

        if ($completed == true) {
            $poDetailsSnapshot->transactionStatus = \Application\Model\Constants::TRANSACTION_STATUS_COMPLETED;
        } else {
            $poDetailsSnapshot->transactionStatus = \Application\Model\Constants::TRANSACTION_STATUS_UNCOMPLETED;
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
        return $rootEntity;
    }

    // +++++++++++++++++++++++++++++++++++++++++

    /**
     *
     * @param int $id
     * @return array|mixed|\Doctrine\DBAL\Driver\Statement|NULL|NULL
     */
    private function getPoRowsDetails($id)
    {
        $sql = "
SELECT
*
FROM nmt_procure_po_row
            
LEFT JOIN
(%s)
AS fin_vendor_invoice_row
ON fin_vendor_invoice_row.po_row_id = nmt_procure_po_row.id
            
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
