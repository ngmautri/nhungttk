<?php
namespace Procure\Application\DTO\Item;

/**
 * PRRow Detail.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemPriceCompareDTO
{

    public $docType;

    public $rowIdentifer;

    public $itemId;

    public $itemName;

    public $itemSKU;

    public $itemSKU1;

    public $itemSKU2;

    public $sourceId;

    public $vendorId;

    public $vendorName;

    public $currencyId;

    public $currencyISO;

    public $docCurrencyId;

    public $docCurrencyISO;

    public $localCurrencyId;

    public $localCurrencyISO;

    public $exchangeRate;

    public $sourceIsActive;

    public $sourceCreatedOn;

    public $docQuantity;

    public $convertFactor;

    public $docUnit;

    public $docUnitPrice;

    public $localUnitPrice;

    public $localTotalPrice;

    public $incoterm;

    public $incotermPlace;

    public $vendorItemName;

    public $vendorItemCode;

}
