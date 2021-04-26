<?php
namespace Application\Application\Service\AccountChart\Tree\Output;

use Application\Domain\Util\Tree\Node\AbstractBaseNode;
use Application\Domain\Util\Tree\Output\AbstractFormatter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class AccountJsTreeFormatter extends AbstractFormatter
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

            $format = '<li id="%s" data-jstree="{}">%s - %s (%s)' . "\n";

            $results = $results . sprintf($format, $node->getNodeCode(), $node->getNodeCode(), $node->getNodeName(), $node->getChildCount() - 1);

            $results = $results . sprintf("<ul>\n");

            foreach ($node->getChildren() as $child) {
                // recursive
                $results = $results . $child->display($this);
            }

            $results = $results . sprintf("</ul>\n");
            $results = $results . sprintf("</li>\n");
        } else {
            $format = '<li id="%s" data-jstree="{}">%s - %s</li>' . "\n";
            $results = $results . sprintf($format, $node->getNodeCode(), $node->getNodeCode(), $node->getNodeName());
        }

        return $results;
    }
}
