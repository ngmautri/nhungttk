<?php
namespace Application\Application\Service\Department\Tree\Output;

use Application\Domain\Company\Contracts\DefaultDepartment;
use Application\Domain\Util\Tree\Node\AbstractBaseNode;
use Application\Domain\Util\Tree\Output\AbstractFormatter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class DepartmentJsTreeFormatter extends AbstractFormatter
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

            $format = '<li id="%s" data-jstree="{}"><span style="color:blue;">%s</span> %s (<span style="color: blue; font-size: 11pt;">%s</span>)' . "\n";

            if ($node->getNodeName() == DefaultDepartment::ROOT) {

                $data_jstree = 'data-jstree=\'{ "opened" : true, "disabled":true}\'';
                $format = '<li %s  id="%s"><span style="color:blue;">%s</span> %s (<span style="color: blue; font-size: 11pt;">%s</span>)' . "\n";

                $results = $results . sprintf($format, $data_jstree, "", "", "DEPARTMENT", $node->getChildCount() - 1);
            } else {
                $results = $results . sprintf($format, "", "", $node->getNodeName(), $node->getChildCount() - 1);
            }

            $results = $results . sprintf("<ul>\n");

            foreach ($node->getChildren() as $child) {
                // recursive
                $results = $results . $child->display($this);
            }

            $results = $results . sprintf("</ul>\n");
            $results = $results . sprintf("</li>\n");
        } else {
            $format = '<li id="%s" data-jstree="{}"><span style="color:black;">%s </span> <span style="color:black;">%s</span></li>' . "\n";
            $results = $results . sprintf($format, "", "", $node->getNodeName());
        }

        return $results;
    }
}
