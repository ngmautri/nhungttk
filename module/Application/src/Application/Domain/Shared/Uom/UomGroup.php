<?php
namespace Application\Domain\Shared\Uom;

use Application\Domain\Shared\SnapshotAssembler;
use Webmozart\Assert\Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
final class UomGroup extends BaseUomGroup implements \JsonSerializable
{

    private $baseUomObject;

    private $members;

    public function addUomPair(UomPair $pair)
    {
        $this->getMembers()->add($pair);
    }

    /**
     *
     * @param UomGroupSnapshot $snapshot
     * @return \Application\Domain\Shared\Uom\UomGroup
     */
    public static function createFrom(UomGroupSnapshot $snapshot)
    {
        $instance = new UomGroup('tmp', new Uom('tmp'));
        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);

        Assert::stringNotEmpty($instance->getGroupName());
        $baseUom = new Uom($instance->getBaseUom());
        $instance->baseUomObject = $baseUom;
        return $instance;
    }

    /**
     *
     * @param array $data
     * @return object
     */
    public static function createFromArray($data)
    {
        $instance = new UomGroup('tmp', new Uom('tmp'));
        SnapshotAssembler::makeFromArray($instance, $data);

        Assert::stringNotEmpty($instance->getGroupName());
        $instance->groupName = trim(\strtolower($instance->getGroupName()));
        $baseUom = new Uom($instance->getBaseUom());
        $instance->baseUomObject = $baseUom;

        return $instance;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\AbstractValueObject::makeSnapshot()
     */
    public function makeSnapshot()
    {
        return SnapshotAssembler::createSnapshotFrom($this, new UomGroupSnapshot());
    }

    /**
     *
     * @param string $groupName
     * @param Uom $baseUom
     */
    public function __construct($groupName, Uom $baseUom)
    {
        Assert::stringNotEmpty($groupName);
        Assert::maxLength($groupName, 45);
        Assert::notNull($baseUom);

        // ignore case incentive.
        $this->groupName = trim(\strtolower($groupName));
        $this->baseUomObject = $baseUom;
    }

    /**
     *
     * @return string
     */
    public function __toString()
    {
        return \strtolower($this->groupName);
    }

    /**
     *
     * {@inheritdoc}
     *
     * @return string
     */
    public function jsonSerialize()
    {
        return \strtolower($this->groupName);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\AbstractValueObject::getAttributesToCompare()
     */
    public function getAttributesToCompare()
    {
        return [
            \strtolower($this->groupName)
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
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getMembers()
    {
        if ($this->members != null) {
            return $this->members;
        }

        return new ArrayCollection();
    }
}
