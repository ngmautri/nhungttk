<?php
namespace Application\Domain\Shared\Uom;

use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\ValueObjectInterface;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
final class Uom extends BaseUom implements \JsonSerializable, ValueObjectInterface
{
    use UomFactory;

    private $alias;

    /**
     *
     * @param UomSnapshot $snapshot
     * @return \Application\Domain\Shared\Uom\Uom
     */
    final public static function createFrom(UomSnapshot $snapshot)
    {
        $instance = new self($snapshot->getUomName());
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
        $instance = new self("tmp");
        $instance = SnapshotAssembler::makeFromArray($instance, $data);

        Assert::stringNotEmpty($instance->getUomName());
        Assert::stringNotEmpty($instance->getUomCode());
        $instance->uomName = trim(\strtolower($instance->getUomName()));
        $instance->uomCode = trim(\strtolower($instance->getUomCode()));
        return $instance;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\AbstractValueObject::makeSnapshot()
     */
    public function makeSnapshot()
    {
        return SnapshotAssembler::createSnapshotFrom($this, new UomSnapshot());
    }

    /**
     *
     * @param string $uomName
     * @throws \InvalidArgumentException
     */
    public function __construct($uomName, $uomCode = null)
    {
        Assert::stringNotEmpty($uomName);

        // ignore case incentive.
        $this->uomName = trim(\strtolower($uomName));
        $this->uomCode = trim(\strtolower($uomCode));
    }

    /**
     *
     * @return string
     */
    public function __toString()
    {
        return $this->uomName;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->uomName;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\AbstractValueObject::getAttributesToCompare()
     */
    public function getAttributesToCompare()
    {
        return [
            $this->uomName
        ];
    }
}
