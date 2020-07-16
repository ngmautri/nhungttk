<?php
namespace Application\Domain\Util\Composite\Output;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AbstractOutput
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Utility\Composite\AbstractComponent::operation()
     */
    public function operation()
    {
        return "AbstractOutput";
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
