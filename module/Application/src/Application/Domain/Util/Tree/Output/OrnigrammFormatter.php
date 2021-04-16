<?php
namespace Application\Domain\Util\Tree\Output;

use Application\Domain\Util\Tree\Node\AbstractBaseNode;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class OrnigrammFormatter extends AbstractFormatter
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Util\Tree\Output\AbstractFormatter::format()
     */
    public function format(AbstractBaseNode $node, $level = 0)

    {
        $results = '';
        // $results = $results . sprintf("<li data-jstree='{ \"opened\" : true}'>%s %s\n", "", $this->getComponentName(), $this->getNumberOfChildren());

        if (! $node->isLeaf()) {

            $format = '<li id="%s" "><a href="#"><span style="color:blue;">%s</span> %s <span style="color: blue; font-size: 11pt;">%s</span></a>' . "\n";
            $results = $results . sprintf($format, $node->getId(), "", $node->getNodeName(), "");
            $results = $results . sprintf("<ul>\n");

            foreach ($node->getChildren() as $child) {
                // recursive
                $results = $results . $child->display($this);
            }

            $results = $results . sprintf("</ul>\n");
            $results = $results . sprintf("</li>\n");
        } else {
            $format = '<li id="%s""><a href="#"><span style="color:black;">%s </span> <span style="color:black;">%s</span></a></li>' . "\n";
            $results = $results . sprintf($format, $node->getId(), "", $node->getNodeName());
        }

        $results;
        return $results;
    }
}
