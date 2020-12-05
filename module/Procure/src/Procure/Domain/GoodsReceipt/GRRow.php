<?php
namespace Procure\Domain\GoodsReceipt;

use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Procure\Application\DTO\Gr\GrRowDTO;
use Procure\Domain\GenericDoc;
use Procure\Domain\AccountPayable\APRow;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\PurchaseOrder\PORow;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;

/**
 * Goods Receipt Row
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GRRow extends BaseRow
{

    private static $instance = null;

    protected function createVO(GenericDoc $rootDoc)
    {}

    private function __construct()
    {}

    public static function createFromSnapshot(GenericGR $rootDoc, GRRowSnapshot $snapshot)
    {
        if (! $snapshot instanceof GRRowSnapshot) {
            return null;
        }

        $instance = new self();

        GenericObjectAssembler::updateAllFieldsFrom($instance, $snapshot);
        $instance->createVO($rootDoc);
        return $instance;
    }

    /**
     *
     * @return NULL|object
     */
    public function makeDetailsDTO()
    {
        $dto = new GrRowDTO();
        $dto = DTOFactory::createDTOFrom($this, $dto);
        return $dto;
    }

    /**
     *
     * @param GRRowSnapshot $snapshot
     * @return NULL|\Procure\Domain\GoodsReceipt\GRRow
     */
    public static function makeFromSnapshot(GRRowSnapshot $snapshot)
    {
        if (! $snapshot instanceof GRRowSnapshot) {
            return null;
        }

        $instance = new self();

        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);
        return $instance;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GenericRow::makeSnapshot()
     */
    public function makeSnapshot()
    {
        return SnapshotAssembler::createSnapshotFrom($this, new GRRowSnapshot());
    }

    /**
     *
     * @param PORow $sourceObj
     * @throws InvalidArgumentException
     * @return \Procure\Domain\GoodsReceipt\GRRow
     */
    public static function createFromPoRow(PORow $sourceObj, CommandOptions $options)
    {
        if (! $sourceObj instanceof PORow) {
            throw new InvalidArgumentException("PO document is required!");
        }
        if ($options == null) {
            throw new InvalidArgumentException("No Options is found");
        }

        /**
         *
         * @var \Procure\Domain\GoodsReceipt\GRRow $instance
         */
        $instance = new self();
        $instance = $sourceObj->convertTo($instance);

        $instance->setDocType(\Procure\Domain\Shared\Constants::PROCURE_DOC_TYPE_GR_FROM_PO); // important.

        $instance->initRow($options);

        return $instance;
    }

    /**
     *
     * @param GenericGR $rootEntity
     * @param APRow $sourceObj
     * @param CommandOptions $options
     * @throws InvalidArgumentException
     * @return \Procure\Domain\GoodsReceipt\GRRow
     */
    public static function copyFromApRow(GenericGR $rootEntity, APRow $sourceObj, CommandOptions $options)
    {
        self::ensureValidParams($rootEntity, $sourceObj, $options);

        /**
         *
         * @var \Procure\Domain\GoodsReceipt\GRRow $instance
         */
        $instance = new self();
        $instance = $sourceObj->convertTo($instance);
        $instance->initRow($options);

        // overwrite
        $instance->setApInvoiceRow($sourceObj->getId()); // important
        $instance->setInvoice($sourceObj->getDocId());
        $instance->setDocType($rootEntity->getDocType()); // important.
        $instance->setIsFixedAsset($sourceObj->getIsFixedAsset());
        return $instance;
    }

    /**
     *
     * @param GenericGR $rootEntity
     * @param APRow $sourceObj
     * @param CommandOptions $options
     * @throws InvalidArgumentException
     * @return \Procure\Domain\GoodsReceipt\GRRow
     */
    public static function copyFromApRowReserval(GenericGR $rootEntity, APRow $sourceObj, CommandOptions $options)
    {
        self::ensureValidParams($rootEntity, $sourceObj, $options);

        /**
         *
         * @var \Procure\Domain\GoodsReceipt\GRRow $instance
         */
        $instance = new self();
        $instance = $sourceObj->convertTo($instance);
        // overwrite

        $instance->initRow($options);
        $instance->setApInvoiceRow($sourceObj->getId()); // important
        $instance->setInvoice($sourceObj->getDocId()); // important
        $instance->setDocType($rootEntity->getDocType()); // important.
        $instance->setRemarks("[Auto] Reversed"); // important.

        return $instance;
    }

    /**
     *
     * @param GenericGR $rootEntity
     * @param APRow $sourceObj
     * @param CommandOptions $options
     * @throws InvalidArgumentException
     */
    static private function ensureValidParams(GenericGR $rootEntity, APRow $sourceObj, CommandOptions $options)
    {
        if (! $rootEntity instanceof GenericGR) {
            throw new InvalidArgumentException("GR document is required!");
        }
        if (! $sourceObj instanceof APRow) {
            throw new InvalidArgumentException("AP document is required!");
        }
        if ($options == null) {
            throw new InvalidArgumentException("No Options is found");
        }
    }

    /**
     *
     * @return \Procure\Domain\GoodsReceipt\GRRow
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    public static function createInstance()
    {
        return new GRRow();
    }

    /**
     *
     * @param \Procure\Domain\GoodsReceipt\GRRow $instance
     */
    protected static function setInstance($instance)
    {
        GRRow::$instance = $instance;
    }
}
