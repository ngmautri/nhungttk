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

    /**
     *
     * @param string $pairName
     */
    private function setPairName($pairName)
    {
        $this->pairName = $pairName;
    }

    /**
     *
     * @param \Application\Domain\Shared\Uom\Uom $baseUom
     */
    private function setBaseUom($baseUom)
    {
        $this->baseUom = $baseUom;
    }

    /**
     *
     * @param \Application\Domain\Shared\Uom\Uom $counterUom
     */
    private function setCounterUom($counterUom)
    {
        $this->counterUom = $counterUom;
    }

    /**
     *
     * @param int $convertFactor
     */
    private function setConvertFactor($convertFactor)
    {
        $this->convertFactor = $convertFactor;
    }

    /**
     *
     * @param string $description
     */
    private function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     *
     * @param mixed $isActive
     */
    private function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     *
     * @param mixed $remarks
     */
    private function setRemarks($remarks)
    {
        $this->remarks = $remarks;
    }

    /**
     *
     * @param mixed $createdOn
     */
    private function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
    }

    /**
     *
     * @param mixed $lastChangeOn
     */
    private function setLastChangeOn($lastChangeOn)
    {
        $this->lastChangeOn = $lastChangeOn;
    }

    /**
     *
     * @param mixed $groupName
     */
    private function setGroupName($groupName)
    {
        $this->groupName = $groupName;
    }

    /**
     *
     * @param mixed $group
     */
    private function setGroup($group)
    {
        $this->group = $group;
    }

    /**
     *
     * @param mixed $createdBy
     */
    private function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     *
     * @param mixed $lastChangeBy
     */
    private function setLastChangeBy($lastChangeBy)
    {
        $this->lastChangeBy = $lastChangeBy;
    }
}
