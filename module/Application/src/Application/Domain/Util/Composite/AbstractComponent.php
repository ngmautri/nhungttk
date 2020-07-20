<?php
namespace Application\Domain\Util\Composite;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractComponent
{

    protected $id;

    protected $parenId;

    protected $level;

    protected $componentCode;

    protected $componentCode1;

    protected $componentLabel;

    protected $componentLabel1;

    protected $componentName;

    protected $componentName1;

    protected $componentDescription;

    protected $componentDescription1;

    protected $parentCode;

    protected $parentLabel;

    /**
     *
     * @var AbstractComponent;
     */
    protected $parent;

    protected $components;

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
     * @return mixed
     */
    public function getParenId()
    {
        return $this->parenId;
    }

    /**
     *
     * @return mixed
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     *
     * @return mixed
     */
    public function getComponentCode()
    {
        return $this->componentCode;
    }

    /**
     *
     * @return mixed
     */
    public function getComponentCode1()
    {
        return $this->componentCode1;
    }

    /**
     *
     * @return mixed
     */
    public function getComponentLabel()
    {
        return $this->componentLabel;
    }

    /**
     *
     * @return mixed
     */
    public function getComponentLabel1()
    {
        return $this->componentLabel1;
    }

    /**
     *
     * @return mixed
     */
    public function getComponentName()
    {
        return $this->componentName;
    }

    /**
     *
     * @return mixed
     */
    public function getComponentName1()
    {
        return $this->componentName1;
    }

    /**
     *
     * @return mixed
     */
    public function getComponentDescription()
    {
        return $this->componentDescription;
    }

    /**
     *
     * @return mixed
     */
    public function getComponentDescription1()
    {
        return $this->componentDescription1;
    }

    /**
     *
     * @return mixed
     */
    public function getParentCode()
    {
        return $this->parentCode;
    }

    /**
     *
     * @return mixed
     */
    public function getParentLabel()
    {
        return $this->parentLabel;
    }

    /**
     *
     * @return \Application\Domain\Util\Composite\AbstractComponent;
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     *
     * @return mixed
     */
    public function getComponents()
    {
        return $this->components;
    }

    /**
     *
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @param mixed $parenId
     */
    public function setParenId($parenId)
    {
        $this->parenId = $parenId;
    }

    /**
     *
     * @param mixed $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     *
     * @param mixed $componentCode
     */
    public function setComponentCode($componentCode)
    {
        $this->componentCode = $componentCode;
    }

    /**
     *
     * @param mixed $componentCode1
     */
    public function setComponentCode1($componentCode1)
    {
        $this->componentCode1 = $componentCode1;
    }

    /**
     *
     * @param mixed $componentLabel
     */
    public function setComponentLabel($componentLabel)
    {
        $this->componentLabel = $componentLabel;
    }

    /**
     *
     * @param mixed $componentLabel1
     */
    public function setComponentLabel1($componentLabel1)
    {
        $this->componentLabel1 = $componentLabel1;
    }

    /**
     *
     * @param mixed $componentName
     */
    public function setComponentName($componentName)
    {
        $this->componentName = $componentName;
    }

    /**
     *
     * @param mixed $componentName1
     */
    public function setComponentName1($componentName1)
    {
        $this->componentName1 = $componentName1;
    }

    /**
     *
     * @param mixed $componentDescription
     */
    public function setComponentDescription($componentDescription)
    {
        $this->componentDescription = $componentDescription;
    }

    /**
     *
     * @param mixed $componentDescription1
     */
    public function setComponentDescription1($componentDescription1)
    {
        $this->componentDescription1 = $componentDescription1;
    }

    /**
     *
     * @param mixed $parentCode
     */
    public function setParentCode($parentCode)
    {
        $this->parentCode = $parentCode;
    }

    /**
     *
     * @param mixed $parentLabel
     */
    public function setParentLabel($parentLabel)
    {
        $this->parentLabel = $parentLabel;
    }

    /**
     *
     * @param \Application\Domain\Util\Composite\AbstractComponent; $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     *
     * @param mixed $components
     */
    public function setComponents($components)
    {
        $this->components = $components;
    }
}
