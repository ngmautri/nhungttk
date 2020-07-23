<?php
namespace Application\Domain\Util\Composite;

use SplObjectStorage;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class Composite extends AbstractBaseComponent
{

    /**
     *
     * @var \SplObjectStorage
     */
    protected $children;

    /**
     *
     * @return SplObjectStorage
     */
    public function getChildren()
    {
        return $this->children;
    }

    public function __construct()
    {
        $this->children = new \SplObjectStorage();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Utility\Composite\AbstractComponent::add()
     */
    public function add(AbstractComponent $component)
    {
        $component->setParent($this);

        if ($this->has($component)) {
            throw new \InvalidArgumentException("Child exits already! " . $component->getComponentName());
        }

        $this->children->attach($component);
    }

    public function has(AbstractComponent $component)
    {
        foreach ($this->getChildren() as $child) {

            if ($child == $component) {
                return true;
            }
        }
        return false;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Utility\Composite\AbstractComponent::remove()
     */
    public function remove(AbstractComponent $component)
    {
        $this->children->detach($component);
        $component->setParent(null);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Utility\Composite\AbstractComponent::isComposite()
     */
    public function isComposite()
    {
        return true;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Utility\Composite\AbstractComponent::getNumberOfChildren()
     */
    public function getNumberOfChildren()
    {
        $total = 1;
        foreach ($this->children as $child) {
            $total = $total + $child->getNumberOfChildren();
        }

        return $total;
    }
}
