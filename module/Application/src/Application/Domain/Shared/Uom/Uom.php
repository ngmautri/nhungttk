<?php
namespace Application\Domain\Shared\Uom;

use Application\Domain\Shared\SnapshotAssembler;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
final class Uom extends BaseUom implements \JsonSerializable
{
    use UomFactory;

    private $alias;

    /**
     *
     * @param string $uomName
     * @throws \InvalidArgumentException
     */
    public function __construct($uomName, $ctx = null)
    {
        // ignore case incentive.
        $this->uomName = trim(\strtolower($uomName));
        // $this->uomCode = trim(\strtolower($uomCode));
        Assert::stringNotEmpty($uomName, \sprintf('Uom name empty! %s %s', $uomName, $ctx));
        Assert::maxLength($uomName, 45);
    }

    /**
     *
     * @param UomSnapshot $snapshot
     * @return \Application\Domain\Shared\Uom\Uom
     */
    final public static function createFrom(UomSnapshot $snapshot)
    {
        $instance = new self('temp');
        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);
        return static::assertInstance($instance);
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
        return static::assertInstance($instance);
    }

    private static function assertInstance(Uom $instance)
    {
        $instance->uomName = trim(\strtolower($instance->getUomName()));
        // $instance->uomCode = trim(\strtolower($instance->getUomCode()));
        Assert::stringNotEmpty($instance->getUomName());
        Assert::maxLength($instance->getUomName(), 45);
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
