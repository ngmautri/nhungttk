<?php
namespace Application\Domain\Shared\Uom;

use Application\Domain\Shared\ValueObject;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
final class UomPair extends ValueObject implements \JsonSerializable
{

    private $pairName;

    private $baseUom;

    private $counterUom;

    private $convertFactor;

    private $description;

    public function makeSnapshot()
    {}

    public function getAttributesToCompare()
    {
        return [
            $this->getBaseUom()->getUomName(),
            $this->getCounterUom()->getUomName(),
            $this->getConvertFactor()
        ];
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

        $this->baseUom = $baseUom;
        $this->counterUom = $counterUom;

        $this->convertFactor = $convertFactor;
        $this->description = $description;

        $this->pairName = \sprintf("%s", $this->baseUom);
        if (! $baseUom->equals($counterUom)) {
            $this->pairName = \sprintf("%s (%s %s)", $this->counterUom, $this->convertFactor, $this->baseUom);
        }
    }

    /**
     *
     * @param UomPair $other
     * @return boolean
     */
    public function compareTo(UomPair $other)
    {
        return $this->baseUom->equals($other->baseUom) && $this->counterUom->equals($other->counterUom) && $this->convertFactor === $other->convertFactor;
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
    public function getBaseUom()
    {
        return $this->baseUom;
    }

    /**
     *
     * @return \Application\Domain\Shared\Uom\Uom
     */
    public function getCounterUom()
    {
        return $this->counterUom;
    }

    /**
     *
     * @return int
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
     * @return string
     */
    public function getPairName()
    {
        return $this->pairName;
    }
}
