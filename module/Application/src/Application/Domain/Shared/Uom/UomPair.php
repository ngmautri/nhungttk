<?php
namespace Application\Domain\Shared\Uom;

use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\ValueObject;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
final class UomPair extends ValueObject implements \JsonSerializable
{

    private $baseUomObject;

    private $counterUomObject;

    private $id;

    private $pairName;

    private $baseUom;

    private $counterUom;

    private $convertFactor;

    private $description;

    private $isActive;

    private $remarks;

    private $createdOn;

    private $lastChangeOn;

    private $groupName;

    private $group;

    private $createdBy;

    private $lastChangeBy;

    final public static function createFrom(UomPairSnapshot $snapshot)
    {
        Assert::notNull($snapshot);

        $baseUom = new Uom($snapshot->getBaseUom());
        $counterUom = new Uom($snapshot->getCounterUom());
        $convertFactor = $snapshot->getConvertFactor();
        Assert::numeric($convertFactor);

        $instance = new self($baseUom, $counterUom, $convertFactor);
        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);

        $instance->counterUomObject = $baseUom;
        $instance->counterUomObject = $counterUom;
        $instance->convertFactor = $convertFactor;

        return $instance;
    }

    public function makeSnapshot()
    {
        return SnapshotAssembler::createSnapshotFrom($this, new UomPairSnapshot());
    }

    public function getAttributesToCompare()
    {
        return [
            \strtolower($this->getGroupName()),
            \strtolower($this->getBaseUom()),
            \strtolower($this->getCounterUom()),
            (int) $this->getConvertFactor()
        ];
    }

    public function __toString()
    {
        return \sprintf("%s,%s,%s", $this->getBaseUom(), $this->getCounterUom(), $this->getConvertFactor());
    }

    /**
     *
     * @param Uom $baseUom
     * @param Uom $counterUom
     * @param int $convertFactor
     * @param string $description
     */
    public function __construct(Uom $baseUom, Uom $counterUom, $convertFactor, $description = null)
    {
        Assert::numeric($convertFactor);

        $this->baseUomObject = $baseUom;
        $this->baseUom = $baseUom->getUomName();

        $this->counterUomObject = $counterUom;
        $this->counterUom = $counterUom->getUomName();

        $this->convertFactor = $convertFactor;
        $this->description = $description;

        $this->pairName = \sprintf("%s", $this->baseUom);

        if (! $baseUom->equals($counterUom)) {
            $this->pairName = \sprintf("%s (%s %s)", $this->counterUom, $this->convertFactor, $this->baseUom);
        } else {
            $this->convertFactor = 1;
        }
    }

    /**
     *
     * @param UomPair $other
     * @return boolean
     */
    public function compareTo(UomPair $other)
    {
        return $this->baseUomOject->equals($other->baseUom) && $this->counterUomObject->equals($other->counterUom) && $this->convertFactor === $other->convertFactor;
    }

    public function jsonSerialize()
    {
        return [
            'baseUom' => $this->baseUom,
            'counterUom' => $this->counterUom,
            'convertFactor' => $this->convertFactor
        ];
    }

    /**
     *
     * @return \Application\Domain\Shared\Uom\Uom
     */
    public function getBaseUomObject()
    {
        return $this->baseUomObject;
    }

    /**
     *
     * @return \Application\Domain\Shared\Uom\Uom
     */
    public function getCounterUomObject()
    {
        return $this->counterUomObject;
    }

    /**
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @return string
     */
    public function getPairName()
    {
        return $this->pairName;
    }

    /**
     *
     * @return Ambigous <mixed, string>
     */
    public function getBaseUom()
    {
        return $this->baseUom;
    }

    /**
     *
     * @return Ambigous <mixed, string>
     */
    public function getCounterUom()
    {
        return $this->counterUom;
    }

    /**
     *
     * @return Ambigous <number, int>
     */
    public function getConvertFactor()
    {
        return $this->convertFactor;
    }

    /**
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     *
     * @return mixed
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     *
     * @return mixed
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     *
     * @return mixed
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     *
     * @return mixed
     */
    public function getLastChangeOn()
    {
        return $this->lastChangeOn;
    }

    /**
     *
     * @return mixed
     */
    public function getGroupName()
    {
        return $this->groupName;
    }

    /**
     *
     * @return mixed
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     *
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     *
     * @return mixed
     */
    public function getLastChangeBy()
    {
        return $this->lastChangeBy;
    }
}
