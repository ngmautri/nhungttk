<?php
namespace Procure\Domain\PurchaseOrder;

use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Procure\Application\DTO\Po\PORowDTO;
use Procure\Application\DTO\Po\PORowDetailsDTO;
use Procure\Domain\AccountPayable\APRowSnapshotAssembler;
use Procure\Domain\Contracts\ProcureDocType;
use Procure\Domain\QuotationRequest\QRRow;
use Webmozart\Assert\Assert;

/**
 * PO Row
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PORow extends BaseRow
{

    protected function createVO(PODoc $rootDoc)
    {
        $this->createUomVO();
        $this->createQuantityVO();
        $this->createDocPriceVO($rootDoc);
        $this->createLocalPriceVO($rootDoc);
    }

    public static function cloneFrom(PODoc $rootDoc, PoRow $sourceObj, CommandOptions $options)
    {
        Assert::isInstanceOf($rootDoc, PODoc::class, "PO is required!");
        Assert::isInstanceOf($sourceObj, PoRow::class, "PO row is required!");
        Assert::notNull($options, "No Options is found");

        /**
         *
         * @var PORow $instance
         */
        $instance = new self();

        $exculdedProps = [
            'invoice',
            'po',
            'rowIdentifer'
        ];

        $instance = $sourceObj->convertExcludeFieldsTo($instance, $exculdedProps);
        $instance->initRow($options);
        return $instance;
    }

    /**
     *
     * @param PODoc $rootDoc
     * @param PORowSnapshot $snapshot
     * @return \Procure\Domain\PurchaseOrder\PORow
     */
    public static function createFromSnapshot(PODoc $rootDoc, PORowSnapshot $snapshot)
    {
        Assert::isInstanceOf($rootDoc, PODoc::class, "PO is required!");
        Assert::isInstanceOf($snapshot, PORowSnapshot::class, "PO row snapshot is required!");

        $instance = new self();

        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);
        $instance->createVO($rootDoc);
        return $instance;
    }

    public static function createFromQuoteRow(QRRow $sourceObj, CommandOptions $options)
    {
        Assert::isInstanceOf($sourceObj, QRRow::class, "Quotation document is required!");

        /**
         *
         * @var PORow $instance
         */
        $instance = new self();
        $instance = $sourceObj->convertTo($instance);

        $instance->setDocType(ProcureDocType::PO_FROM_QOUTE); // important.
                                                              // $instance->setQ($sourceObj->getId()); // Important

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $instance->initRow($options);

        return $instance;
    }

    /**
     *
     * @return \Procure\Domain\PurchaseOrder\PORow
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new PORow();
        }
        return self::$instance;
    }

    /**
     *
     * @return NULL|PORowSnapshot
     */
    public function makeSnapshot()
    {
        return APRowSnapshotAssembler::updateAllFieldsFrom(new PORowSnapshot(), $this);
    }

    /**
     *
     * @return NULL|\Procure\Application\DTO\Po\PORowDTO
     */
    public function makeDTO()
    {
        return \Application\Domain\Shared\DTOFactory::createDTOFrom($this, new PORowDTO());
    }

    /**
     *
     * @return NULL|\Procure\Application\DTO\Po\PORowDTO
     */
    public function makeDetailsDTO()
    {
        $dto = new PORowDetailsDTO();
        $dto = DTOFactory::createDTOFrom($this, $dto);
        return $dto;
    }

    /**
     *
     * @return NULL|object
     */
    public function makeDTOForGrid()
    {
        $dto = new PORowDetailsDTO();
        $dto = DTOFactory::createDTOFrom($this, $dto);
        return $dto;
    }

    /**
     *
     * @param PORowSnapshot $snapshot
     */
    public static function makeFromSnapshot(PORowSnapshot $snapshot)
    {
        if (! $snapshot instanceof PORowSnapshot) {
            return null;
        }

        $instance = new self();

        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);
        return $instance;
    }

    /**
     *
     * @param PORowDetailsSnapshot $snapshot
     */
    public static function makeFromDetailsSnapshot(PORowSnapshot $snapshot)
    {
        if (! $snapshot instanceof PORowSnapshot)
            return;

        $instance = new self();
        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);
        return $instance;
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
    public function getNetAmountVO()
    {
        return $this->netAmountVO;
    }

    /**
     *
     * @return mixed
     */
    public function getTaxAmountVO()
    {
        return $this->taxAmountVO;
    }

    /**
     *
     * @return mixed
     */
    public function getGrossAmountVO()
    {
        return $this->grossAmountVO;
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
     * @return mixed
     */
    public function getUomPairVO()
    {
        return $this->uomPairVO;
    }

    /**
     *
     * @return mixed
     */
    public function getStandardQuantityVO()
    {
        return $this->standardQuantityVO;
    }
}
