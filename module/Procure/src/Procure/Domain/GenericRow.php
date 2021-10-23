<?php
namespace Procure\Domain;

use Application\Application\Contracts\GenericSnapshotAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Application\Domain\Shared\Money\MoneyParser;
use Application\Domain\Shared\Number\NumberFormatter;
use Application\Domain\Shared\Price\Price;
use Application\Domain\Shared\Quantity\Quantity;
use Application\Domain\Shared\Uom\Uom;
use Application\Domain\Shared\Uom\UomPair;
use Money\Currency;
use Money\CurrencyPair;
use Procure\Domain\Contracts\ProcureDocStatus;
use Procure\Domain\Exception\InvalidArgumentException;

/**
 * Generic Row
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class GenericRow extends BaseRow
{

    /*
     * |=============================
     * | VALUE OBJECT VO.
     * |
     * |=============================
     */

    // Uom VO
    // ====================
    protected $docUomVO;

    protected $itemStandardUomVO;

    protected $uomPairVO;

    // Quantity VO
    // ====================
    protected $docQuantityVO;

    protected $itemStandardQuantityVO;

    // Currency VO
    // ====================
    protected $docCurrencyVO;

    protected $localCurrencyVO;

    protected $currencyPair;

    // Price VO
    // ====================
    protected $docUnitPriceVO;

    protected $docItemStandardUnitPriceVO;

    protected $docNetAmountVO;

    protected $docTaxAmountVO;

    protected $docGrossAmountVO;

    // -------------------------
    protected $localUnitPriceVO;

    protected $localItemStandardUnitPriceVO;

    protected $LocalNetAmountVO;

    protected $localTaxAmountVO;

    protected $localGrossAmountVO;

    /*
     * |=============================
     * | FUNCTION
     * |
     * |=============================
     */
    private $exculdedProps = [
        "id",
        "uuid",
        "token",
        "instance",
        "sysNumber",
        "createdBy",
        "lastchangeBy",
        "docId",
        "docToken",
        "revisionNo",
        "docVersion"
    ];

    abstract protected function createVO(GenericDoc $rootDoc);

    public function updateRowStatus()
    {}

    protected function createWarehouseVO()
    {}

    protected function createUomVO()
    {
        // UOM VO
        // ==================
        $this->docUomVO = new Uom($this->getDocUnit(), 'docUomVO');
        $this->itemStandardUomVO = new Uom($this->getItemStandardUnitName(), 'itemStandardUomVO');
        $this->uomPairVO = new UomPair($this->itemStandardUomVO, $this->docUomVO, $this->getStandardConvertFactor());
    }

    protected function createQuantityVO()
    {
        // Quantity VO
        // ==================
        $this->docQuantityVO = new Quantity($this->docQuantity, $this->docUomVO);
        $this->itemStandardQuantityVO = $this->docQuantityVO->convert($this->uomPairVO);
        $this->setConvertedStandardQuantity($this->itemStandardQuantityVO->getAmount());
    }

    /**
     *
     * @param GenericDoc $rootDoc
     */
    protected function createDocPriceVO(GenericDoc $rootDoc)
    {
        $unitPriceMoney = MoneyParser::parseFromLocalizedDecimal(NumberFormatter::formatToEN($this->docUnitPrice), new Currency($rootDoc->getDocCurrencyISO()));
        $this->docUnitPriceVO = new Price($unitPriceMoney, new Quantity(1, $this->docUomVO));

        $this->docItemStandardUnitPriceVO = $this->docUnitPriceVO->convertQuantiy($this->uomPairVO)->getUnitPrice();
        $this->setConvertedStandardUnitPrice($this->docItemStandardUnitPriceVO->getMoneyAmountInEn());

        $this->docNetAmountVO = $this->docUnitPriceVO->multiply($this->docQuantityVO->getAmount());

        $this->docTaxAmountVO = null;
        if ($this->getTaxRate() > 0) {
            $tmp = $this->docNetAmountVO->multiply($this->getTaxRate());
            $this->docTaxAmountVO = $tmp->divideMoney(100);
            $this->setTaxAmount($this->docTaxAmountVO->getMoneyAmountInEn());
        } else {
            $this->setTaxAmount(0);
        }

        if ($this->docTaxAmountVO == null) {
            $this->docGrossAmountVO = $this->docNetAmountVO->multiply(1);
        } else {
            $this->docGrossAmountVO = $this->docNetAmountVO->add($this->docTaxAmountVO);
        }

        $this->setNetAmount($this->docNetAmountVO->getMoneyAmountInEn());
        $this->setGrossAmount($this->docGrossAmountVO->getMoneyAmountInEn());
    }

    /**
     *
     * @param GenericDoc $rootDoc
     */
    protected function createLocalPriceVO(GenericDoc $rootDoc)
    {
        // Currency VO
        // ==================
        $this->docCurrencyVO = new Currency($rootDoc->getDocCurrencyISO());
        $this->localCurrencyVO = new Currency($rootDoc->getLocalCurrencyISO());

        $this->currencyPair = new CurrencyPair($this->localCurrencyVO, $this->docCurrencyVO, 1 / $rootDoc->getExchangeRate());

        $this->localUnitPriceVO = $this->docUnitPriceVO->convertCurrency($this->currencyPair);
        $this->localItemStandardUnitPriceVO = $this->docItemStandardUnitPriceVO->convertCurrency($this->currencyPair);
        $this->LocalNetAmountVO = $this->docNetAmountVO->convertCurrency($this->currencyPair);
        $this->localGrossAmountVO = $this->docGrossAmountVO->convertCurrency($this->currencyPair);

        $this->setLocalUnitPrice($this->localUnitPriceVO->getMoneyAmountInEn());
        $this->setLocalNetAmount($this->LocalNetAmountVO->getMoneyAmountInEn());
        $this->setLocalGrossAmount($this->localGrossAmountVO->getMoneyAmountInEn());
    }

    /**
     *
     * @param GenericRow $targetObj
     * @throws InvalidArgumentException
     * @return \Procure\Domain\GenericRow
     */
    public function convertTo(GenericRow $targetObj)
    {
        if (! $targetObj instanceof GenericRow) {
            throw new InvalidArgumentException("Convertion input invalid!");
        }

        // Converting
        // ==========================

        $sourceObj = $this;
        $reflectionClass = new \ReflectionClass(get_class($sourceObj));
        $props = $reflectionClass->getProperties();

        foreach ($props as $prop) {
            $prop->setAccessible(true);

            $propName = $prop->getName();

            if (\in_array($propName, $this->exculdedProps)) {
                continue;
            }

            if (property_exists($targetObj, $propName)) {
                $targetObj->$propName = $prop->getValue($sourceObj);
            }
        }
        return $targetObj;
    }

    public function convertExcludeFieldsTo(GenericRow $targetObj, $exculdedProps)
    {
        if (! $targetObj instanceof GenericRow) {
            throw new InvalidArgumentException("Convertion input invalid!");
        }

        // Converting
        // ==========================

        $sourceObj = $this;
        $reflectionClass = new \ReflectionClass(get_class($sourceObj));
        $props = $reflectionClass->getProperties();

        foreach ($props as $prop) {
            $prop->setAccessible(true);

            $propName = $prop->getName();

            if (\in_array($propName, $this->exculdedProps) || \in_array($propName, $exculdedProps)) {
                continue;
            }

            if (property_exists($targetObj, $propName)) {
                $targetObj->$propName = $prop->getValue($sourceObj);
            }
        }
        return $targetObj;
    }

    /**
     *
     * @param int $createdBy
     * @param string $createdDate
     */
    protected function initRow(CommandOptions $options)
    {
        $createdDate = new \Datetime();
        $this->setCreatedOn(date_format($createdDate, 'Y-m-d H:i:s'));
        $this->setCreatedBy($options->getUserId());

        $this->setDocStatus(ProcureDocStatus::DRAFT);

        $this->setIsActive(1);
        $this->setIsDraft(1);
        $this->setIsPosted(0);

        $this->setRevisionNo(0);
        $this->setDocVersion(0);
        $this->setUuid(\Ramsey\Uuid\Uuid::uuid4()->toString());
        $this->setToken($this->getUuid());
    }

    protected function updateRow($createdBy, $createdDate)
    {
        $this->setCreatedOn($createdDate);
        $this->setCreatedBy($createdBy);
        $this->setDocStatus(ProcureDocStatus::DRAFT);

        $this->setIsActive(1);
        $this->setIsDraft(1);
        $this->setIsPosted(0);
    }

    /**
     *
     * @param int $postedBy
     * @param string $postedDate
     */
    public function markAsPosted($postedBy, $postedDate)
    {
        $this->setLastchangeOn($postedDate);
        $this->setLastchangeBy($postedBy);
        $this->setIsPosted(1);
        $this->setIsActive(1);
        $this->setIsDraft(0);
        $this->setIsReversed(0);
        $this->setDocStatus(ProcureDocStatus::POSTED);
    }

    /**
     *
     * @param GenericDoc $rootDoc
     * @param CommandOptions $options
     */
    public function markRowAsPosted(GenericDoc $rootDoc, CommandOptions $options)
    {
        $this->createVO($rootDoc); // important to recalculate before posting.

        $createdDate = new \Datetime();
        $this->setLastchangeOn(date_format($createdDate, 'Y-m-d H:i:s'));

        $this->setIsPosted(1);
        $this->setIsActive(1);
        $this->setIsDraft(0);
        $this->setIsReversed(0);
        $this->setDocStatus(ProcureDocStatus::POSTED);
    }

    public function markRowAsReversed(GenericDoc $rootDoc, CommandOptions $options)
    {
        $this->createVO($rootDoc); // important to recalculate before posting.

        $createdDate = new \Datetime();
        $this->setLastchangeOn(date_format($createdDate, 'Y-m-d H:i:s'));
        $this->setLastchangeBy($options->getUserId());

        $this->setIsReversed(1);
        $this->setIsActive(1);
        $this->setIsDraft(0);
        $this->setIsPosted(0);
        $this->setDocStatus(ProcureDocStatus::REVERSED);
    }

    /**
     *
     * @param int $postedBy
     * @param \DateTime $postedDate
     */
    public function markAsReversed(CommandOptions $options)
    {
        $createdDate = new \Datetime();
        $this->setLastchangeOn(date_format($createdDate, 'Y-m-d H:i:s'));
        $this->setLastchangeBy($options->getUserId());

        $this->setIsReversed(1);
        $this->setIsActive(1);
        $this->setIsDraft(0);
        $this->setIsPosted(0);
        $this->setDocStatus(ProcureDocStatus::REVERSED);
    }

    public function refreshRowsFromNewHeaderSnapshot(DocSnapshot $snapshot)
    {
        // only update, if row does not have PR
        if ($this->getPrRow() > 0) {
            return;
        }
        $this->setWarehouse($snapshot->getWarehouse());
    }

    /**
     *
     * @return NULL|object
     */
    public function makeSnapshot()
    {
        return GenericSnapshotAssembler::createSnapshotFrom($this, new RowSnapshot());
    }

    /*
     * |=============================
     * | DEPRECETED
     * |
     * |=============================
     */

    /**
     *
     * @deprecated
     */
    public function calculateQuantity()
    {
        trigger_error("Deprecated function called." . __METHOD__, E_USER_NOTICE);

        if ($this->hasErrors()) {
            return;
        }

        try {
            // $this->convertedDocQuantity = $this->getDocQuantity() * $this->getConversionFactor();
            // $this->convertedDocUnitPrice = $this->getDocUnitPrice() / $this->getConvertedDocQuantity();

            // actuallly converted doc quantity /price.
            $this->quantity = $this->getDocQuantity() * $this->getConversionFactor();
            $this->unitPrice = $this->getDocUnitPrice() / $this->getConversionFactor();

            $netAmount = $this->getDocUnitPrice() * $this->getDocQuantity();

            $discountAmount = 0;
            if ($this->getDiscountRate() > 0) {
                $discountAmount = $netAmount * ($this->getDiscountRate() / 100);
                $this->setDiscountAmount($discountAmount);
                $netAmount = $netAmount - $discountAmount;
            }

            $taxAmount = $netAmount * $this->getTaxRate() / 100;
            $grosAmount = $netAmount + $taxAmount;

            $this->setNetAmount($netAmount);
            $this->setTaxAmount($taxAmount);
            $this->setGrossAmount($grosAmount);

            $convertedPurchaseQuantity = $this->getDocQuantity();
            $convertedPurchaseUnitPrice = $this->getDocUnitPrice();

            $conversionFactor = $this->getConversionFactor();

            $standardCF = 1;

            if ($this->getStandardConvertFactor() > 0) {
                $standardCF = $this->getStandardConvertFactor();
            }

            $prRowConvertFactor = $this->getPrRowConvertFactor();

            if ($this->getPrRow() > 0) {
                $convertedPurchaseQuantity = $convertedPurchaseQuantity * $conversionFactor;
                $convertedPurchaseUnitPrice = $convertedPurchaseUnitPrice / $conversionFactor;
                $standardCF = $standardCF * $prRowConvertFactor;
            }

            // quantity /unit price is converted purchase quantity to clear PR

            // $entity->setQuantity($convertedPurchaseQuantity);
            // $entity->setUnitPrice($convertedPurchaseUnitPrice);

            $convertedStandardQuantity = $this->getQuantity();
            $convertedStandardUnitPrice = $this->getUnitPrice();

            if ($this->getItem() > 0) {
                $convertedStandardQuantity = $convertedStandardQuantity * $standardCF;
                $convertedStandardUnitPrice = $convertedStandardUnitPrice / $standardCF;
            }

            // calculate standard quantity
            $this->setConvertedPurchaseQuantity($convertedPurchaseQuantity);
            $this->setConvertedPurchaseUnitPrice($convertedPurchaseUnitPrice);

            $this->setConvertedStandardQuantity($convertedStandardQuantity);
            $this->setConvertedStandardUnitPrice($convertedStandardUnitPrice);
        } catch (\Exception $e) {
            $this->addError($e->getMessage());
        }
    }

    /**
     *
     * @deprecated
     * @return void|\Procure\Domain\GenericRow
     */
    public function calculate()
    {
        trigger_error("Deprecated function called." . __METHOD__, E_USER_NOTICE);

        if ($this->hasErrors()) {
            return;
        }

        try {
            // $this->convertedDocQuantity = $this->getDocQuantity() * $this->getConversionFactor();
            // $this->convertedDocUnitPrice = $this->getDocUnitPrice() / $this->getConvertedDocQuantity();

            // actuallly converted doc quantity /price.
            $this->quantity = $this->getDocQuantity() * $this->getConversionFactor();
            $this->unitPrice = $this->getDocUnitPrice() / $this->getConversionFactor();

            $netAmount = $this->getDocUnitPrice() * $this->getDocQuantity();

            $discountAmount = 0;
            if ($this->getDiscountRate() > 0) {
                $discountAmount = $netAmount * ($this->getDiscountRate() / 100);
                $this->setDiscountAmount($discountAmount);
                $netAmount = $netAmount - $discountAmount;
            }

            $taxAmount = $netAmount * $this->getTaxRate() / 100;
            $grosAmount = $netAmount + $taxAmount;

            $this->setNetAmount($netAmount);
            $this->setTaxAmount($taxAmount);
            $this->setGrossAmount($grosAmount);

            $convertedPurchaseQuantity = $this->getDocQuantity();
            $convertedPurchaseUnitPrice = $this->getDocUnitPrice();

            $conversionFactor = $this->getConversionFactor();

            $standardCF = 1;

            if ($this->getStandardConvertFactor() > 0) {
                $standardCF = $this->getStandardConvertFactor();
            }

            $prRowConvertFactor = $this->getPrRowConvertFactor();

            if ($this->getPrRow() > 0) {
                $convertedPurchaseQuantity = $convertedPurchaseQuantity * $conversionFactor;
                $convertedPurchaseUnitPrice = $convertedPurchaseUnitPrice / $conversionFactor;
                $standardCF = $standardCF * $prRowConvertFactor;
            }

            // quantity /unit price is converted purchase quantity to clear PR

            // $entity->setQuantity($convertedPurchaseQuantity);
            // $entity->setUnitPrice($convertedPurchaseUnitPrice);

            $convertedStandardQuantity = $this->getQuantity();
            $convertedStandardUnitPrice = $this->getUnitPrice();

            if ($this->getItem() > 0) {
                $convertedStandardQuantity = $convertedStandardQuantity * $standardCF;
                $convertedStandardUnitPrice = $convertedStandardUnitPrice / $standardCF;
            }

            // calculate standard quantity
            $this->setConvertedPurchaseQuantity($convertedPurchaseQuantity);
            $this->setConvertedPurchaseUnitPrice($convertedPurchaseUnitPrice);

            $this->setConvertedStandardQuantity($convertedStandardQuantity);
            $this->setConvertedStandardUnitPrice($convertedStandardUnitPrice);
        } catch (\Exception $e) {
            $this->addError($e->getMessage());
        }
    }

    /**
     * this should be called when validated.
     *
     * @deprecated
     * @return \Procure\Domain\PurchaseOrder\PORow
     */
    public function refresh()
    {
        trigger_error("Deprecated function called." . __METHOD__, E_USER_NOTICE);

        if ($this->hasErrors()) {
            return;
        }

        $netAmount = $this->getDocUnitPrice() * $this->getDocQuantity();
        $taxAmount = $netAmount * $this->getTaxRate();
        $grosAmount = $netAmount + $taxAmount;

        $this->netAmount = $netAmount;
        $this->taxAmount = $taxAmount;
        $this->grossAmount = $grosAmount;
        return $this;
    }

    /*
     * |=============================
     * | SETTER AND GETTER
     * |
     * |=============================
     */

    /**
     *
     * @return \Application\Domain\Shared\Uom\Uom
     */
    public function getDocUomVO()
    {
        return $this->docUomVO;
    }

    /**
     *
     * @return \Application\Domain\Shared\Uom\Uom
     */
    public function getItemStandardUomVO()
    {
        return $this->itemStandardUomVO;
    }

    /**
     *
     * @return \Application\Domain\Shared\Uom\UomPair
     */
    public function getUomPairVO()
    {
        return $this->uomPairVO;
    }

    /**
     *
     * @return \Application\Domain\Shared\Quantity\Quantity
     */
    public function getDocQuantityVO()
    {
        return $this->docQuantityVO;
    }

    /**
     *
     * @return \Application\Domain\Shared\Quantity\Quantity
     */
    public function getItemStandardQuantityVO()
    {
        return $this->itemStandardQuantityVO;
    }

    /**
     *
     * @return \Money\Currency
     */
    public function getDocCurrencyVO()
    {
        return $this->docCurrencyVO;
    }

    /**
     *
     * @return \Money\Currency
     */
    public function getLocalCurrencyVO()
    {
        return $this->localCurrencyVO;
    }

    /**
     *
     * @return \Money\CurrencyPair
     */
    public function getCurrencyPair()
    {
        return $this->currencyPair;
    }

    /**
     *
     * @return \Application\Domain\Shared\Price\Price
     */
    public function getDocUnitPriceVO()
    {
        return $this->docUnitPriceVO;
    }

    /**
     *
     * @return \Application\Domain\Shared\Price\Price
     */
    public function getDocItemStandardUnitPriceVO()
    {
        return $this->docItemStandardUnitPriceVO;
    }

    /**
     *
     * @return \Application\Domain\Shared\Price\Price
     */
    public function getDocNetAmountVO()
    {
        return $this->docNetAmountVO;
    }

    /**
     *
     * @return \Application\Domain\Shared\Price\Price
     */
    public function getDocTaxAmountVO()
    {
        return $this->docTaxAmountVO;
    }

    /**
     *
     * @return \Application\Domain\Shared\Price\Price
     */
    public function getDocGrossAmountVO()
    {
        return $this->docGrossAmountVO;
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
     * @return \Application\Domain\Shared\Price\Price
     */
    public function getLocalItemStandardUnitPriceVO()
    {
        return $this->localItemStandardUnitPriceVO;
    }

    /**
     *
     * @return \Application\Domain\Shared\Price\Price
     */
    public function getLocalNetAmountVO()
    {
        return $this->LocalNetAmountVO;
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
     * @return \Application\Domain\Shared\Price\Price
     */
    public function getLocalGrossAmountVO()
    {
        return $this->localGrossAmountVO;
    }
}
