<?php
namespace Application\Domain\Shared\Uom;

use Application\Domain\Shared\SnapshotAssembler;
use Webmozart\Assert\Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\Uuid;
use Application\Infrastructure\Persistence\Contracts\CompositeCrudRepositoryInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
final class UomGroup extends BaseUomGroup implements \JsonSerializable
{

    private $baseUomObject;

    private $members;

    private $companyId;

    public function addUomPair(UomPair $pair)
    {
        Assert::notNull($pair);
        if ($this->getMembers()->contains($pair)) {
            throw new \InvalidArgumentException(\sprintf("UOM exits [%s]", $pair->__toString()));
        }

        if (! $pair->getBaseUomObject()->equals($this->getBaseUomObject())) {
            throw new \InvalidArgumentException(\sprintf("Base UoM not valid %s", ""));
        }

        if (! $pair->getBaseUomObject()->equals($this->getBaseUomObject())) {
            throw new \InvalidArgumentException(\sprintf("Base UoM not valid %s", ""));
        }

        $this->getMembers()->add($pair);
    }

    /**
     *
     * @param UomGroupSnapshot $snapshot
     * @return \Application\Domain\Shared\Uom\UomGroup
     */
    public function createPairFrom(UomPairSnapshot $snapshot, CompositeCrudRepositoryInterface $repository)
    {
        Assert::notNull($repository, 'Repsository  not found!');
        Assert::notNull($snapshot, 'Input data not found!');

        $snapshot->groupName = $this->getGroupName();
        $snapshot->baseUom = $this->getBaseUom();
        $pair = UomPair::createFrom($snapshot);
        $this->addUomPair($pair);
        $repository->saveMember($this, $pair);
    }

    public static function createFrom(UomGroupSnapshot $snapshot)
    {
        $baseUom = new Uom($snapshot->baseUom);
        $instance = new self($snapshot->groupName, $baseUom);
        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);

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
        $this->baseUom = $baseUom->getUomName();
        $this->uuid = Uuid::uuid4()->toString();
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
        if ($this->members == null) {
            $this->members = new ArrayCollection();
        }

        return $this->members;
    }
}
