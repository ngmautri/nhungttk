<?php
namespace Application\Domain\Shared\PaymentTerm;

use Application\Domain\Shared\AbstractValueObject;
use Application\Domain\Shared\SnapshotAssembler;
use Procure\Domain\AccountPayable\APSnapshot;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
final class PaymentTerm extends AbstractValueObject implements \JsonSerializable
{

    private $uomCode;

    private $uomName;

    private $symbol;

    private $alias;

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
        $instance->alias = $snapshot->getAlias();
        return $instance;
    }

    public function makeSnapshot()
    {
        return SnapshotAssembler::createSnapshotFrom($this, new UomSnapshot());
    }
    /**
     *
     * @param string $uomName
     * @throws \InvalidArgumentException
     */
    public function __construct($uomName, $uomCode=null)
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

    /**
     *
     * @return mixed
     */
    public function getAlias()
    {
        return $this->alias;
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
