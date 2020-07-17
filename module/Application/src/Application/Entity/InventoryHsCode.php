<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InventoryHsCode
 *
 * @ORM\Table(name="inventory_hs_code")
 * @ORM\Entity
 */
class InventoryHsCode
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="parent_id", type="integer", nullable=true)
     */
    private $parentId;

    /**
     * @var integer
     *
     * @ORM\Column(name="code_order", type="integer", nullable=true)
     */
    private $codeOrder;

    /**
     * @var integer
     *
     * @ORM\Column(name="level", type="integer", nullable=true)
     */
    private $level;

    /**
     * @var string
     *
     * @ORM\Column(name="hs_code", type="string", length=100, nullable=true)
     */
    private $hsCode;

    /**
     * @var string
     *
     * @ORM\Column(name="parent_code", type="string", length=100, nullable=true)
     */
    private $parentCode;

    /**
     * @var string
     *
     * @ORM\Column(name="hs_code_1", type="string", length=100, nullable=true)
     */
    private $hsCode1;

    /**
     * @var string
     *
     * @ORM\Column(name="parent_code_1", type="string", length=100, nullable=true)
     */
    private $parentCode1;

    /**
     * @var string
     *
     * @ORM\Column(name="code_description", type="text", length=65535, nullable=true)
     */
    private $codeDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="code_description_1", type="text", length=65535, nullable=true)
     */
    private $codeDescription1;

    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="string", length=45, nullable=true)
     */
    private $reference;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255, nullable=true)
     */
    private $path;

    /**
     * @var integer
     *
     * @ORM\Column(name="path_depth", type="integer", nullable=true)
     */
    private $pathDepth;

    /**
     * @var boolean
     *
     * @ORM\Column(name="has_member", type="boolean", nullable=true)
     */
    private $hasMember;

    /**
     * @var string
     *
     * @ORM\Column(name="hs_code_2", type="string", length=100, nullable=true)
     */
    private $hsCode2;

    /**
     * @var string
     *
     * @ORM\Column(name="hs_code_3", type="string", length=100, nullable=true)
     */
    private $hsCode3;

    /**
     * @var string
     *
     * @ORM\Column(name="hs_code_4", type="string", length=100, nullable=true)
     */
    private $hsCode4;

    /**
     * @var string
     *
     * @ORM\Column(name="code_description_2", type="text", length=65535, nullable=true)
     */
    private $codeDescription2;



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set parentId
     *
     * @param integer $parentId
     *
     * @return InventoryHsCode
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * Get parentId
     *
     * @return integer
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Set codeOrder
     *
     * @param integer $codeOrder
     *
     * @return InventoryHsCode
     */
    public function setCodeOrder($codeOrder)
    {
        $this->codeOrder = $codeOrder;

        return $this;
    }

    /**
     * Get codeOrder
     *
     * @return integer
     */
    public function getCodeOrder()
    {
        return $this->codeOrder;
    }

    /**
     * Set level
     *
     * @param integer $level
     *
     * @return InventoryHsCode
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return integer
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set hsCode
     *
     * @param string $hsCode
     *
     * @return InventoryHsCode
     */
    public function setHsCode($hsCode)
    {
        $this->hsCode = $hsCode;

        return $this;
    }

    /**
     * Get hsCode
     *
     * @return string
     */
    public function getHsCode()
    {
        return $this->hsCode;
    }

    /**
     * Set parentCode
     *
     * @param string $parentCode
     *
     * @return InventoryHsCode
     */
    public function setParentCode($parentCode)
    {
        $this->parentCode = $parentCode;

        return $this;
    }

    /**
     * Get parentCode
     *
     * @return string
     */
    public function getParentCode()
    {
        return $this->parentCode;
    }

    /**
     * Set hsCode1
     *
     * @param string $hsCode1
     *
     * @return InventoryHsCode
     */
    public function setHsCode1($hsCode1)
    {
        $this->hsCode1 = $hsCode1;

        return $this;
    }

    /**
     * Get hsCode1
     *
     * @return string
     */
    public function getHsCode1()
    {
        return $this->hsCode1;
    }

    /**
     * Set parentCode1
     *
     * @param string $parentCode1
     *
     * @return InventoryHsCode
     */
    public function setParentCode1($parentCode1)
    {
        $this->parentCode1 = $parentCode1;

        return $this;
    }

    /**
     * Get parentCode1
     *
     * @return string
     */
    public function getParentCode1()
    {
        return $this->parentCode1;
    }

    /**
     * Set codeDescription
     *
     * @param string $codeDescription
     *
     * @return InventoryHsCode
     */
    public function setCodeDescription($codeDescription)
    {
        $this->codeDescription = $codeDescription;

        return $this;
    }

    /**
     * Get codeDescription
     *
     * @return string
     */
    public function getCodeDescription()
    {
        return $this->codeDescription;
    }

    /**
     * Set codeDescription1
     *
     * @param string $codeDescription1
     *
     * @return InventoryHsCode
     */
    public function setCodeDescription1($codeDescription1)
    {
        $this->codeDescription1 = $codeDescription1;

        return $this;
    }

    /**
     * Get codeDescription1
     *
     * @return string
     */
    public function getCodeDescription1()
    {
        return $this->codeDescription1;
    }

    /**
     * Set reference
     *
     * @param string $reference
     *
     * @return InventoryHsCode
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return InventoryHsCode
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set pathDepth
     *
     * @param integer $pathDepth
     *
     * @return InventoryHsCode
     */
    public function setPathDepth($pathDepth)
    {
        $this->pathDepth = $pathDepth;

        return $this;
    }

    /**
     * Get pathDepth
     *
     * @return integer
     */
    public function getPathDepth()
    {
        return $this->pathDepth;
    }

    /**
     * Set hasMember
     *
     * @param boolean $hasMember
     *
     * @return InventoryHsCode
     */
    public function setHasMember($hasMember)
    {
        $this->hasMember = $hasMember;

        return $this;
    }

    /**
     * Get hasMember
     *
     * @return boolean
     */
    public function getHasMember()
    {
        return $this->hasMember;
    }

    /**
     * Set hsCode2
     *
     * @param string $hsCode2
     *
     * @return InventoryHsCode
     */
    public function setHsCode2($hsCode2)
    {
        $this->hsCode2 = $hsCode2;

        return $this;
    }

    /**
     * Get hsCode2
     *
     * @return string
     */
    public function getHsCode2()
    {
        return $this->hsCode2;
    }

    /**
     * Set hsCode3
     *
     * @param string $hsCode3
     *
     * @return InventoryHsCode
     */
    public function setHsCode3($hsCode3)
    {
        $this->hsCode3 = $hsCode3;

        return $this;
    }

    /**
     * Get hsCode3
     *
     * @return string
     */
    public function getHsCode3()
    {
        return $this->hsCode3;
    }

    /**
     * Set hsCode4
     *
     * @param string $hsCode4
     *
     * @return InventoryHsCode
     */
    public function setHsCode4($hsCode4)
    {
        $this->hsCode4 = $hsCode4;

        return $this;
    }

    /**
     * Get hsCode4
     *
     * @return string
     */
    public function getHsCode4()
    {
        return $this->hsCode4;
    }

    /**
     * Set codeDescription2
     *
     * @param string $codeDescription2
     *
     * @return InventoryHsCode
     */
    public function setCodeDescription2($codeDescription2)
    {
        $this->codeDescription2 = $codeDescription2;

        return $this;
    }

    /**
     * Get codeDescription2
     *
     * @return string
     */
    public function getCodeDescription2()
    {
        return $this->codeDescription2;
    }
}
