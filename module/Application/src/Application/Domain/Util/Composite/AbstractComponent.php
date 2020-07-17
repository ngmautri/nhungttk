<?php
namespace Application\Domain\Util\Composite;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractComponent
{

    /**
     *
     * @var AbstractComponent;
     */
    protected $parent;

    protected $componentName;

    protected $components;

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
     * @param mixed $componentName
     */
    public function setComponentName($componentName)
    {
        $this->componentName = $componentName;
    }

    abstract public function operation();

    abstract public function generateJsTree();

    abstract public function getNumberOfChildren();

    /**
     *
     * @param AbstractComponent $parent
     */
    public function setParent(AbstractComponent $parent)
    {
        $this->parent = $parent;
    }

    /**
     *
     * @return AbstractComponent
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     *
     * @param AbstractComponent $component
     */
    public function add(AbstractComponent $component)
    {}

    /**
     *
     * @param AbstractComponent $component
     */
    public function remove(AbstractComponent $component)
    {}

    /**
     *
     * @return bool
     */
    public function isComposite()
    {
        return false;
    }
}
