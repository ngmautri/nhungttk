<?php
namespace Procure\Domain\QuotationRequest;

use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\SnapshotAssembler;
use Procure\Application\DTO\Qr\QrRowDTO;
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

    public static function createSnapshotProps()
    {
        $entity = new self();
        $reflectionClass = new \ReflectionClass($entity);

        $props = $reflectionClass->getProperties();

        foreach ($props as $property) {

            if ($property->class == $reflectionClass->getName()) {
                $property->setAccessible(true);
                $propertyName = $property->getName();
                print "\n" . "public $" . $propertyName . ";";
            }
        }
    }

    public static function createAllSnapshotProps()
    {
        $entity = new self();
        $reflectionClass = new \ReflectionClass($entity);

        $props = $reflectionClass->getProperties();

        foreach ($props as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print "\n" . "public $" . $propertyName . ";";
        }
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
