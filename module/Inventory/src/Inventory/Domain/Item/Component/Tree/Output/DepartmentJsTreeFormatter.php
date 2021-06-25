<?php
namespace Inventory\Domain\Component\Tree\Output;

use Application\Domain\Company\Contracts\DefaultDepartment;
use Application\Domain\Util\Tree\Node\AbstractBaseNode;
use Application\Domain\Util\Tree\Output\AbstractFormatter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ComponentJsTreeFormatter extends AbstractFormatter
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

            $format = '<li id="%s" data-jstree="{}">%s %s (%s)' . "\n";

            if ($node->getNodeName() == DefaultDepartment::ROOT) {

                $data_jstree = 'data-jstree=\'{ "opened" : true, "disabled":true}\'';
                $format = '<li %s  id="%s">%s %s (%s)' . "\n";

                $d = '<span style="color:navy; font-weight: bold;">DEPARTMENT <i class="fa fa-sitemap" aria-hidden="true"></i></span>';

                $results = $results . sprintf($format, $data_jstree, $node->getNodeName(), "", $d, $node->getChildCount() - 1);
            } else {
                $results = $results . sprintf($format, $node->getNodeName(), "", $node->getNodeName(), $node->getChildCount() - 1);
            }

            $results = $results . sprintf("<ul>\n");

            foreach ($node->getChildren() as $child) {
                // recursive
                $results = $results . $child->display($this);
            }

            $results = $results . sprintf("</ul>\n");
            $results = $results . sprintf("</li>\n");
        } else {
            $format = '<li id="%s" data-jstree="{}">%s %s</li>' . "\n";
            $results = $results . sprintf($format, $node->getNodeName(), "", $node->getNodeName());
        }

        return $results;
    }
}
