<?php
namespace Procure\Domain\QuotationRequest;

use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use PHPUnit\Framework\Assert;
use Procure\Application\DTO\Qr\QrRowDTO;
use Procure\Domain\GenericDoc;
use Procure\Domain\GenericRow;

/**
 * Quotation Row
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class QRRow extends GenericRow
{

    private static $instance = null;

    // Specific Attributes
    // =================================
    protected $qo;

    // =================================
    private function __construct()
    {}

    public static function cloneFrom(QRDoc $rootDoc, QRRow $sourceObj, CommandOptions $options)
    {
        Assert::isInstanceOf($rootDoc, QRDoc::class, "QR is required!");
        Assert::isInstanceOf($sourceObj, QRRow::class, "QR row is required!");
        Assert::notNull($options, "No Options is found");

        /**
         *
         * @var QRRow $instance
         */
        $instance = new self();

        $exculdedProps = [
            'rowIdentifer'
        ];

        $instance = $sourceObj->convertExcludeFieldsTo($instance, $exculdedProps);
        $instance->initRow($options);
        return $instance;
    }

    protected function createVO(GenericDoc $rootDoc)
    {
        $this->createUomVO();
        $this->createQuantityVO();
        $this->createDocPriceVO($rootDoc);
        $this->createLocalPriceVO($rootDoc);
    }

    public static function createFromSnapshot(QRDoc $rootDoc, QRRowSnapshot $snapshot)
    {
        Assert::isInstanceOf($rootDoc, QRDoc::class, "PR is required!");
        Assert::isInstanceOf($snapshot, QRRowSnapshot::class, "PR row snapshot is required!");

        $instance = new self();

        GenericObjectAssembler::updateAllFieldsFrom($instance, $snapshot);
        $instance->createVO($rootDoc);
        return $instance;
    }

    /**
     *
     * @param QRRowSnapshot $snapshot
     * @return NULL|\Procure\Domain\QuotationRequest\QRRow
     */
    public static function makeFromSnapshot(QRRowSnapshot $snapshot)
    {
        if (! $snapshot instanceof QRRowSnapshot) {
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
        return SnapshotAssembler::createSnapshotFrom($this, new QRRowSnapshot());
    }

    /**
     *
     * @return NULL|object
     */
    public function makeDetailsDTO()
    {
        $dto = new QrRowDTO();
        $dto = DTOFactory::createDTOFrom($this, $dto);
        return $dto;
    }

    /**
     *
     * @return \Procure\Domain\QuotationRequest\QRRow
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new QRRow();
        }
        return self::$instance;
    }

    /**
     *
     * @return \Procure\Domain\AccountPayable\APRow
     */
    public static function createInstance()
    {
        return new self();
    }

    /**
     *
     * @return mixed
     */
    public function getQo()
    {
        return $this->qo;
    }

    /**
     *
     * @param mixed $qo
     */
    protected function setQo($qo)
    {
        $this->qo = $qo;
    }
}
