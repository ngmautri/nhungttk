<?php
namespace Inventory\Domain\Transaction;

use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Inventory\Application\DTO\Transaction\TrxRowDTO;
use Procure\Domain\GenericDoc;

/**
 * Transaction Row
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class TrxRow extends BaseRow
{

    private static $instance = null;

    // Adddtional Attributes, if needed
    // ====================

    // ===================
    public static function createFromSnapshot(GenericDoc $rootDoc, TrxRowSnapshot $snapshot)
    {
        if (! $snapshot instanceof TrxRowSnapshot) {
            return null;
        }

        $instance = new self();
        GenericObjectAssembler::updateAllFieldsFrom($instance, $snapshot);
        $instance->createVO($rootDoc);
        return $instance;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GenericRow::createVO()
     */
    protected function createVO(GenericDoc $rootDoc)
    {}

    public function setCalculatedCost($cogs)
    {
        $this->setCogsLocal($cogs);
    }

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
}
