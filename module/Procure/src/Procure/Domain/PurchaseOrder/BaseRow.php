<?php
namespace Procure\Domain\PurchaseOrder;

use Application\Domain\Shared\Money\MoneyParser;
use Application\Domain\Shared\Price\Price;
use Application\Domain\Shared\Quantity\Quantity;
use Application\Domain\Shared\Uom\Uom;
use Application\Domain\Shared\Uom\UomPair;
use Money\Currency;
use Money\CurrencyPair;
use Procure\Domain\GenericRow;
use Application\Domain\Shared\Number\NumberFormatter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BaseRow extends GenericRow
{

    // Specific Attributes
    // =================================
    protected $draftGrQuantity;

    protected $postedGrQuantity;

    protected $confirmedGrBalance;

    protected $openGrBalance;

    protected $draftAPQuantity;

    protected $postedAPQuantity;

    protected $openAPQuantity;

    protected $billedAmount;

    protected $openAPAmount;

    // =================================
    protected $poId;

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

    protected function createUomVO()
    {
        // UOM VO
        // ==================
        $this->docUomVO = new Uom($this->getDocUnit());
        $this->itemStandardUomVO = new Uom($this->getItemStandardUnitName());
        $this->uomPairVO = new UomPair( $this->itemStandardUomVO, $this->docUomVO,$this->getStandardConvertFactor());
    }

    protected function createQuantityVO()
    {
        // Quantity VO
        // ==================
        $this->docQuantityVO = new Quantity($this->docQuantity, $this->docUomVO);
        $this->itemStandardQuantityVO = $this->docQuantityVO->convert($this->uomPairVO);
    }

    protected function createDocPriceVO()
    {
        $unitPriceMoney = MoneyParser::parseFromLocalizedDecimal(NumberFormatter::formatToEN($this->docUnitPrice), new Currency($this->docCurrencyISO));
        $this->docUnitPriceVO = new Price($unitPriceMoney, new Quantity(1, $this->docUomVO));

        $this->docItemStandardUnitPriceVO = $this->docUnitPriceVO->convertQuantiy($this->uomPairVO)->getUnitPrice();

        $this->docNetAmountVO = $this->docUnitPriceVO->multiply($this->docQuantityVO->getAmount());


        $this->docTaxAmountVO = 0;
        if ($this->getTaxRate() > 0) {
            $tmp = $this->docNetAmountVO->multiply($this->getTaxRate());
            $this->docTaxAmountVO = $tmp->divideMoney(100);
        }

        if ($this->docTaxAmountVO == 0) {
            $this->docGrossAmountVO = $this->docNetAmountVO->multiply(1);
        } else {
            $this->docGrossAmountVO = $this->docNetAmountVO->add($this->docTaxAmountVO);
        }
    }

    protected function createLocalPriceVO()
    {
        // Currency VO
        // ==================
        $this->docCurrencyVO = new Currency(new Currency($this->docCurrencyISO));
        $this->localCurrencyVO = new Currency(new Currency($this->localCurrencyISO));
        $this->currencyPair = new CurrencyPair($this->docCurrencyVO, $this->localCurrencyVO, $this->getExchangeRate());

        $this->localUnitPrice = $this->docUnitPriceVO->convertCurrency($this->currencyPair);
        $this->localItemStandardUnitPriceVO = $this->docItemStandardUnitPriceVO->convertCurrency($this->currencyPair);
        $this->LocalNetAmountVO = $this->docNetAmountVO->convertCurrency($this->currencyPair);
        $this->localGrossAmountVO = $this->docGrossAmountVO->convertCurrency($this->currencyPair);
    }

    /**
     *
     * @return mixed
     */
    public function getPoId()
    {
        return $this->poId;
    }

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

    /**
     *
     * @param mixed $draftGrQuantity
     */
    protected function setDraftGrQuantity($draftGrQuantity)
    {
        $this->draftGrQuantity = $draftGrQuantity;
    }

    /**
     *
     * @param mixed $postedGrQuantity
     */
    protected function setPostedGrQuantity($postedGrQuantity)
    {
        $this->postedGrQuantity = $postedGrQuantity;
    }

    /**
     *
     * @param mixed $confirmedGrBalance
     */
    protected function setConfirmedGrBalance($confirmedGrBalance)
    {
        $this->confirmedGrBalance = $confirmedGrBalance;
    }

    /**
     *
     * @param mixed $openGrBalance
     */
    protected function setOpenGrBalance($openGrBalance)
    {
        $this->openGrBalance = $openGrBalance;
    }

    /**
     *
     * @param mixed $draftAPQuantity
     */
    protected function setDraftAPQuantity($draftAPQuantity)
    {
        $this->draftAPQuantity = $draftAPQuantity;
    }

    /**
     *
     * @param mixed $postedAPQuantity
     */
    protected function setPostedAPQuantity($postedAPQuantity)
    {
        $this->postedAPQuantity = $postedAPQuantity;
    }

    /**
     *
     * @param mixed $openAPQuantity
     */
    protected function setOpenAPQuantity($openAPQuantity)
    {
        $this->openAPQuantity = $openAPQuantity;
    }

    /**
     *
     * @param mixed $billedAmount
     */
    protected function setBilledAmount($billedAmount)
    {
        $this->billedAmount = $billedAmount;
    }

    /**
     *
     * @param mixed $openAPAmount
     */
    protected function setOpenAPAmount($openAPAmount)
    {
        $this->openAPAmount = $openAPAmount;
    }

    // =================================

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
     * @return mixed
     */
    public function getPostedGrQuantity()
    {
        return $this->postedGrQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getConfirmedGrBalance()
    {
        return $this->confirmedGrBalance;
    }

    /**
     *
     * @return mixed
     */
    public function getOpenGrBalance()
    {
        return $this->openGrBalance;
    }

    /**
     *
     * @return mixed
     */
    public function getDraftAPQuantity()
    {
        return $this->draftAPQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedAPQuantity()
    {
        return $this->postedAPQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getOpenAPQuantity()
    {
        return $this->openAPQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getBilledAmount()
    {
        return $this->billedAmount;
    }

    /**
     *
     * @return mixed
     */
    public function getOpenAPAmount()
    {
        return $this->openAPAmount;
    }
}
