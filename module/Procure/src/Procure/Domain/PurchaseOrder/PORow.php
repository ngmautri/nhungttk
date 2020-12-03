<?php
namespace Procure\Domain\PurchaseOrder;

use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Procure\Application\DTO\Po\PORowDTO;
use Procure\Application\DTO\Po\PORowDetailsDTO;
use Procure\Domain\GenericDoc;
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
final class PORow extends BaseRow
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GenericRow::createVO()
     */
    protected function createVO(GenericDoc $rootDoc)
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
}
