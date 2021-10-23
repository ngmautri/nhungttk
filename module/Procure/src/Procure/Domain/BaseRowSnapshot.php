<?php
namespace Procure\Domain;

use Application\Domain\Shared\AbstractDTO;

/**
 * Row Snapshot
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class BaseRowSnapshot extends AbstractDTO
{

    /*
     * |=============================
     * | Procure\Domain\GenericRow
     * |
     * |=============================
     */
    public $docUomVO;

    public $itemStandardUomVO;

    public $uomPairVO;

    public $docQuantityVO;

    public $itemStandardQuantityVO;

    public $docCurrencyVO;

    public $localCurrencyVO;

    public $currencyPair;

    public $docUnitPriceVO;

    public $docItemStandardUnitPriceVO;

    public $docNetAmountVO;

    public $docTaxAmountVO;

    public $docGrossAmountVO;

    public $localUnitPriceVO;

    public $localItemStandardUnitPriceVO;

    public $LocalNetAmountVO;

    public $localTaxAmountVO;

    public $localGrossAmountVO;

    public $exculdedProps;

    /*
     * |=============================
     * | Procure\Domain\BaseRow
     * |
     * |=============================
     */
    public $qoQuantity;

    public $standardQoQuantity;

    public $postedQoQuantity;

    public $postedStandardQoQuantity;

    public $draftPoQuantity;

    public $standardPoQuantity;

    public $postedPoQuantity;

    public $postedStandardPoQuantity;

    public $draftGrQuantity;

    public $standardGrQuantity;

    public $postedGrQuantity;

    public $postedStandardGrQuantity;

    public $draftReturnQuantity;

    public $standardReturnQuantity;

    public $postedReturnQuantity;

    public $postedStandardReturnQuantity;

    public $draftStockQrQuantity;

    public $standardStockQrQuantity;

    public $postedStockQrQuantity;

    public $postedStandardStockQrQuantity;

    public $draftStockReturnQuantity;

    public $standardStockReturnQuantity;

    public $postedStockReturnQuantity;

    public $postedStandardStockReturnQuantity;

    public $draftApQuantity;

    public $standardApQuantity;

    public $postedApQuantity;

    public $postedStandardApQuantity;

    public $constructedFromDB;

    public $standardQuantity;

    public $standardUnitPriceInDocCurrency;

    public $standardUnitPriceInLocCurrency;

    public $docQuantityObject;

    public $baseUomPair;

    public $docUnitPriceObject;

    public $baseDocUnitPriceObject;

    public $localUnitPriceObject;

    public $baseLocalUnitPriceObject;

    public $localStandardUnitPrice;

    public $createdByName;

    public $lastChangeByName;

    public $glAccountName;

    public $glAccountNumber;

    public $glAccountType;

    public $costCenterName;

    public $discountAmount;

    public $convertedDocQuantity;

    public $convertedDocUnitPrice;

    public $companyId;

    public $companyToken;

    public $companyName;

    public $vendorId;

    public $vendorToken;

    public $vendorName;

    public $vendorCountry;

    public $docNumber;

    public $docSysNumber;

    public $docCurrencyISO;

    public $localCurrencyISO;

    public $docCurrencyId;

    public $localCurrencyId;

    public $docToken;

    public $docId;

    public $exchangeRate;

    public $docDate;

    public $docYear;

    public $docMonth;

    public $docRevisionNo;

    public $docWarehouseName;

    public $docWarehouseCode;

    public $warehouseName;

    public $warehouseCode;

    public $docUomName;

    public $docUomCode;

    public $docUomDescription;

    public $itemToken;

    public $itemChecksum;

    public $itemName;

    public $itemName1;

    public $itemSKU;

    public $itemSKU1;

    public $itemSKU2;

    public $itemUUID;

    public $itemSysNumber;

    public $itemStandardUnit;

    public $itemStandardUnitName;

    public $itemStandardUnitCode;

    public $itemVersion;

    public $isInventoryItem;

    public $isFixedAsset;

    public $itemMonitorMethod;

    public $itemModel;

    public $itemSerial;

    public $itemDefaultManufacturer;

    public $itemManufacturerModel;

    public $itemManufacturerSerial;

    public $itemManufacturerCode;

    public $itemKeywords;

    public $itemAssetLabel;

    public $itemAssetLabel1;

    public $itemDescription;

    public $itemInventoryGL;

    public $itemCogsGL;

    public $itemCostCenter;

    public $pr;

    public $prToken;

    public $prChecksum;

    public $prNumber;

    public $prSysNumber;

    public $prRowIndentifer;

    public $prRowCode;

    public $prRowName;

    public $prRowConvertFactor;

    public $prRowUnit;

    public $prRowVersion;

    public $projectId;

    public $projectToken;

    public $projectName;

    public $prDepartment;

    public $prDepartmentName;

    public $prWarehouse;

    public $prWarehouseName;

    public $prDocId;

    public $prDocToken;

    public $grDocId;

    public $grDocToken;

    public $grSysNumber;

    public $apDocId;

    public $apDocToken;

    public $apSysNumber;

    /*
     * |=============================
     * | Procure\Domain\AbstractRow
     * |
     * |=============================
     */
    public $id;

    public $rowNumber;

    public $token;

    public $quantity;

    public $unitPrice;

    public $netAmount;

    public $unit;

    public $itemUnit;

    public $conversionFactor;

    public $converstionText;

    public $taxRate;

    public $remarks;

    public $isActive;

    public $createdOn;

    public $lastchangeOn;

    public $currentState;

    public $vendorItemCode;

    public $traceStock;

    public $grossAmount;

    public $taxAmount;

    public $faRemarks;

    public $rowIdentifer;

    public $discountRate;

    public $revisionNo;

    public $targetObject;

    public $sourceObject;

    public $targetObjectId;

    public $sourceObjectId;

    public $docStatus;

    public $workflowStatus;

    public $transactionStatus;

    public $isPosted;

    public $isDraft;

    public $exwUnitPrice;

    public $totalExwPrice;

    public $convertFactorPurchase;

    public $convertedPurchaseQuantity;

    public $convertedStandardQuantity;

    public $convertedStockQuantity;

    public $convertedStandardUnitPrice;

    public $convertedStockUnitPrice;

    public $docQuantity;

    public $docUnit;

    public $docUnitPrice;

    public $convertedPurchaseUnitPrice;

    public $docType;

    public $descriptionText;

    public $vendorItemName;

    public $reversalBlocked;

    public $invoice;

    public $lastchangeBy;

    public $prRow;

    public $createdBy;

    public $warehouse;

    public $po;

    public $item;

    public $docUom;

    public $docVersion;

    public $uuid;

    public $localUnitPrice;

    public $exwCurrency;

    public $localNetAmount;

    public $localGrossAmount;

    public $transactionType;

    public $isReversed;

    public $reversalDate;

    public $glAccount;

    public $costCenter;

    public $standardConvertFactor;

    public $clearingDocId;

    public $brand;

    public $variantId;

    public $project;

    /**
     *
     * @return mixed
     */
    public function getDocUomVO()
    {
        return $this->docUomVO;
    }

    /**
     *
     * @param mixed $docUomVO
     */
    public function setDocUomVO($docUomVO)
    {
        $this->docUomVO = $docUomVO;
    }

    /**
     *
     * @return mixed
     */
    public function getItemStandardUomVO()
    {
        return $this->itemStandardUomVO;
    }

    /**
     *
     * @param mixed $itemStandardUomVO
     */
    public function setItemStandardUomVO($itemStandardUomVO)
    {
        $this->itemStandardUomVO = $itemStandardUomVO;
    }

    /**
     *
     * @return mixed
     */
    public function getUomPairVO()
    {
        return $this->uomPairVO;
    }

    /**
     *
     * @param mixed $uomPairVO
     */
    public function setUomPairVO($uomPairVO)
    {
        $this->uomPairVO = $uomPairVO;
    }

    /**
     *
     * @return mixed
     */
    public function getDocQuantityVO()
    {
        return $this->docQuantityVO;
    }

    /**
     *
     * @param mixed $docQuantityVO
     */
    public function setDocQuantityVO($docQuantityVO)
    {
        $this->docQuantityVO = $docQuantityVO;
    }

    /**
     *
     * @return mixed
     */
    public function getItemStandardQuantityVO()
    {
        return $this->itemStandardQuantityVO;
    }

    /**
     *
     * @param mixed $itemStandardQuantityVO
     */
    public function setItemStandardQuantityVO($itemStandardQuantityVO)
    {
        $this->itemStandardQuantityVO = $itemStandardQuantityVO;
    }

    /**
     *
     * @return mixed
     */
    public function getDocCurrencyVO()
    {
        return $this->docCurrencyVO;
    }

    /**
     *
     * @param mixed $docCurrencyVO
     */
    public function setDocCurrencyVO($docCurrencyVO)
    {
        $this->docCurrencyVO = $docCurrencyVO;
    }

    /**
     *
     * @return mixed
     */
    public function getLocalCurrencyVO()
    {
        return $this->localCurrencyVO;
    }

    /**
     *
     * @param mixed $localCurrencyVO
     */
    public function setLocalCurrencyVO($localCurrencyVO)
    {
        $this->localCurrencyVO = $localCurrencyVO;
    }

    /**
     *
     * @return mixed
     */
    public function getCurrencyPair()
    {
        return $this->currencyPair;
    }

    /**
     *
     * @param mixed $currencyPair
     */
    public function setCurrencyPair($currencyPair)
    {
        $this->currencyPair = $currencyPair;
    }

    /**
     *
     * @return mixed
     */
    public function getDocUnitPriceVO()
    {
        return $this->docUnitPriceVO;
    }

    /**
     *
     * @param mixed $docUnitPriceVO
     */
    public function setDocUnitPriceVO($docUnitPriceVO)
    {
        $this->docUnitPriceVO = $docUnitPriceVO;
    }

    /**
     *
     * @return mixed
     */
    public function getDocItemStandardUnitPriceVO()
    {
        return $this->docItemStandardUnitPriceVO;
    }

    /**
     *
     * @param mixed $docItemStandardUnitPriceVO
     */
    public function setDocItemStandardUnitPriceVO($docItemStandardUnitPriceVO)
    {
        $this->docItemStandardUnitPriceVO = $docItemStandardUnitPriceVO;
    }

    /**
     *
     * @return mixed
     */
    public function getDocNetAmountVO()
    {
        return $this->docNetAmountVO;
    }

    /**
     *
     * @param mixed $docNetAmountVO
     */
    public function setDocNetAmountVO($docNetAmountVO)
    {
        $this->docNetAmountVO = $docNetAmountVO;
    }

    /**
     *
     * @return mixed
     */
    public function getDocTaxAmountVO()
    {
        return $this->docTaxAmountVO;
    }

    /**
     *
     * @param mixed $docTaxAmountVO
     */
    public function setDocTaxAmountVO($docTaxAmountVO)
    {
        $this->docTaxAmountVO = $docTaxAmountVO;
    }

    /**
     *
     * @return mixed
     */
    public function getDocGrossAmountVO()
    {
        return $this->docGrossAmountVO;
    }

    /**
     *
     * @param mixed $docGrossAmountVO
     */
    public function setDocGrossAmountVO($docGrossAmountVO)
    {
        $this->docGrossAmountVO = $docGrossAmountVO;
    }

    /**
     *
     * @return mixed
     */
    public function getLocalUnitPriceVO()
    {
        return $this->localUnitPriceVO;
    }

    /**
     *
     * @param mixed $localUnitPriceVO
     */
    public function setLocalUnitPriceVO($localUnitPriceVO)
    {
        $this->localUnitPriceVO = $localUnitPriceVO;
    }

    /**
     *
     * @return mixed
     */
    public function getLocalItemStandardUnitPriceVO()
    {
        return $this->localItemStandardUnitPriceVO;
    }

    /**
     *
     * @param mixed $localItemStandardUnitPriceVO
     */
    public function setLocalItemStandardUnitPriceVO($localItemStandardUnitPriceVO)
    {
        $this->localItemStandardUnitPriceVO = $localItemStandardUnitPriceVO;
    }

    /**
     *
     * @return mixed
     */
    public function getLocalNetAmountVO()
    {
        return $this->LocalNetAmountVO;
    }

    /**
     *
     * @param mixed $LocalNetAmountVO
     */
    public function setLocalNetAmountVO($LocalNetAmountVO)
    {
        $this->LocalNetAmountVO = $LocalNetAmountVO;
    }

    /**
     *
     * @return mixed
     */
    public function getLocalTaxAmountVO()
    {
        return $this->localTaxAmountVO;
    }

    /**
     *
     * @param mixed $localTaxAmountVO
     */
    public function setLocalTaxAmountVO($localTaxAmountVO)
    {
        $this->localTaxAmountVO = $localTaxAmountVO;
    }

    /**
     *
     * @return mixed
     */
    public function getLocalGrossAmountVO()
    {
        return $this->localGrossAmountVO;
    }

    /**
     *
     * @param mixed $localGrossAmountVO
     */
    public function setLocalGrossAmountVO($localGrossAmountVO)
    {
        $this->localGrossAmountVO = $localGrossAmountVO;
    }

    /**
     *
     * @return mixed
     */
    public function getExculdedProps()
    {
        return $this->exculdedProps;
    }

    /**
     *
     * @param mixed $exculdedProps
     */
    public function setExculdedProps($exculdedProps)
    {
        $this->exculdedProps = $exculdedProps;
    }

    /**
     *
     * @return mixed
     */
    public function getQoQuantity()
    {
        return $this->qoQuantity;
    }

    /**
     *
     * @param mixed $qoQuantity
     */
    public function setQoQuantity($qoQuantity)
    {
        $this->qoQuantity = $qoQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getStandardQoQuantity()
    {
        return $this->standardQoQuantity;
    }

    /**
     *
     * @param mixed $standardQoQuantity
     */
    public function setStandardQoQuantity($standardQoQuantity)
    {
        $this->standardQoQuantity = $standardQoQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedQoQuantity()
    {
        return $this->postedQoQuantity;
    }

    /**
     *
     * @param mixed $postedQoQuantity
     */
    public function setPostedQoQuantity($postedQoQuantity)
    {
        $this->postedQoQuantity = $postedQoQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedStandardQoQuantity()
    {
        return $this->postedStandardQoQuantity;
    }

    /**
     *
     * @param mixed $postedStandardQoQuantity
     */
    public function setPostedStandardQoQuantity($postedStandardQoQuantity)
    {
        $this->postedStandardQoQuantity = $postedStandardQoQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getDraftPoQuantity()
    {
        return $this->draftPoQuantity;
    }

    /**
     *
     * @param mixed $draftPoQuantity
     */
    public function setDraftPoQuantity($draftPoQuantity)
    {
        $this->draftPoQuantity = $draftPoQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getStandardPoQuantity()
    {
        return $this->standardPoQuantity;
    }

    /**
     *
     * @param mixed $standardPoQuantity
     */
    public function setStandardPoQuantity($standardPoQuantity)
    {
        $this->standardPoQuantity = $standardPoQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedPoQuantity()
    {
        return $this->postedPoQuantity;
    }

    /**
     *
     * @param mixed $postedPoQuantity
     */
    public function setPostedPoQuantity($postedPoQuantity)
    {
        $this->postedPoQuantity = $postedPoQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedStandardPoQuantity()
    {
        return $this->postedStandardPoQuantity;
    }

    /**
     *
     * @param mixed $postedStandardPoQuantity
     */
    public function setPostedStandardPoQuantity($postedStandardPoQuantity)
    {
        $this->postedStandardPoQuantity = $postedStandardPoQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getDraftGrQuantity()
    {
        return $this->draftGrQuantity;
    }

    /**
     *
     * @param mixed $draftGrQuantity
     */
    public function setDraftGrQuantity($draftGrQuantity)
    {
        $this->draftGrQuantity = $draftGrQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getStandardGrQuantity()
    {
        return $this->standardGrQuantity;
    }

    /**
     *
     * @param mixed $standardGrQuantity
     */
    public function setStandardGrQuantity($standardGrQuantity)
    {
        $this->standardGrQuantity = $standardGrQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedGrQuantity()
    {
        return $this->postedGrQuantity;
    }

    /**
     *
     * @param mixed $postedGrQuantity
     */
    public function setPostedGrQuantity($postedGrQuantity)
    {
        $this->postedGrQuantity = $postedGrQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedStandardGrQuantity()
    {
        return $this->postedStandardGrQuantity;
    }

    /**
     *
     * @param mixed $postedStandardGrQuantity
     */
    public function setPostedStandardGrQuantity($postedStandardGrQuantity)
    {
        $this->postedStandardGrQuantity = $postedStandardGrQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getDraftReturnQuantity()
    {
        return $this->draftReturnQuantity;
    }

    /**
     *
     * @param mixed $draftReturnQuantity
     */
    public function setDraftReturnQuantity($draftReturnQuantity)
    {
        $this->draftReturnQuantity = $draftReturnQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getStandardReturnQuantity()
    {
        return $this->standardReturnQuantity;
    }

    /**
     *
     * @param mixed $standardReturnQuantity
     */
    public function setStandardReturnQuantity($standardReturnQuantity)
    {
        $this->standardReturnQuantity = $standardReturnQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedReturnQuantity()
    {
        return $this->postedReturnQuantity;
    }

    /**
     *
     * @param mixed $postedReturnQuantity
     */
    public function setPostedReturnQuantity($postedReturnQuantity)
    {
        $this->postedReturnQuantity = $postedReturnQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedStandardReturnQuantity()
    {
        return $this->postedStandardReturnQuantity;
    }

    /**
     *
     * @param mixed $postedStandardReturnQuantity
     */
    public function setPostedStandardReturnQuantity($postedStandardReturnQuantity)
    {
        $this->postedStandardReturnQuantity = $postedStandardReturnQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getDraftStockQrQuantity()
    {
        return $this->draftStockQrQuantity;
    }

    /**
     *
     * @param mixed $draftStockQrQuantity
     */
    public function setDraftStockQrQuantity($draftStockQrQuantity)
    {
        $this->draftStockQrQuantity = $draftStockQrQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getStandardStockQrQuantity()
    {
        return $this->standardStockQrQuantity;
    }

    /**
     *
     * @param mixed $standardStockQrQuantity
     */
    public function setStandardStockQrQuantity($standardStockQrQuantity)
    {
        $this->standardStockQrQuantity = $standardStockQrQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedStockQrQuantity()
    {
        return $this->postedStockQrQuantity;
    }

    /**
     *
     * @param mixed $postedStockQrQuantity
     */
    public function setPostedStockQrQuantity($postedStockQrQuantity)
    {
        $this->postedStockQrQuantity = $postedStockQrQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedStandardStockQrQuantity()
    {
        return $this->postedStandardStockQrQuantity;
    }

    /**
     *
     * @param mixed $postedStandardStockQrQuantity
     */
    public function setPostedStandardStockQrQuantity($postedStandardStockQrQuantity)
    {
        $this->postedStandardStockQrQuantity = $postedStandardStockQrQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getDraftStockReturnQuantity()
    {
        return $this->draftStockReturnQuantity;
    }

    /**
     *
     * @param mixed $draftStockReturnQuantity
     */
    public function setDraftStockReturnQuantity($draftStockReturnQuantity)
    {
        $this->draftStockReturnQuantity = $draftStockReturnQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getStandardStockReturnQuantity()
    {
        return $this->standardStockReturnQuantity;
    }

    /**
     *
     * @param mixed $standardStockReturnQuantity
     */
    public function setStandardStockReturnQuantity($standardStockReturnQuantity)
    {
        $this->standardStockReturnQuantity = $standardStockReturnQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedStockReturnQuantity()
    {
        return $this->postedStockReturnQuantity;
    }

    /**
     *
     * @param mixed $postedStockReturnQuantity
     */
    public function setPostedStockReturnQuantity($postedStockReturnQuantity)
    {
        $this->postedStockReturnQuantity = $postedStockReturnQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedStandardStockReturnQuantity()
    {
        return $this->postedStandardStockReturnQuantity;
    }

    /**
     *
     * @param mixed $postedStandardStockReturnQuantity
     */
    public function setPostedStandardStockReturnQuantity($postedStandardStockReturnQuantity)
    {
        $this->postedStandardStockReturnQuantity = $postedStandardStockReturnQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getDraftApQuantity()
    {
        return $this->draftApQuantity;
    }

    /**
     *
     * @param mixed $draftApQuantity
     */
    public function setDraftApQuantity($draftApQuantity)
    {
        $this->draftApQuantity = $draftApQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getStandardApQuantity()
    {
        return $this->standardApQuantity;
    }

    /**
     *
     * @param mixed $standardApQuantity
     */
    public function setStandardApQuantity($standardApQuantity)
    {
        $this->standardApQuantity = $standardApQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedApQuantity()
    {
        return $this->postedApQuantity;
    }

    /**
     *
     * @param mixed $postedApQuantity
     */
    public function setPostedApQuantity($postedApQuantity)
    {
        $this->postedApQuantity = $postedApQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedStandardApQuantity()
    {
        return $this->postedStandardApQuantity;
    }

    /**
     *
     * @param mixed $postedStandardApQuantity
     */
    public function setPostedStandardApQuantity($postedStandardApQuantity)
    {
        $this->postedStandardApQuantity = $postedStandardApQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getConstructedFromDB()
    {
        return $this->constructedFromDB;
    }

    /**
     *
     * @param mixed $constructedFromDB
     */
    public function setConstructedFromDB($constructedFromDB)
    {
        $this->constructedFromDB = $constructedFromDB;
    }

    /**
     *
     * @return mixed
     */
    public function getStandardQuantity()
    {
        return $this->standardQuantity;
    }

    /**
     *
     * @param mixed $standardQuantity
     */
    public function setStandardQuantity($standardQuantity)
    {
        $this->standardQuantity = $standardQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getStandardUnitPriceInDocCurrency()
    {
        return $this->standardUnitPriceInDocCurrency;
    }

    /**
     *
     * @param mixed $standardUnitPriceInDocCurrency
     */
    public function setStandardUnitPriceInDocCurrency($standardUnitPriceInDocCurrency)
    {
        $this->standardUnitPriceInDocCurrency = $standardUnitPriceInDocCurrency;
    }

    /**
     *
     * @return mixed
     */
    public function getStandardUnitPriceInLocCurrency()
    {
        return $this->standardUnitPriceInLocCurrency;
    }

    /**
     *
     * @param mixed $standardUnitPriceInLocCurrency
     */
    public function setStandardUnitPriceInLocCurrency($standardUnitPriceInLocCurrency)
    {
        $this->standardUnitPriceInLocCurrency = $standardUnitPriceInLocCurrency;
    }

    /**
     *
     * @return mixed
     */
    public function getDocQuantityObject()
    {
        return $this->docQuantityObject;
    }

    /**
     *
     * @param mixed $docQuantityObject
     */
    public function setDocQuantityObject($docQuantityObject)
    {
        $this->docQuantityObject = $docQuantityObject;
    }

    /**
     *
     * @return mixed
     */
    public function getBaseUomPair()
    {
        return $this->baseUomPair;
    }

    /**
     *
     * @param mixed $baseUomPair
     */
    public function setBaseUomPair($baseUomPair)
    {
        $this->baseUomPair = $baseUomPair;
    }

    /**
     *
     * @return mixed
     */
    public function getDocUnitPriceObject()
    {
        return $this->docUnitPriceObject;
    }

    /**
     *
     * @param mixed $docUnitPriceObject
     */
    public function setDocUnitPriceObject($docUnitPriceObject)
    {
        $this->docUnitPriceObject = $docUnitPriceObject;
    }

    /**
     *
     * @return mixed
     */
    public function getBaseDocUnitPriceObject()
    {
        return $this->baseDocUnitPriceObject;
    }

    /**
     *
     * @param mixed $baseDocUnitPriceObject
     */
    public function setBaseDocUnitPriceObject($baseDocUnitPriceObject)
    {
        $this->baseDocUnitPriceObject = $baseDocUnitPriceObject;
    }

    /**
     *
     * @return mixed
     */
    public function getLocalUnitPriceObject()
    {
        return $this->localUnitPriceObject;
    }

    /**
     *
     * @param mixed $localUnitPriceObject
     */
    public function setLocalUnitPriceObject($localUnitPriceObject)
    {
        $this->localUnitPriceObject = $localUnitPriceObject;
    }

    /**
     *
     * @return mixed
     */
    public function getBaseLocalUnitPriceObject()
    {
        return $this->baseLocalUnitPriceObject;
    }

    /**
     *
     * @param mixed $baseLocalUnitPriceObject
     */
    public function setBaseLocalUnitPriceObject($baseLocalUnitPriceObject)
    {
        $this->baseLocalUnitPriceObject = $baseLocalUnitPriceObject;
    }

    /**
     *
     * @return mixed
     */
    public function getLocalStandardUnitPrice()
    {
        return $this->localStandardUnitPrice;
    }

    /**
     *
     * @param mixed $localStandardUnitPrice
     */
    public function setLocalStandardUnitPrice($localStandardUnitPrice)
    {
        $this->localStandardUnitPrice = $localStandardUnitPrice;
    }

    /**
     *
     * @return mixed
     */
    public function getCreatedByName()
    {
        return $this->createdByName;
    }

    /**
     *
     * @param mixed $createdByName
     */
    public function setCreatedByName($createdByName)
    {
        $this->createdByName = $createdByName;
    }

    /**
     *
     * @return mixed
     */
    public function getLastChangeByName()
    {
        return $this->lastChangeByName;
    }

    /**
     *
     * @param mixed $lastChangeByName
     */
    public function setLastChangeByName($lastChangeByName)
    {
        $this->lastChangeByName = $lastChangeByName;
    }

    /**
     *
     * @return mixed
     */
    public function getGlAccountName()
    {
        return $this->glAccountName;
    }

    /**
     *
     * @param mixed $glAccountName
     */
    public function setGlAccountName($glAccountName)
    {
        $this->glAccountName = $glAccountName;
    }

    /**
     *
     * @return mixed
     */
    public function getGlAccountNumber()
    {
        return $this->glAccountNumber;
    }

    /**
     *
     * @param mixed $glAccountNumber
     */
    public function setGlAccountNumber($glAccountNumber)
    {
        $this->glAccountNumber = $glAccountNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getGlAccountType()
    {
        return $this->glAccountType;
    }

    /**
     *
     * @param mixed $glAccountType
     */
    public function setGlAccountType($glAccountType)
    {
        $this->glAccountType = $glAccountType;
    }

    /**
     *
     * @return mixed
     */
    public function getCostCenterName()
    {
        return $this->costCenterName;
    }

    /**
     *
     * @param mixed $costCenterName
     */
    public function setCostCenterName($costCenterName)
    {
        $this->costCenterName = $costCenterName;
    }

    /**
     *
     * @return mixed
     */
    public function getDiscountAmount()
    {
        return $this->discountAmount;
    }

    /**
     *
     * @param mixed $discountAmount
     */
    public function setDiscountAmount($discountAmount)
    {
        $this->discountAmount = $discountAmount;
    }

    /**
     *
     * @return mixed
     */
    public function getConvertedDocQuantity()
    {
        return $this->convertedDocQuantity;
    }

    /**
     *
     * @param mixed $convertedDocQuantity
     */
    public function setConvertedDocQuantity($convertedDocQuantity)
    {
        $this->convertedDocQuantity = $convertedDocQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getConvertedDocUnitPrice()
    {
        return $this->convertedDocUnitPrice;
    }

    /**
     *
     * @param mixed $convertedDocUnitPrice
     */
    public function setConvertedDocUnitPrice($convertedDocUnitPrice)
    {
        $this->convertedDocUnitPrice = $convertedDocUnitPrice;
    }

    /**
     *
     * @return mixed
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     *
     * @param mixed $companyId
     */
    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;
    }

    /**
     *
     * @return mixed
     */
    public function getCompanyToken()
    {
        return $this->companyToken;
    }

    /**
     *
     * @param mixed $companyToken
     */
    public function setCompanyToken($companyToken)
    {
        $this->companyToken = $companyToken;
    }

    /**
     *
     * @return mixed
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     *
     * @param mixed $companyName
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;
    }

    /**
     *
     * @return mixed
     */
    public function getVendorId()
    {
        return $this->vendorId;
    }

    /**
     *
     * @param mixed $vendorId
     */
    public function setVendorId($vendorId)
    {
        $this->vendorId = $vendorId;
    }

    /**
     *
     * @return mixed
     */
    public function getVendorToken()
    {
        return $this->vendorToken;
    }

    /**
     *
     * @param mixed $vendorToken
     */
    public function setVendorToken($vendorToken)
    {
        $this->vendorToken = $vendorToken;
    }

    /**
     *
     * @return mixed
     */
    public function getVendorName()
    {
        return $this->vendorName;
    }

    /**
     *
     * @param mixed $vendorName
     */
    public function setVendorName($vendorName)
    {
        $this->vendorName = $vendorName;
    }

    /**
     *
     * @return mixed
     */
    public function getVendorCountry()
    {
        return $this->vendorCountry;
    }

    /**
     *
     * @param mixed $vendorCountry
     */
    public function setVendorCountry($vendorCountry)
    {
        $this->vendorCountry = $vendorCountry;
    }

    /**
     *
     * @return mixed
     */
    public function getDocNumber()
    {
        return $this->docNumber;
    }

    /**
     *
     * @param mixed $docNumber
     */
    public function setDocNumber($docNumber)
    {
        $this->docNumber = $docNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getDocSysNumber()
    {
        return $this->docSysNumber;
    }

    /**
     *
     * @param mixed $docSysNumber
     */
    public function setDocSysNumber($docSysNumber)
    {
        $this->docSysNumber = $docSysNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getDocCurrencyISO()
    {
        return $this->docCurrencyISO;
    }

    /**
     *
     * @param mixed $docCurrencyISO
     */
    public function setDocCurrencyISO($docCurrencyISO)
    {
        $this->docCurrencyISO = $docCurrencyISO;
    }

    /**
     *
     * @return mixed
     */
    public function getLocalCurrencyISO()
    {
        return $this->localCurrencyISO;
    }

    /**
     *
     * @param mixed $localCurrencyISO
     */
    public function setLocalCurrencyISO($localCurrencyISO)
    {
        $this->localCurrencyISO = $localCurrencyISO;
    }

    /**
     *
     * @return mixed
     */
    public function getDocCurrencyId()
    {
        return $this->docCurrencyId;
    }

    /**
     *
     * @param mixed $docCurrencyId
     */
    public function setDocCurrencyId($docCurrencyId)
    {
        $this->docCurrencyId = $docCurrencyId;
    }

    /**
     *
     * @return mixed
     */
    public function getLocalCurrencyId()
    {
        return $this->localCurrencyId;
    }

    /**
     *
     * @param mixed $localCurrencyId
     */
    public function setLocalCurrencyId($localCurrencyId)
    {
        $this->localCurrencyId = $localCurrencyId;
    }

    /**
     *
     * @return mixed
     */
    public function getDocToken()
    {
        return $this->docToken;
    }

    /**
     *
     * @param mixed $docToken
     */
    public function setDocToken($docToken)
    {
        $this->docToken = $docToken;
    }

    /**
     *
     * @return mixed
     */
    public function getDocId()
    {
        return $this->docId;
    }

    /**
     *
     * @param mixed $docId
     */
    public function setDocId($docId)
    {
        $this->docId = $docId;
    }

    /**
     *
     * @return mixed
     */
    public function getExchangeRate()
    {
        return $this->exchangeRate;
    }

    /**
     *
     * @param mixed $exchangeRate
     */
    public function setExchangeRate($exchangeRate)
    {
        $this->exchangeRate = $exchangeRate;
    }

    /**
     *
     * @return mixed
     */
    public function getDocDate()
    {
        return $this->docDate;
    }

    /**
     *
     * @param mixed $docDate
     */
    public function setDocDate($docDate)
    {
        $this->docDate = $docDate;
    }

    /**
     *
     * @return mixed
     */
    public function getDocYear()
    {
        return $this->docYear;
    }

    /**
     *
     * @param mixed $docYear
     */
    public function setDocYear($docYear)
    {
        $this->docYear = $docYear;
    }

    /**
     *
     * @return mixed
     */
    public function getDocMonth()
    {
        return $this->docMonth;
    }

    /**
     *
     * @param mixed $docMonth
     */
    public function setDocMonth($docMonth)
    {
        $this->docMonth = $docMonth;
    }

    /**
     *
     * @return mixed
     */
    public function getDocRevisionNo()
    {
        return $this->docRevisionNo;
    }

    /**
     *
     * @param mixed $docRevisionNo
     */
    public function setDocRevisionNo($docRevisionNo)
    {
        $this->docRevisionNo = $docRevisionNo;
    }

    /**
     *
     * @return mixed
     */
    public function getDocWarehouseName()
    {
        return $this->docWarehouseName;
    }

    /**
     *
     * @param mixed $docWarehouseName
     */
    public function setDocWarehouseName($docWarehouseName)
    {
        $this->docWarehouseName = $docWarehouseName;
    }

    /**
     *
     * @return mixed
     */
    public function getDocWarehouseCode()
    {
        return $this->docWarehouseCode;
    }

    /**
     *
     * @param mixed $docWarehouseCode
     */
    public function setDocWarehouseCode($docWarehouseCode)
    {
        $this->docWarehouseCode = $docWarehouseCode;
    }

    /**
     *
     * @return mixed
     */
    public function getWarehouseName()
    {
        return $this->warehouseName;
    }

    /**
     *
     * @param mixed $warehouseName
     */
    public function setWarehouseName($warehouseName)
    {
        $this->warehouseName = $warehouseName;
    }

    /**
     *
     * @return mixed
     */
    public function getWarehouseCode()
    {
        return $this->warehouseCode;
    }

    /**
     *
     * @param mixed $warehouseCode
     */
    public function setWarehouseCode($warehouseCode)
    {
        $this->warehouseCode = $warehouseCode;
    }

    /**
     *
     * @return mixed
     */
    public function getDocUomName()
    {
        return $this->docUomName;
    }

    /**
     *
     * @param mixed $docUomName
     */
    public function setDocUomName($docUomName)
    {
        $this->docUomName = $docUomName;
    }

    /**
     *
     * @return mixed
     */
    public function getDocUomCode()
    {
        return $this->docUomCode;
    }

    /**
     *
     * @param mixed $docUomCode
     */
    public function setDocUomCode($docUomCode)
    {
        $this->docUomCode = $docUomCode;
    }

    /**
     *
     * @return mixed
     */
    public function getDocUomDescription()
    {
        return $this->docUomDescription;
    }

    /**
     *
     * @param mixed $docUomDescription
     */
    public function setDocUomDescription($docUomDescription)
    {
        $this->docUomDescription = $docUomDescription;
    }

    /**
     *
     * @return mixed
     */
    public function getItemToken()
    {
        return $this->itemToken;
    }

    /**
     *
     * @param mixed $itemToken
     */
    public function setItemToken($itemToken)
    {
        $this->itemToken = $itemToken;
    }

    /**
     *
     * @return mixed
     */
    public function getItemChecksum()
    {
        return $this->itemChecksum;
    }

    /**
     *
     * @param mixed $itemChecksum
     */
    public function setItemChecksum($itemChecksum)
    {
        $this->itemChecksum = $itemChecksum;
    }

    /**
     *
     * @return mixed
     */
    public function getItemName()
    {
        return $this->itemName;
    }

    /**
     *
     * @param mixed $itemName
     */
    public function setItemName($itemName)
    {
        $this->itemName = $itemName;
    }

    /**
     *
     * @return mixed
     */
    public function getItemName1()
    {
        return $this->itemName1;
    }

    /**
     *
     * @param mixed $itemName1
     */
    public function setItemName1($itemName1)
    {
        $this->itemName1 = $itemName1;
    }

    /**
     *
     * @return mixed
     */
    public function getItemSKU()
    {
        return $this->itemSKU;
    }

    /**
     *
     * @param mixed $itemSKU
     */
    public function setItemSKU($itemSKU)
    {
        $this->itemSKU = $itemSKU;
    }

    /**
     *
     * @return mixed
     */
    public function getItemSKU1()
    {
        return $this->itemSKU1;
    }

    /**
     *
     * @param mixed $itemSKU1
     */
    public function setItemSKU1($itemSKU1)
    {
        $this->itemSKU1 = $itemSKU1;
    }

    /**
     *
     * @return mixed
     */
    public function getItemSKU2()
    {
        return $this->itemSKU2;
    }

    /**
     *
     * @param mixed $itemSKU2
     */
    public function setItemSKU2($itemSKU2)
    {
        $this->itemSKU2 = $itemSKU2;
    }

    /**
     *
     * @return mixed
     */
    public function getItemUUID()
    {
        return $this->itemUUID;
    }

    /**
     *
     * @param mixed $itemUUID
     */
    public function setItemUUID($itemUUID)
    {
        $this->itemUUID = $itemUUID;
    }

    /**
     *
     * @return mixed
     */
    public function getItemSysNumber()
    {
        return $this->itemSysNumber;
    }

    /**
     *
     * @param mixed $itemSysNumber
     */
    public function setItemSysNumber($itemSysNumber)
    {
        $this->itemSysNumber = $itemSysNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getItemStandardUnit()
    {
        return $this->itemStandardUnit;
    }

    /**
     *
     * @param mixed $itemStandardUnit
     */
    public function setItemStandardUnit($itemStandardUnit)
    {
        $this->itemStandardUnit = $itemStandardUnit;
    }

    /**
     *
     * @return mixed
     */
    public function getItemStandardUnitName()
    {
        return $this->itemStandardUnitName;
    }

    /**
     *
     * @param mixed $itemStandardUnitName
     */
    public function setItemStandardUnitName($itemStandardUnitName)
    {
        $this->itemStandardUnitName = $itemStandardUnitName;
    }

    /**
     *
     * @return mixed
     */
    public function getItemStandardUnitCode()
    {
        return $this->itemStandardUnitCode;
    }

    /**
     *
     * @param mixed $itemStandardUnitCode
     */
    public function setItemStandardUnitCode($itemStandardUnitCode)
    {
        $this->itemStandardUnitCode = $itemStandardUnitCode;
    }

    /**
     *
     * @return mixed
     */
    public function getItemVersion()
    {
        return $this->itemVersion;
    }

    /**
     *
     * @param mixed $itemVersion
     */
    public function setItemVersion($itemVersion)
    {
        $this->itemVersion = $itemVersion;
    }

    /**
     *
     * @return mixed
     */
    public function getIsInventoryItem()
    {
        return $this->isInventoryItem;
    }

    /**
     *
     * @param mixed $isInventoryItem
     */
    public function setIsInventoryItem($isInventoryItem)
    {
        $this->isInventoryItem = $isInventoryItem;
    }

    /**
     *
     * @return mixed
     */
    public function getIsFixedAsset()
    {
        return $this->isFixedAsset;
    }

    /**
     *
     * @param mixed $isFixedAsset
     */
    public function setIsFixedAsset($isFixedAsset)
    {
        $this->isFixedAsset = $isFixedAsset;
    }

    /**
     *
     * @return mixed
     */
    public function getItemMonitorMethod()
    {
        return $this->itemMonitorMethod;
    }

    /**
     *
     * @param mixed $itemMonitorMethod
     */
    public function setItemMonitorMethod($itemMonitorMethod)
    {
        $this->itemMonitorMethod = $itemMonitorMethod;
    }

    /**
     *
     * @return mixed
     */
    public function getItemModel()
    {
        return $this->itemModel;
    }

    /**
     *
     * @param mixed $itemModel
     */
    public function setItemModel($itemModel)
    {
        $this->itemModel = $itemModel;
    }

    /**
     *
     * @return mixed
     */
    public function getItemSerial()
    {
        return $this->itemSerial;
    }

    /**
     *
     * @param mixed $itemSerial
     */
    public function setItemSerial($itemSerial)
    {
        $this->itemSerial = $itemSerial;
    }

    /**
     *
     * @return mixed
     */
    public function getItemDefaultManufacturer()
    {
        return $this->itemDefaultManufacturer;
    }

    /**
     *
     * @param mixed $itemDefaultManufacturer
     */
    public function setItemDefaultManufacturer($itemDefaultManufacturer)
    {
        $this->itemDefaultManufacturer = $itemDefaultManufacturer;
    }

    /**
     *
     * @return mixed
     */
    public function getItemManufacturerModel()
    {
        return $this->itemManufacturerModel;
    }

    /**
     *
     * @param mixed $itemManufacturerModel
     */
    public function setItemManufacturerModel($itemManufacturerModel)
    {
        $this->itemManufacturerModel = $itemManufacturerModel;
    }

    /**
     *
     * @return mixed
     */
    public function getItemManufacturerSerial()
    {
        return $this->itemManufacturerSerial;
    }

    /**
     *
     * @param mixed $itemManufacturerSerial
     */
    public function setItemManufacturerSerial($itemManufacturerSerial)
    {
        $this->itemManufacturerSerial = $itemManufacturerSerial;
    }

    /**
     *
     * @return mixed
     */
    public function getItemManufacturerCode()
    {
        return $this->itemManufacturerCode;
    }

    /**
     *
     * @param mixed $itemManufacturerCode
     */
    public function setItemManufacturerCode($itemManufacturerCode)
    {
        $this->itemManufacturerCode = $itemManufacturerCode;
    }

    /**
     *
     * @return mixed
     */
    public function getItemKeywords()
    {
        return $this->itemKeywords;
    }

    /**
     *
     * @param mixed $itemKeywords
     */
    public function setItemKeywords($itemKeywords)
    {
        $this->itemKeywords = $itemKeywords;
    }

    /**
     *
     * @return mixed
     */
    public function getItemAssetLabel()
    {
        return $this->itemAssetLabel;
    }

    /**
     *
     * @param mixed $itemAssetLabel
     */
    public function setItemAssetLabel($itemAssetLabel)
    {
        $this->itemAssetLabel = $itemAssetLabel;
    }

    /**
     *
     * @return mixed
     */
    public function getItemAssetLabel1()
    {
        return $this->itemAssetLabel1;
    }

    /**
     *
     * @param mixed $itemAssetLabel1
     */
    public function setItemAssetLabel1($itemAssetLabel1)
    {
        $this->itemAssetLabel1 = $itemAssetLabel1;
    }

    /**
     *
     * @return mixed
     */
    public function getItemDescription()
    {
        return $this->itemDescription;
    }

    /**
     *
     * @param mixed $itemDescription
     */
    public function setItemDescription($itemDescription)
    {
        $this->itemDescription = $itemDescription;
    }

    /**
     *
     * @return mixed
     */
    public function getItemInventoryGL()
    {
        return $this->itemInventoryGL;
    }

    /**
     *
     * @param mixed $itemInventoryGL
     */
    public function setItemInventoryGL($itemInventoryGL)
    {
        $this->itemInventoryGL = $itemInventoryGL;
    }

    /**
     *
     * @return mixed
     */
    public function getItemCogsGL()
    {
        return $this->itemCogsGL;
    }

    /**
     *
     * @param mixed $itemCogsGL
     */
    public function setItemCogsGL($itemCogsGL)
    {
        $this->itemCogsGL = $itemCogsGL;
    }

    /**
     *
     * @return mixed
     */
    public function getItemCostCenter()
    {
        return $this->itemCostCenter;
    }

    /**
     *
     * @param mixed $itemCostCenter
     */
    public function setItemCostCenter($itemCostCenter)
    {
        $this->itemCostCenter = $itemCostCenter;
    }

    /**
     *
     * @return mixed
     */
    public function getPr()
    {
        return $this->pr;
    }

    /**
     *
     * @param mixed $pr
     */
    public function setPr($pr)
    {
        $this->pr = $pr;
    }

    /**
     *
     * @return mixed
     */
    public function getPrToken()
    {
        return $this->prToken;
    }

    /**
     *
     * @param mixed $prToken
     */
    public function setPrToken($prToken)
    {
        $this->prToken = $prToken;
    }

    /**
     *
     * @return mixed
     */
    public function getPrChecksum()
    {
        return $this->prChecksum;
    }

    /**
     *
     * @param mixed $prChecksum
     */
    public function setPrChecksum($prChecksum)
    {
        $this->prChecksum = $prChecksum;
    }

    /**
     *
     * @return mixed
     */
    public function getPrNumber()
    {
        return $this->prNumber;
    }

    /**
     *
     * @param mixed $prNumber
     */
    public function setPrNumber($prNumber)
    {
        $this->prNumber = $prNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getPrSysNumber()
    {
        return $this->prSysNumber;
    }

    /**
     *
     * @param mixed $prSysNumber
     */
    public function setPrSysNumber($prSysNumber)
    {
        $this->prSysNumber = $prSysNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getPrRowIndentifer()
    {
        return $this->prRowIndentifer;
    }

    /**
     *
     * @param mixed $prRowIndentifer
     */
    public function setPrRowIndentifer($prRowIndentifer)
    {
        $this->prRowIndentifer = $prRowIndentifer;
    }

    /**
     *
     * @return mixed
     */
    public function getPrRowCode()
    {
        return $this->prRowCode;
    }

    /**
     *
     * @param mixed $prRowCode
     */
    public function setPrRowCode($prRowCode)
    {
        $this->prRowCode = $prRowCode;
    }

    /**
     *
     * @return mixed
     */
    public function getPrRowName()
    {
        return $this->prRowName;
    }

    /**
     *
     * @param mixed $prRowName
     */
    public function setPrRowName($prRowName)
    {
        $this->prRowName = $prRowName;
    }

    /**
     *
     * @return mixed
     */
    public function getPrRowConvertFactor()
    {
        return $this->prRowConvertFactor;
    }

    /**
     *
     * @param mixed $prRowConvertFactor
     */
    public function setPrRowConvertFactor($prRowConvertFactor)
    {
        $this->prRowConvertFactor = $prRowConvertFactor;
    }

    /**
     *
     * @return mixed
     */
    public function getPrRowUnit()
    {
        return $this->prRowUnit;
    }

    /**
     *
     * @param mixed $prRowUnit
     */
    public function setPrRowUnit($prRowUnit)
    {
        $this->prRowUnit = $prRowUnit;
    }

    /**
     *
     * @return mixed
     */
    public function getPrRowVersion()
    {
        return $this->prRowVersion;
    }

    /**
     *
     * @param mixed $prRowVersion
     */
    public function setPrRowVersion($prRowVersion)
    {
        $this->prRowVersion = $prRowVersion;
    }

    /**
     *
     * @return mixed
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     *
     * @param mixed $projectId
     */
    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;
    }

    /**
     *
     * @return mixed
     */
    public function getProjectToken()
    {
        return $this->projectToken;
    }

    /**
     *
     * @param mixed $projectToken
     */
    public function setProjectToken($projectToken)
    {
        $this->projectToken = $projectToken;
    }

    /**
     *
     * @return mixed
     */
    public function getProjectName()
    {
        return $this->projectName;
    }

    /**
     *
     * @param mixed $projectName
     */
    public function setProjectName($projectName)
    {
        $this->projectName = $projectName;
    }

    /**
     *
     * @return mixed
     */
    public function getPrDepartment()
    {
        return $this->prDepartment;
    }

    /**
     *
     * @param mixed $prDepartment
     */
    public function setPrDepartment($prDepartment)
    {
        $this->prDepartment = $prDepartment;
    }

    /**
     *
     * @return mixed
     */
    public function getPrDepartmentName()
    {
        return $this->prDepartmentName;
    }

    /**
     *
     * @param mixed $prDepartmentName
     */
    public function setPrDepartmentName($prDepartmentName)
    {
        $this->prDepartmentName = $prDepartmentName;
    }

    /**
     *
     * @return mixed
     */
    public function getPrWarehouse()
    {
        return $this->prWarehouse;
    }

    /**
     *
     * @param mixed $prWarehouse
     */
    public function setPrWarehouse($prWarehouse)
    {
        $this->prWarehouse = $prWarehouse;
    }

    /**
     *
     * @return mixed
     */
    public function getPrWarehouseName()
    {
        return $this->prWarehouseName;
    }

    /**
     *
     * @param mixed $prWarehouseName
     */
    public function setPrWarehouseName($prWarehouseName)
    {
        $this->prWarehouseName = $prWarehouseName;
    }

    /**
     *
     * @return mixed
     */
    public function getPrDocId()
    {
        return $this->prDocId;
    }

    /**
     *
     * @param mixed $prDocId
     */
    public function setPrDocId($prDocId)
    {
        $this->prDocId = $prDocId;
    }

    /**
     *
     * @return mixed
     */
    public function getPrDocToken()
    {
        return $this->prDocToken;
    }

    /**
     *
     * @param mixed $prDocToken
     */
    public function setPrDocToken($prDocToken)
    {
        $this->prDocToken = $prDocToken;
    }

    /**
     *
     * @return mixed
     */
    public function getGrDocId()
    {
        return $this->grDocId;
    }

    /**
     *
     * @param mixed $grDocId
     */
    public function setGrDocId($grDocId)
    {
        $this->grDocId = $grDocId;
    }

    /**
     *
     * @return mixed
     */
    public function getGrDocToken()
    {
        return $this->grDocToken;
    }

    /**
     *
     * @param mixed $grDocToken
     */
    public function setGrDocToken($grDocToken)
    {
        $this->grDocToken = $grDocToken;
    }

    /**
     *
     * @return mixed
     */
    public function getGrSysNumber()
    {
        return $this->grSysNumber;
    }

    /**
     *
     * @param mixed $grSysNumber
     */
    public function setGrSysNumber($grSysNumber)
    {
        $this->grSysNumber = $grSysNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getApDocId()
    {
        return $this->apDocId;
    }

    /**
     *
     * @param mixed $apDocId
     */
    public function setApDocId($apDocId)
    {
        $this->apDocId = $apDocId;
    }

    /**
     *
     * @return mixed
     */
    public function getApDocToken()
    {
        return $this->apDocToken;
    }

    /**
     *
     * @param mixed $apDocToken
     */
    public function setApDocToken($apDocToken)
    {
        $this->apDocToken = $apDocToken;
    }

    /**
     *
     * @return mixed
     */
    public function getApSysNumber()
    {
        return $this->apSysNumber;
    }

    /**
     *
     * @param mixed $apSysNumber
     */
    public function setApSysNumber($apSysNumber)
    {
        $this->apSysNumber = $apSysNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return mixed
     */
    public function getRowNumber()
    {
        return $this->rowNumber;
    }

    /**
     *
     * @param mixed $rowNumber
     */
    public function setRowNumber($rowNumber)
    {
        $this->rowNumber = $rowNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     *
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     *
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     *
     * @param mixed $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     *
     * @return mixed
     */
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    /**
     *
     * @param mixed $unitPrice
     */
    public function setUnitPrice($unitPrice)
    {
        $this->unitPrice = $unitPrice;
    }

    /**
     *
     * @return mixed
     */
    public function getNetAmount()
    {
        return $this->netAmount;
    }

    /**
     *
     * @param mixed $netAmount
     */
    public function setNetAmount($netAmount)
    {
        $this->netAmount = $netAmount;
    }

    /**
     *
     * @return mixed
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     *
     * @param mixed $unit
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;
    }

    /**
     *
     * @return mixed
     */
    public function getItemUnit()
    {
        return $this->itemUnit;
    }

    /**
     *
     * @param mixed $itemUnit
     */
    public function setItemUnit($itemUnit)
    {
        $this->itemUnit = $itemUnit;
    }

    /**
     *
     * @return mixed
     */
    public function getConversionFactor()
    {
        return $this->conversionFactor;
    }

    /**
     *
     * @param mixed $conversionFactor
     */
    public function setConversionFactor($conversionFactor)
    {
        $this->conversionFactor = $conversionFactor;
    }

    /**
     *
     * @return mixed
     */
    public function getConverstionText()
    {
        return $this->converstionText;
    }

    /**
     *
     * @param mixed $converstionText
     */
    public function setConverstionText($converstionText)
    {
        $this->converstionText = $converstionText;
    }

    /**
     *
     * @return mixed
     */
    public function getTaxRate()
    {
        return $this->taxRate;
    }

    /**
     *
     * @param mixed $taxRate
     */
    public function setTaxRate($taxRate)
    {
        $this->taxRate = $taxRate;
    }

    /**
     *
     * @return mixed
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     *
     * @param mixed $remarks
     */
    public function setRemarks($remarks)
    {
        $this->remarks = $remarks;
    }

    /**
     *
     * @return mixed
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     *
     * @param mixed $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     *
     * @return mixed
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     *
     * @param mixed $createdOn
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
    }

    /**
     *
     * @return mixed
     */
    public function getLastchangeOn()
    {
        return $this->lastchangeOn;
    }

    /**
     *
     * @param mixed $lastchangeOn
     */
    public function setLastchangeOn($lastchangeOn)
    {
        $this->lastchangeOn = $lastchangeOn;
    }

    /**
     *
     * @return mixed
     */
    public function getCurrentState()
    {
        return $this->currentState;
    }

    /**
     *
     * @param mixed $currentState
     */
    public function setCurrentState($currentState)
    {
        $this->currentState = $currentState;
    }

    /**
     *
     * @return mixed
     */
    public function getVendorItemCode()
    {
        return $this->vendorItemCode;
    }

    /**
     *
     * @param mixed $vendorItemCode
     */
    public function setVendorItemCode($vendorItemCode)
    {
        $this->vendorItemCode = $vendorItemCode;
    }

    /**
     *
     * @return mixed
     */
    public function getTraceStock()
    {
        return $this->traceStock;
    }

    /**
     *
     * @param mixed $traceStock
     */
    public function setTraceStock($traceStock)
    {
        $this->traceStock = $traceStock;
    }

    /**
     *
     * @return mixed
     */
    public function getGrossAmount()
    {
        return $this->grossAmount;
    }

    /**
     *
     * @param mixed $grossAmount
     */
    public function setGrossAmount($grossAmount)
    {
        $this->grossAmount = $grossAmount;
    }

    /**
     *
     * @return mixed
     */
    public function getTaxAmount()
    {
        return $this->taxAmount;
    }

    /**
     *
     * @param mixed $taxAmount
     */
    public function setTaxAmount($taxAmount)
    {
        $this->taxAmount = $taxAmount;
    }

    /**
     *
     * @return mixed
     */
    public function getFaRemarks()
    {
        return $this->faRemarks;
    }

    /**
     *
     * @param mixed $faRemarks
     */
    public function setFaRemarks($faRemarks)
    {
        $this->faRemarks = $faRemarks;
    }

    /**
     *
     * @return mixed
     */
    public function getRowIdentifer()
    {
        return $this->rowIdentifer;
    }

    /**
     *
     * @param mixed $rowIdentifer
     */
    public function setRowIdentifer($rowIdentifer)
    {
        $this->rowIdentifer = $rowIdentifer;
    }

    /**
     *
     * @return mixed
     */
    public function getDiscountRate()
    {
        return $this->discountRate;
    }

    /**
     *
     * @param mixed $discountRate
     */
    public function setDiscountRate($discountRate)
    {
        $this->discountRate = $discountRate;
    }

    /**
     *
     * @return mixed
     */
    public function getRevisionNo()
    {
        return $this->revisionNo;
    }

    /**
     *
     * @param mixed $revisionNo
     */
    public function setRevisionNo($revisionNo)
    {
        $this->revisionNo = $revisionNo;
    }

    /**
     *
     * @return mixed
     */
    public function getTargetObject()
    {
        return $this->targetObject;
    }

    /**
     *
     * @param mixed $targetObject
     */
    public function setTargetObject($targetObject)
    {
        $this->targetObject = $targetObject;
    }

    /**
     *
     * @return mixed
     */
    public function getSourceObject()
    {
        return $this->sourceObject;
    }

    /**
     *
     * @param mixed $sourceObject
     */
    public function setSourceObject($sourceObject)
    {
        $this->sourceObject = $sourceObject;
    }

    /**
     *
     * @return mixed
     */
    public function getTargetObjectId()
    {
        return $this->targetObjectId;
    }

    /**
     *
     * @param mixed $targetObjectId
     */
    public function setTargetObjectId($targetObjectId)
    {
        $this->targetObjectId = $targetObjectId;
    }

    /**
     *
     * @return mixed
     */
    public function getSourceObjectId()
    {
        return $this->sourceObjectId;
    }

    /**
     *
     * @param mixed $sourceObjectId
     */
    public function setSourceObjectId($sourceObjectId)
    {
        $this->sourceObjectId = $sourceObjectId;
    }

    /**
     *
     * @return mixed
     */
    public function getDocStatus()
    {
        return $this->docStatus;
    }

    /**
     *
     * @param mixed $docStatus
     */
    public function setDocStatus($docStatus)
    {
        $this->docStatus = $docStatus;
    }

    /**
     *
     * @return mixed
     */
    public function getWorkflowStatus()
    {
        return $this->workflowStatus;
    }

    /**
     *
     * @param mixed $workflowStatus
     */
    public function setWorkflowStatus($workflowStatus)
    {
        $this->workflowStatus = $workflowStatus;
    }

    /**
     *
     * @return mixed
     */
    public function getTransactionStatus()
    {
        return $this->transactionStatus;
    }

    /**
     *
     * @param mixed $transactionStatus
     */
    public function setTransactionStatus($transactionStatus)
    {
        $this->transactionStatus = $transactionStatus;
    }

    /**
     *
     * @return mixed
     */
    public function getIsPosted()
    {
        return $this->isPosted;
    }

    /**
     *
     * @param mixed $isPosted
     */
    public function setIsPosted($isPosted)
    {
        $this->isPosted = $isPosted;
    }

    /**
     *
     * @return mixed
     */
    public function getIsDraft()
    {
        return $this->isDraft;
    }

    /**
     *
     * @param mixed $isDraft
     */
    public function setIsDraft($isDraft)
    {
        $this->isDraft = $isDraft;
    }

    /**
     *
     * @return mixed
     */
    public function getExwUnitPrice()
    {
        return $this->exwUnitPrice;
    }

    /**
     *
     * @param mixed $exwUnitPrice
     */
    public function setExwUnitPrice($exwUnitPrice)
    {
        $this->exwUnitPrice = $exwUnitPrice;
    }

    /**
     *
     * @return mixed
     */
    public function getTotalExwPrice()
    {
        return $this->totalExwPrice;
    }

    /**
     *
     * @param mixed $totalExwPrice
     */
    public function setTotalExwPrice($totalExwPrice)
    {
        $this->totalExwPrice = $totalExwPrice;
    }

    /**
     *
     * @return mixed
     */
    public function getConvertFactorPurchase()
    {
        return $this->convertFactorPurchase;
    }

    /**
     *
     * @param mixed $convertFactorPurchase
     */
    public function setConvertFactorPurchase($convertFactorPurchase)
    {
        $this->convertFactorPurchase = $convertFactorPurchase;
    }

    /**
     *
     * @return mixed
     */
    public function getConvertedPurchaseQuantity()
    {
        return $this->convertedPurchaseQuantity;
    }

    /**
     *
     * @param mixed $convertedPurchaseQuantity
     */
    public function setConvertedPurchaseQuantity($convertedPurchaseQuantity)
    {
        $this->convertedPurchaseQuantity = $convertedPurchaseQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getConvertedStandardQuantity()
    {
        return $this->convertedStandardQuantity;
    }

    /**
     *
     * @param mixed $convertedStandardQuantity
     */
    public function setConvertedStandardQuantity($convertedStandardQuantity)
    {
        $this->convertedStandardQuantity = $convertedStandardQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getConvertedStockQuantity()
    {
        return $this->convertedStockQuantity;
    }

    /**
     *
     * @param mixed $convertedStockQuantity
     */
    public function setConvertedStockQuantity($convertedStockQuantity)
    {
        $this->convertedStockQuantity = $convertedStockQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getConvertedStandardUnitPrice()
    {
        return $this->convertedStandardUnitPrice;
    }

    /**
     *
     * @param mixed $convertedStandardUnitPrice
     */
    public function setConvertedStandardUnitPrice($convertedStandardUnitPrice)
    {
        $this->convertedStandardUnitPrice = $convertedStandardUnitPrice;
    }

    /**
     *
     * @return mixed
     */
    public function getConvertedStockUnitPrice()
    {
        return $this->convertedStockUnitPrice;
    }

    /**
     *
     * @param mixed $convertedStockUnitPrice
     */
    public function setConvertedStockUnitPrice($convertedStockUnitPrice)
    {
        $this->convertedStockUnitPrice = $convertedStockUnitPrice;
    }

    /**
     *
     * @return mixed
     */
    public function getDocQuantity()
    {
        return $this->docQuantity;
    }

    /**
     *
     * @param mixed $docQuantity
     */
    public function setDocQuantity($docQuantity)
    {
        $this->docQuantity = $docQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getDocUnit()
    {
        return $this->docUnit;
    }

    /**
     *
     * @param mixed $docUnit
     */
    public function setDocUnit($docUnit)
    {
        $this->docUnit = $docUnit;
    }

    /**
     *
     * @return mixed
     */
    public function getDocUnitPrice()
    {
        return $this->docUnitPrice;
    }

    /**
     *
     * @param mixed $docUnitPrice
     */
    public function setDocUnitPrice($docUnitPrice)
    {
        $this->docUnitPrice = $docUnitPrice;
    }

    /**
     *
     * @return mixed
     */
    public function getConvertedPurchaseUnitPrice()
    {
        return $this->convertedPurchaseUnitPrice;
    }

    /**
     *
     * @param mixed $convertedPurchaseUnitPrice
     */
    public function setConvertedPurchaseUnitPrice($convertedPurchaseUnitPrice)
    {
        $this->convertedPurchaseUnitPrice = $convertedPurchaseUnitPrice;
    }

    /**
     *
     * @return mixed
     */
    public function getDocType()
    {
        return $this->docType;
    }

    /**
     *
     * @param mixed $docType
     */
    public function setDocType($docType)
    {
        $this->docType = $docType;
    }

    /**
     *
     * @return mixed
     */
    public function getDescriptionText()
    {
        return $this->descriptionText;
    }

    /**
     *
     * @param mixed $descriptionText
     */
    public function setDescriptionText($descriptionText)
    {
        $this->descriptionText = $descriptionText;
    }

    /**
     *
     * @return mixed
     */
    public function getVendorItemName()
    {
        return $this->vendorItemName;
    }

    /**
     *
     * @param mixed $vendorItemName
     */
    public function setVendorItemName($vendorItemName)
    {
        $this->vendorItemName = $vendorItemName;
    }

    /**
     *
     * @return mixed
     */
    public function getReversalBlocked()
    {
        return $this->reversalBlocked;
    }

    /**
     *
     * @param mixed $reversalBlocked
     */
    public function setReversalBlocked($reversalBlocked)
    {
        $this->reversalBlocked = $reversalBlocked;
    }

    /**
     *
     * @return mixed
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     *
     * @param mixed $invoice
     */
    public function setInvoice($invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     *
     * @return mixed
     */
    public function getLastchangeBy()
    {
        return $this->lastchangeBy;
    }

    /**
     *
     * @param mixed $lastchangeBy
     */
    public function setLastchangeBy($lastchangeBy)
    {
        $this->lastchangeBy = $lastchangeBy;
    }

    /**
     *
     * @return mixed
     */
    public function getPrRow()
    {
        return $this->prRow;
    }

    /**
     *
     * @param mixed $prRow
     */
    public function setPrRow($prRow)
    {
        $this->prRow = $prRow;
    }

    /**
     *
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     *
     * @param mixed $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     *
     * @return mixed
     */
    public function getWarehouse()
    {
        return $this->warehouse;
    }

    /**
     *
     * @param mixed $warehouse
     */
    public function setWarehouse($warehouse)
    {
        $this->warehouse = $warehouse;
    }

    /**
     *
     * @return mixed
     */
    public function getPo()
    {
        return $this->po;
    }

    /**
     *
     * @param mixed $po
     */
    public function setPo($po)
    {
        $this->po = $po;
    }

    /**
     *
     * @return mixed
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     *
     * @param mixed $item
     */
    public function setItem($item)
    {
        $this->item = $item;
    }

    /**
     *
     * @return mixed
     */
    public function getDocUom()
    {
        return $this->docUom;
    }

    /**
     *
     * @param mixed $docUom
     */
    public function setDocUom($docUom)
    {
        $this->docUom = $docUom;
    }

    /**
     *
     * @return mixed
     */
    public function getDocVersion()
    {
        return $this->docVersion;
    }

    /**
     *
     * @param mixed $docVersion
     */
    public function setDocVersion($docVersion)
    {
        $this->docVersion = $docVersion;
    }

    /**
     *
     * @return mixed
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     *
     * @param mixed $uuid
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     *
     * @return mixed
     */
    public function getLocalUnitPrice()
    {
        return $this->localUnitPrice;
    }

    /**
     *
     * @param mixed $localUnitPrice
     */
    public function setLocalUnitPrice($localUnitPrice)
    {
        $this->localUnitPrice = $localUnitPrice;
    }

    /**
     *
     * @return mixed
     */
    public function getExwCurrency()
    {
        return $this->exwCurrency;
    }

    /**
     *
     * @param mixed $exwCurrency
     */
    public function setExwCurrency($exwCurrency)
    {
        $this->exwCurrency = $exwCurrency;
    }

    /**
     *
     * @return mixed
     */
    public function getLocalNetAmount()
    {
        return $this->localNetAmount;
    }

    /**
     *
     * @param mixed $localNetAmount
     */
    public function setLocalNetAmount($localNetAmount)
    {
        $this->localNetAmount = $localNetAmount;
    }

    /**
     *
     * @return mixed
     */
    public function getLocalGrossAmount()
    {
        return $this->localGrossAmount;
    }

    /**
     *
     * @param mixed $localGrossAmount
     */
    public function setLocalGrossAmount($localGrossAmount)
    {
        $this->localGrossAmount = $localGrossAmount;
    }

    /**
     *
     * @return mixed
     */
    public function getTransactionType()
    {
        return $this->transactionType;
    }

    /**
     *
     * @param mixed $transactionType
     */
    public function setTransactionType($transactionType)
    {
        $this->transactionType = $transactionType;
    }

    /**
     *
     * @return mixed
     */
    public function getIsReversed()
    {
        return $this->isReversed;
    }

    /**
     *
     * @param mixed $isReversed
     */
    public function setIsReversed($isReversed)
    {
        $this->isReversed = $isReversed;
    }

    /**
     *
     * @return mixed
     */
    public function getReversalDate()
    {
        return $this->reversalDate;
    }

    /**
     *
     * @param mixed $reversalDate
     */
    public function setReversalDate($reversalDate)
    {
        $this->reversalDate = $reversalDate;
    }

    /**
     *
     * @return mixed
     */
    public function getGlAccount()
    {
        return $this->glAccount;
    }

    /**
     *
     * @param mixed $glAccount
     */
    public function setGlAccount($glAccount)
    {
        $this->glAccount = $glAccount;
    }

    /**
     *
     * @return mixed
     */
    public function getCostCenter()
    {
        return $this->costCenter;
    }

    /**
     *
     * @param mixed $costCenter
     */
    public function setCostCenter($costCenter)
    {
        $this->costCenter = $costCenter;
    }

    /**
     *
     * @return mixed
     */
    public function getStandardConvertFactor()
    {
        return $this->standardConvertFactor;
    }

    /**
     *
     * @param mixed $standardConvertFactor
     */
    public function setStandardConvertFactor($standardConvertFactor)
    {
        $this->standardConvertFactor = $standardConvertFactor;
    }

    /**
     *
     * @return mixed
     */
    public function getClearingDocId()
    {
        return $this->clearingDocId;
    }

    /**
     *
     * @param mixed $clearingDocId
     */
    public function setClearingDocId($clearingDocId)
    {
        $this->clearingDocId = $clearingDocId;
    }

    /**
     *
     * @return mixed
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     *
     * @param mixed $brand
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;
    }

    /**
     *
     * @return mixed
     */
    public function getVariantId()
    {
        return $this->variantId;
    }

    /**
     *
     * @param mixed $variantId
     */
    public function setVariantId($variantId)
    {
        $this->variantId = $variantId;
    }

    /**
     *
     * @return mixed
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     *
     * @param mixed $project
     */
    public function setProject($project)
    {
        $this->project = $project;
    }
}


