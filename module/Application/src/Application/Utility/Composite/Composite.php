<?php
namespace Application\Utility\Composite;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class Composite extends AbstractComponent
{

    /**
     *
     * @var \SplObjectStorage
     */
    protected $children;

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
        $this->children->attach($component);
        $component->setParent($this);
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
     * @see \Application\Utility\Composite\AbstractComponent::operation()
     */
    public function operation()
    {
        $results = [];
        foreach ($this->children as $child) {
            $results[] = $child->operation();
        }
        return "Branch(" . implode("+", $results) . ")";
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Utility\Composite\AbstractComponent::generateJsTree()
     */
    public function generateJsTree()
    {
        $results = '';

        $results = $results . sprintf("<li data-jstree='{ \"opened\" : true}'>%s\n", "branch-" . $this->getNumberOfChildren());
        $results = $results . sprintf("<ul>\n");

        foreach ($this->children as $child) {
            $results = $results . $child->generateJsTree();
        }

        $results = $results . sprintf("</ul>\n");
        $results = $results . sprintf("</li>\n");

        return $results;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Utility\Composite\AbstractComponent::getNumberOfChildren()
     */
    public function getNumberOfChildren()
    {
        $total = 01;
        foreach ($this->children as $child) {
            $total = $total + $child->getNumberOfChildren();
        }

        return $total;
    }
}
