<?php
namespace Application\Domain\Shared\Uom;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
final class Uom implements \JsonSerializable
{
    use UomFactory;

    private $uomCode;

    private $uomName;

    private $symbol;

    /**
     *
     * @param UomSnapshot $snapshot
     * @return \Application\Domain\Shared\Uom\Uom
     */
    final public static function createFrom(UomSnapshot $snapshot)
    {
        $instance = new self($snapshot->getUomName());
        $instance->symbol = $snapshot->getSymbol();
        $instance->uomCode = $snapshot->getUomName();
        return $instance;
    }

    /**
     *
     * @param string $uomName
     * @throws \InvalidArgumentException
     */
    public function __construct($uomName)
    {
        if (! is_string($uomName)) {
            throw new \InvalidArgumentException('Uom code should be string');
        }

        if ($uomName === '') {
            throw new \InvalidArgumentException('Uom code should not be empty string');
        }

        $this->uomName = \strtolower($uomName);
    }

    /**
     *
     * @param Uom $other
     * @return boolean
     */
    public function equals(Uom $other)
    {
        return $this->uomName == $other->uomName;
    }

    /**
     *
     * @return string
     */
    public function __toString()
    {
        return $this->code;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->code;
    }

    /**
     *
     * @return mixed
     */
    public function getUomCode()
    {
        return $this->uomCode;
    }

    /**
     *
     * @return string
     */
    public function getUomName()
    {
        return $this->uomName;
    }

    /**
     *
     * @return mixed
     */
    public function getSymbol()
    {
        return $this->symbol;
    }
}
