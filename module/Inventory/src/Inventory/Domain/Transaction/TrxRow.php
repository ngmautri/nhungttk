<?php
namespace Inventory\Domain\Transaction;

use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\SnapshotAssembler;
use Inventory\Application\DTO\Transaction\TrxRowDTO;

/**
 * Transaction Row
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
final class TrxRow extends BaseRow
{

    private static $instance = null;

    // Adddtional Attributes, if needed
    // ====================

    // ===================
    private function __construct()
    {}

    /**
     *
     * @param TrxRowSnapshot $snapshot
     * @return NULL|\Procure\Domain\AccountPayable\APRow
     */
    public static function makeFromSnapshot(TrxRowSnapshot $snapshot)
    {
        if (! $snapshot instanceof TrxRowSnapshot) {
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
        return SnapshotAssembler::createSnapshotFrom($this, new TrxRowSnapshot());
    }

    /**
     *
     * @return NULL|object
     */
    public function makeDetailsDTO()
    {
        $dto = new TrxRowDTO();
        $dto = DTOFactory::createDTOFrom($this, $dto);
        return $dto;
    }

    /**
     *
     * @return \Inventory\Domain\Transaction\TrxRow
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new TrxRow();
        }
        return self::$instance;
    }

    public static function createInstance()
    {
        return new self();
    }

    public static function createSnapshotProps()
    {
        $baseClass = "Inventory\Domain\Transaction\BaseRow";
        $entity = new self();
        $reflectionClass = new \ReflectionClass($entity);

        $props = $reflectionClass->getProperties();

        foreach ($props as $property) {
            // echo $property->class . "\n";
            if ($property->class == $reflectionClass->getName() || $property->class == $baseClass) {
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
}
