<?php
namespace Application\Domain\Util\Composite;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class Leaf extends AbstractComponent
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Utility\Composite\AbstractComponent::operation()
     */
    public function operation()
    {
        return "Leaf";
    }

    public function generateJsTree()
    {
        return sprintf("<li data-jstree='{}'>%s</li>\n", "leaf");
    }

    public function getNumberOfChildren()
    {
        return 1;
    }
}
