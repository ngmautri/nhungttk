<?php
namespace Procure\Infrastructure\Doctrine;

use Application\Entity\NmtProcurePo;
use Application\Entity\NmtProcurePoRow;
use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Procure\Domain\PurchaseOrder\GenericPO;
use Procure\Domain\PurchaseOrder\PODetailsSnapshot;
use Procure\Domain\PurchaseOrder\POQueryRepositoryInterface;
use Procure\Domain\PurchaseOrder\PORow;
use Procure\Domain\PurchaseOrder\PORowDetailsSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DoctrinePOQueryRepository extends AbstractDoctrineRepository implements POQueryRepositoryInterface
{

    public function getHeaderById($id, $token = null)
    {}

    public function getById($id, $outputStragegy = null)
    {}

    public function getByUUID($uuid)
    {}

    public function findAll()
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseOrder\POQueryRepositoryInterface::getPODetailsById()
     */
    public function getPODetailsById($id)
    {
        $rows = $this->getPoRowsDetails($id);

        if (count($rows) == 0) {
            return;
        }

        /**
         *
         * @var \Application\Entity\NmtProcurePo $po ;
         */
        $po = null;
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

            if ($po == null) {
                $po = $po_row->getPo();
            }

            $poRowDetailSnapshot = $this->createRowDetailSnapshot($po_row);

            if ($poRowDetailSnapshot == null) {
                continue;
            }

            if ($r['open_gr_qty'] == 0 and $r['open_ap_qty'] == 0) {
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

            $totalRows ++;
            $totalActiveRows ++;
            $netAmount = $netAmount + $poRowDetailSnapshot->netAmount;
            $taxAmount = $taxAmount + $poRowDetailSnapshot->taxAmount;
            $grossAmount = $grossAmount + $poRowDetailSnapshot->grossAmount;
            $billedAmount = $billedAmount + $poRowDetailSnapshot->billedAmount;

            $poRow = new PORow();
            $poRow->makeFromDetailsSnapshot($poRowDetailSnapshot);
            $docRowsArray[] = $poRow;
        }

        $poDetailsSnapshot = $this->createPODetailSnapshot($po);
        if ($poDetailsSnapshot == null) {
            return null;
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

        $rootEntity = new GenericPO();
        $rootEntity->makeFromDetailsSnapshot($poDetailsSnapshot);

        $rootEntity->setDocRows($docRowsArray);
        return $rootEntity;
    }

    // +++++++++++++++++++++++++++++++++++++++++

    /**
     *
     * @param object $entity
     * @return NULL|\Procure\Domain\PurchaseOrder\PODetailsSnapshot
     */
    private function createPODetailSnapshot(NmtProcurePo $entity)
    {
        if ($entity == null)
            return null;

        $snapshot = new PODetailsSnapshot();

        // Mapping Reference
        // =====================

        // $snapshot->vendor= $entity->getVendor();
        if ($entity->getVendor() !== null) {
            $snapshot->vendor = $entity->getVendor()->getId();
        }

        // $snapshot->pmtTerm = $entity->getPmtTerm();
        if ($entity->getPmtTerm() !== null) {
            $snapshot->pmtTerm = $entity->getPmtTerm()->getId();
            $snapshot->paymentTermName = $entity->getPmtTerm()->getPmtTermName();
            $snapshot->paymentTermCode = $entity->getPmtTerm()->getPmtTermCode();
        }

        // $snapshot->warehouse = $entity->getWarehouse();
        if ($entity->getWarehouse() !== null) {
            $snapshot->warehouse = $entity->getWarehouse()->getId();
            $snapshot->warehouseName = $entity->getWarehouse()->getWhName();
            $snapshot->warehouseCode = $entity->getWarehouse()->getWhCode();
        }

        // $snapshot->createdBy = $entity->getCreatedBy();
        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
            $snapshot->createdByName = $entity->getCreatedBy()->getFirstname() . " " . $entity->getCreatedBy()->getLastname();
        }

        // $snapshot->lastchangeBy = $entity->getLastchangeBy();
        if ($entity->getLastchangeBy() !== null) {
            $snapshot->lastchangeBy = $entity->getLastchangeBy()->getId();
            $snapshot->lastChangedByName = $entity->getLastchangeBy()->getFirstname() . " " . $entity->getLastchangeBy()->getLastname();
        }

        // $snapshot->currency = $entity->getCurrency();
        if ($entity->getCurrency() !== null) {
            $snapshot->currency = $entity->getCurrency()->getId();
        }

        // $snapshot->paymentMethod = $entity->getPaymentMethod();
        if ($entity->getPaymentMethod() !== null) {
            $snapshot->paymentMethod = $entity->getPaymentMethod()->getId();
            $snapshot->paymentMethodName = $entity->getPaymentMethod()->getMethodName();
            $snapshot->paymentMethodCode = $entity->getPaymentMethod()->getMethodCode();
        }

        // $snapshot->localCurrency = $entity->getLocalCurrency();
        if ($entity->getLocalCurrency() !== null) {
            $snapshot->localCurrency = $entity->getLocalCurrency()->getId();
        }

        // $snapshot->docCurrency = $entity->getDocCurrency();
        if ($entity->getDocCurrency() !== null) {
            $snapshot->docCurrency = $entity->getDocCurrency()->getId();
        }

        // $snapshot->incoterm2 = $entity->getIncoterm2();
        if ($entity->getIncoterm2() !== null) {
            $snapshot->incoterm2 = $entity->getIncoterm2()->getId();
            $snapshot->incotermCode = $entity->getIncoterm2()->getIncoterm();
            $snapshot->incotermName = $entity->getIncoterm2()->getIncoterm1();
        }

        // MAPPING DATE
        // =====================
        $snapshot->invoiceDate = $entity->getInvoiceDate();
        if (! $entity->getInvoiceDate() == null) {
            $snapshot->invoiceDate = $entity->getInvoiceDate()->format("Y-m-d");
        }

        $snapshot->postingDate = $entity->getPostingDate();
        if (! $entity->getPostingDate() == null) {
            $snapshot->postingDate = $entity->getPostingDate()->format("Y-m-d");
        }

        $snapshot->grDate = $entity->getGrDate();
        if (! $entity->getGrDate() == null) {
            $snapshot->grDate = $entity->getGrDate()->format("Y-m-d");
        }

        $snapshot->quotationDate = $entity->getQuotationDate();
        if (! $entity->getQuotationDate() == null) {
            $snapshot->quotationDate = $entity->getQuotationDate()->format("Y-m-d");
        }

        // $snapshot->createdOn = $entity->getCreatedOn();
        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d");
        }

        // $snapshot->lastchangeOn = $entity->getLastchangeOn();
        if (! $entity->getLastChangeOn() == null) {
            $snapshot->lastChangeOn = $entity->getLastChangeOn()->format("Y-m-d");
        }

        // $snapshot->contractDate= $entity->getContractDate();
        if (! $entity->getContractDate() == null) {
            $snapshot->contractDate = $entity->getContractDate()->format("Y-m-d");
        }

        // Mapping None-Object Field
        // =====================

        $snapshot->id = $entity->getId();
        $snapshot->token = $entity->getToken();
        $snapshot->vendorName = $entity->getVendorName();
        $snapshot->invoiceNo = $entity->getInvoiceNo();

        $snapshot->currencyIso3 = $entity->getCurrencyIso3();
        $snapshot->exchangeRate = $entity->getExchangeRate();
        $snapshot->remarks = $entity->getRemarks();
        $snapshot->currentState = $entity->getCurrentState();
        $snapshot->isActive = $entity->getIsActive();
        $snapshot->trxType = $entity->getTrxType();
        $snapshot->sapDoc = $entity->getSapDoc();
        $snapshot->contractNo = $entity->getContractNo();
        $snapshot->quotationNo = $entity->getQuotationNo();
        $snapshot->sysNumber = $entity->getSysNumber();
        $snapshot->revisionNo = $entity->getRevisionNo();
        $snapshot->deliveryMode = $entity->getDeliveryMode();
        $snapshot->incoterm = $entity->getIncoterm();
        $snapshot->incotermPlace = $entity->getIncotermPlace();
        $snapshot->paymentTerm = $entity->getPaymentTerm();
        $snapshot->docStatus = $entity->getDocStatus();
        $snapshot->workflowStatus = $entity->getWorkflowStatus();
        $snapshot->transactionStatus = $entity->getTransactionStatus();
        $snapshot->docType = $entity->getDocType();
        $snapshot->paymentStatus = $entity->getPaymentStatus();
        $snapshot->totalDocValue = $entity->getTotalDocValue();
        $snapshot->totalDocTax = $entity->getTotalDocTax();
        $snapshot->totalDocDiscount = $entity->getTotalDocDiscount();
        $snapshot->totalLocalValue = $entity->getTotalLocalValue();
        $snapshot->totalLocalTax = $entity->getTotalLocalTax();
        $snapshot->totalLocalDiscount = $entity->getTotalLocalDiscount();
        $snapshot->reversalBlocked = $entity->getReversalBlocked();
        $snapshot->uuid = $entity->getUuid();

        return $snapshot;
    }

    /**
     *
     * @param NmtProcurePoRow $entity
     * @return NULL|\Procure\Domain\PurchaseOrder\PORowDetailsSnapshot
     */
    private function createRowDetailSnapshot(NmtProcurePoRow $entity)
    {
        if ($entity == null)
            return null;

        $snapshot = new PORowDetailsSnapshot();

        // Mapping Reference
        // =====================

        // $snapshot->invoice= $entity->getInvoice();
        if ($entity->getInvoice() !== null) {
            $snapshot->invoice = $entity->getInvoice()->getId();
        }

        // $snapshot->lastchangeBy= $entity->getLastchangeBy();
        if ($entity->getLastchangeBy() !== null) {
            $snapshot->lastchangeBy = $entity->getLastchangeBy()->getId();
        }

        // $snapshot->prRow= $entity->getPrRow();
        if ($entity->getPrRow() !== null) {
            $snapshot->prRow = $entity->getPrRow()->getId();

            $snapshot->prRowIndentifer = $entity->getPrRow()->getRowIdentifer();
            $snapshot->prRowCode = $entity->getPrRow()->getRowCode();
            $snapshot->prRowName = $entity->getPrRow()->getRowName();
            $snapshot->prRowConvertFactor = $entity->getPrRow()->getConversionFactor();
            $snapshot->prRowUnit = $entity->getPrRow()->getRowUnit();

            if ($entity->getPrRow()->getPr() !== null) {
                $snapshot->pr = $entity->getPrRow()
                ->getPr()
                ->getId();
                
                $snapshot->prSysNumber = $entity->getPrRow()
                    ->getPr()
                    ->getPrAutoNumber();

                $snapshot->prNumber = $entity->getPrRow()
                    ->getPr()
                    ->getPrNumber();

                $snapshot->prToken = $entity->getPrRow()
                    ->getPr()
                    ->getToken();

                $snapshot->prChecksum = $entity->getPrRow()
                    ->getPr()
                    ->getChecksum();
            }
        }

        // $snapshot->createdBy= $entity->getCreatedBy();
        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
        }

        // $snapshot->warehouse= $entity->getWarehouse();
        if ($entity->getWarehouse() !== null) {
            $snapshot->warehouse = $entity->getWarehouse()->getId();
        }

        // $snapshot->po= $entity->getPo();
        if ($entity->getPo() !== null) {
            $snapshot->po = $entity->getPo()->getId();
        }

        // $snapshot->item= $entity->getItem();
        if ($entity->getItem() !== null) {
            $snapshot->item = $entity->getItem()->getId();

            $snapshot->itemToken = $entity->getItem()->getToken();
            $snapshot->itemCheckSum = $entity->getItem()->getChecksum();

            $snapshot->itemName = $entity->getItem()->getItemName();
            $snapshot->itemName1 = $entity->getItem()->getItemNameForeign();

            $snapshot->itemSKU = $entity->getItem()->getItemSku();

            $snapshot->itemSKU1 = $entity->getItem()->getItemSku1();
            $snapshot->itemSKU2 = $entity->getItem()->getItemSku2();

            $snapshot->itemVersion = $entity->getItem()->getRevisionNo();

            if ($entity->getItem()->getStandardUom() != null) {
                $snapshot->itemStandardUnit = $entity->getItem()
                    ->getStandardUom()
                    ->getId();
                $snapshot->itemStandardUnitName = $entity->getItem()
                    ->getStandardUom()
                    ->getUomCode();
            }
        }

        $snapshot->docUom = $entity->getDocUom();
        if ($entity->getDocUom() !== null) {
            $snapshot->docUom = $entity->getDocUom()->getId();
        }

        // Mapping Date
        // =====================

        $snapshot->createdOn = $entity->getCreatedOn();
        $snapshot->lastchangeOn = $entity->getLastchangeOn();

        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d");
        }

        if (! $entity->getLastChangeOn() == null) {
            $snapshot->lastChangeOn = $entity->getLastChangeOn()->format("Y-m-d");
        }

        // Mapping None-Object Field
        // =====================

        $snapshot->id = $entity->getId();
        $snapshot->rowNumber = $entity->getRowNumber();
        $snapshot->token = $entity->getToken();
        $snapshot->quantity = $entity->getQuantity();
        $snapshot->unitPrice = $entity->getUnitPrice();
        $snapshot->netAmount = $entity->getNetAmount();
        $snapshot->unit = $entity->getUnit();
        $snapshot->itemUnit = $entity->getItemUnit();
        $snapshot->conversionFactor = $entity->getConversionFactor();
        $snapshot->converstionText = $entity->getConverstionText();
        $snapshot->taxRate = $entity->getTaxRate();
        $snapshot->remarks = $entity->getRemarks();
        $snapshot->isActive = $entity->getIsActive();
        $snapshot->currentState = $entity->getCurrentState();
        $snapshot->vendorItemCode = $entity->getVendorItemCode();
        $snapshot->traceStock = $entity->getTraceStock();
        $snapshot->grossAmount = $entity->getGrossAmount();
        $snapshot->taxAmount = $entity->getTaxAmount();
        $snapshot->faRemarks = $entity->getFaRemarks();
        $snapshot->rowIdentifer = $entity->getRowIdentifer();
        $snapshot->discountRate = $entity->getDiscountRate();
        $snapshot->revisionNo = $entity->getRevisionNo();
        $snapshot->targetObject = $entity->getTargetObject();
        $snapshot->sourceObject = $entity->getSourceObject();
        $snapshot->targetObjectId = $entity->getTargetObjectId();
        $snapshot->sourceObjectId = $entity->getSourceObjectId();
        $snapshot->docStatus = $entity->getDocStatus();
        $snapshot->workflowStatus = $entity->getWorkflowStatus();
        $snapshot->transactionStatus = $entity->getTransactionStatus();
        $snapshot->isPosted = $entity->getIsPosted();
        $snapshot->isDraft = $entity->getIsDraft();
        $snapshot->exwUnitPrice = $entity->getExwUnitPrice();
        $snapshot->totalExwPrice = $entity->getTotalExwPrice();
        $snapshot->convertFactorPurchase = $entity->getConvertFactorPurchase();
        $snapshot->convertedPurchaseQuantity = $entity->getConvertedPurchaseQuantity();
        $snapshot->convertedStandardQuantity = $entity->getConvertedStandardQuantity();
        $snapshot->convertedStockQuantity = $entity->getConvertedStockQuantity();
        $snapshot->convertedStandardUnitPrice = $entity->getConvertedStandardUnitPrice();
        $snapshot->convertedStockUnitPrice = $entity->getConvertedStockUnitPrice();
        $snapshot->docQuantity = $entity->getDocQuantity();
        $snapshot->docUnit = $entity->getDocUnit();
        $snapshot->docUnitPrice = $entity->getDocUnitPrice();
        $snapshot->convertedPurchaseUnitPrice = $entity->getConvertedPurchaseUnitPrice();
        $snapshot->docType = $entity->getDocType();
        $snapshot->descriptionText = $entity->getDescriptionText();
        $snapshot->vendorItemName = $entity->getVendorItemName();
        $snapshot->reversalBlocked = $entity->getReversalBlocked();

        return $snapshot;
    }

    /**
     *
     * @param int $id
     * @return array|mixed|\Doctrine\DBAL\Driver\Statement|NULL|NULL
     */
    private function getPoRowsDetails($id)
    {
        $sql1 = "
SELECT
    nmt_procure_po_row.id AS po_row_id,
	IFNULL(SUM(CASE WHEN fin_vendor_invoice_row.is_draft=1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END),0) AS draft_ap_qty,
    IFNULL(SUM(CASE WHEN fin_vendor_invoice_row.is_draft=0 AND fin_vendor_invoice_row.is_posted=1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END),0) AS posted_ap_qty,
    IFNULL(nmt_procure_po_row.quantity-SUM(CASE WHEN fin_vendor_invoice_row.is_draft=0 AND fin_vendor_invoice_row.is_posted=1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END),0) AS confirmed_ap_balance,
    nmt_procure_po_row.quantity-SUM(CASE WHEN fin_vendor_invoice_row.is_draft=1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END)-SUM(CASE WHEN fin_vendor_invoice_row.is_draft=0 AND fin_vendor_invoice_row.is_posted=1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END) AS open_ap_qty,
    ifnull(SUM(CASE WHEN fin_vendor_invoice_row.is_posted=1 THEN  fin_vendor_invoice_row.net_amount ELSE 0 END),0)AS billed_amount
            
FROM nmt_procure_po_row
            
LEFT JOIN fin_vendor_invoice_row
ON fin_vendor_invoice_row.po_row_id =  nmt_procure_po_row.id
            
WHERE nmt_procure_po_row.po_id=%s AND nmt_procure_po_row.is_active=1
GROUP BY nmt_procure_po_row.id
";

        $sql2 = "
SELECT
            
	IFNULL(SUM(CASE WHEN nmt_procure_gr_row.is_draft=1 THEN  nmt_procure_gr_row.quantity ELSE 0 END),0) AS draft_gr_qty,
    IFNULL(SUM(CASE WHEN nmt_procure_gr_row.is_draft=0 AND nmt_procure_gr_row.is_posted=1 THEN  nmt_procure_gr_row.quantity ELSE 0 END),0) AS posted_gr_qty,
    IFNULL(nmt_procure_po_row.quantity-SUM(CASE WHEN nmt_procure_gr_row.is_draft=0 AND nmt_procure_gr_row.is_posted=1 THEN  nmt_procure_gr_row.quantity ELSE 0 END),0) AS confirmed_gr_balance,
    nmt_procure_po_row.quantity-SUM(CASE WHEN nmt_procure_gr_row.is_draft=1 THEN  nmt_procure_gr_row.quantity ELSE 0 END)-SUM(CASE WHEN nmt_procure_gr_row.is_draft=0 AND nmt_procure_gr_row.is_posted=1 THEN  nmt_procure_gr_row.quantity ELSE 0 END) AS open_gr_qty,
    nmt_procure_po_row.id as po_row_id
            
FROM nmt_procure_po_row
            
LEFT JOIN nmt_procure_gr_row
ON nmt_procure_gr_row.po_row_id =  nmt_procure_po_row.id
            
WHERE nmt_procure_po_row.po_id=%s AND nmt_procure_po_row.is_active=1
GROUP BY nmt_procure_po_row.id
";

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

        $sql1 = sprintf($sql1, $id);
        $sql2 = sprintf($sql2, $id);

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
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }
}
