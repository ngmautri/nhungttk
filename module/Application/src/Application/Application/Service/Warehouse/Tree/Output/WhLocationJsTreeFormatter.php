<?php
namespace Application\Application\Service\Warehouse\Tree\Output;

use Application\Domain\Util\Tree\Node\AbstractBaseNode;
use Application\Domain\Util\Tree\Output\AbstractFormatter;
use Inventory\Domain\Warehouse\Contracts\DefaultLocation;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class WhLocationJsTreeFormatter extends AbstractFormatter
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

            $format = '<li %s id="%s">%s' . "\n";
            $data_jstree = 'data-jstree=\'{ "opened" : false, "type":"demo"}\'';

            $f = '%s %s (%s)';
            $t = \sprintf($f, $node->getNodeCode(), $node->getNodeName(), $node->getChildCount() - 1);

            if ($level <= 1) {

                $data_jstree = 'data-jstree=\'{ "opened" : true, "disabled":false}\'';
                $f = '<span style="color:navy; font-weight: normal;">%s - %s (%s)</span>';
                $t = \sprintf($f, $node->getNodeCode(), \strtoupper($node->getNodeName()), $node->getChildCount() - 1);
            }

            if ($level == 2) {

                $data_jstree = 'data-jstree=\'{ "opened" : false, "disabled":false}\'';
                $f = '<span style="color:navy; font-weight: normal;">%s - %s (%s)</span>';
                $t = \sprintf($f, $node->getNodeCode(), $node->getNodeName(), $node->getChildCount() - 1);
            }

            if ($node->getNodeCode() == DefaultLocation::RECYCLE_LOCATION) {

                $data_jstree = 'data-jstree=\'{ "opened" : false, "disabled":false, "type":"recycle"}\'';
                $f = '<span style="color:green; font-weight: normal;">%s - %s (%s)</span>';
                $t = \sprintf($f, $node->getNodeCode(), $node->getNodeName(), $node->getChildCount() - 1);
            }

            if ($node->getNodeCode() == DefaultLocation::ROOT_LOCATION) {

                $data_jstree = 'data-jstree=\'{ "opened" : false, "disabled":false, "type":"root"}\'';
                $f = '<span style="color:navy; font-weight: normal;">%s - %s (%s)</span>';
                $t = \sprintf($f, $node->getNodeCode(), $node->getNodeName(), $node->getChildCount() - 1);
            }

            if ($node->getNodeCode() == DefaultLocation::SCRAP_LOCATION) {

                $data_jstree = 'data-jstree=\'{ "opened" : false, "disabled":false, "type":"trash"}\'';
                $f = '<span style="color:gray; font-weight: normal;">%s - %s (%s)</span>';
                $t = \sprintf($f, $node->getNodeCode(), $node->getNodeName(), $node->getChildCount() - 1);
            }

            $results = $results . sprintf($format, $data_jstree, $node->getNodeCode(), $t);

            $results = $results . sprintf("<ul>\n");

            foreach ($node->getChildren() as $child) {
                // recursive
                $results = $results . $child->display($this, $level + 1);
            }

            $results = $results . sprintf("</ul>\n");
            $results = $results . sprintf("</li>\n");
        } else {
            $format = '<li %s id="%s">%s' . "\n";
            $data_jstree = 'data-jstree=\'{ "opened" : false, "type":"demo"}\'';
            $f = '<span style="color:black; font-weight: normal; font-style: italic;">%s - %s</span>';
            $t = \sprintf($f, $node->getNodeCode(), $node->getNodeName());

            if ($node->getNodeCode() == DefaultLocation::RECYCLE_LOCATION) {

                $data_jstree = 'data-jstree=\'{ "opened" : false, "disabled":false, "type":"recycle"}\'';
                $f = '<span style="color:green; font-weight: normal;">%s - %s (%s)</span>';
                $t = \sprintf($f, $node->getNodeCode(), $node->getNodeName(), $node->getChildCount() - 1);
            }

            if ($node->getNodeCode() == DefaultLocation::SCRAP_LOCATION) {

                $data_jstree = 'data-jstree=\'{ "opened" : false, "disabled":false, "type":"trash"}\'';
                $f = '<span style="color:gray; font-weight: normal;">%s - %s (%s)</span>';
                $t = \sprintf($f, $node->getNodeCode(), $node->getNodeName(), $node->getChildCount() - 1);
            }

            if ($node->getNodeCode() == DefaultLocation::ROOT_LOCATION) {

                $data_jstree = 'data-jstree=\'{ "opened" : false, "disabled":false, "type":"root"}\'';
                $f = '<span style="color:navy; font-weight: normal;">%s - %s (%s)</span>';
                $t = \sprintf($f, $node->getNodeCode(), $node->getNodeName(), $node->getChildCount() - 1);
            }

            $results = $results . sprintf($format, $data_jstree, $node->getNodeCode(), $t);
        }

        return $results;
    }
}
