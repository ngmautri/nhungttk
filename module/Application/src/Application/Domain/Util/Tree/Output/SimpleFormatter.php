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
        if ($level == 5) {
            return "";
        }

        $results = '';

        $space = "";
        for ($i = 0; $i <= $level; $i ++) {
            $space = $space . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        }

        if (! $node->isLeaf()) {

            $format = '<span style="color:black">[+%s] %s %s (%s)' . "\n </br></span>";

            if ($level == 1) {
                $format = '<span style="color:navy; font-weight: bold;">[+%s] %s %s (%s)' . "\n </br></span>";
                ;
            }

            if ($level == 2) {
                $format = '<span style="color:navy">[+%s] %s %s (%s)' . "\n </br></span>";
            }

            $results = $results . $space . sprintf($format, "", $node->getNodeCode(), $node->getNodeDescription(), $node->getChildCount() - 1);

            foreach ($node->getChildren() as $child) {
                // recursive
                $results = $results . $this->format($child, $level + 1);
            }
        } else {
            $format = '<span style="color:black">%s %s %s' . "\n </br></span>";
            $results = $results . $space . sprintf($format, "", $node->getNodeCode(), $node->getNodeName());
        }

        return $results;
    }
}
