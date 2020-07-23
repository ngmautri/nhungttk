<?php
namespace Application\Domain\Util\Tree\Output;

use Application\Domain\Util\Tree\Node\AbstractBaseNode;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class SimpleFormatter extends AbstractFormatter
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

            $format = '[+] %s %s (%s)' . "\n";
            $results = $results . sprintf($format, $node->getNodeName(), $node->getNodeDescription(), $node->getChildCount());
            $results = $results . "<br>";

            foreach ($node->getChildren() as $child) {
                // recursive
                $space = '';
                for ($i = 0; $i <= $level; $i ++) {
                    $space = $space . "&nbsp;&nbsp";
                }
                $results = $results . $space . $child->display($this, $level + 1);
            }
        } else {
            $space = '';
            for ($i = 0; $i <= $level; $i ++) {
                $space = $space . "&nbsp;&nbsp;";
            }
            $format = '- %s %s' . "\n";
            $results = $results . $space . sprintf($format, $node->getNodeCode(), $node->getNodeName());
            $results = $results . "<br>";
        }

        return $results;
    }
}
