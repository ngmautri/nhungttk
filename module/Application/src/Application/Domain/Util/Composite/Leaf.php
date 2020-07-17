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
        // return sprintf("<li data-jstree='{}'><span style=\"color:black;\">%s<span></li>\n", $this->getComponentName());
        $format = '<li id="%s" data-jstree="{}"><span style="color:blue;">%s </span> <span style="color:black;">%s</span></li>' . "\n";
        return sprintf($format, $this->getId(), $this->getComponentCode(), $this->getComponentName());
    }

    public function getNumberOfChildren()
    {
        return 1;
    }
}
