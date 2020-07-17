<?php
namespace Application\Service;

use Application\Utility\AbstractCategory;
use Doctrine\ORM\EntityManager;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemCategoryService extends AbstractCategory
{

    protected $doctrineEM;

    /**
     *
     * {@inheritdoc}
     * @see \Application\Utility\AbstractCategory::initCategory()
     */
    public function initCategory()
    {
        $nodes = $this->getDoctrineEM()
            ->getRepository('Application\Entity\NmtInventoryItemCategory')
            ->findBy(array(), array(
            'nodeName' => 'ASC'
        ));

        /*
         * $n = new NmtApplicationDepartment();
         * $n->getNodeParentId();
         */

        foreach ($nodes as $row) {
            $id = $row->getNodeId();
            $parent_id = $row->getNodeParentId();
            $this->data[$id] = $row;
            $this->index[$parent_id][] = $id;
        }
        return $this;
    }

    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     *
     * @param unknown $root
     * @param unknown $expandAll
     * @return string|unknown
     */
    public function generateJSTreeNew($root, $expandAll)
    {
        $tree = $this->categories[$root];
        $children = $tree['children'];

        // inorder travesal
        if (count($children) > 0) {

            if ($tree['instance']->getNodeName() == "_ROOT_") {

                $this->jsTree = $this->jsTree . '<li id="' . $tree['instance']->getNodeId() . '" data-jstree=\'{ "opened" : true, "disabled":true}\' data-ic=\'{"hasMember":"0"}\'><span  style="cursor:text">ITEM CATEGORY</span>';
            } else {
                if ($expandAll === true) {

                    $tmp = sprintf('<li title="%s" id="%s" data-jstree=\'{"opened" : true}\' data-ic=\'{"hasMember":%s}\'><span id="%s" class="categoryDroppable"> %s </span>', ucwords($tree['instance']->getNodeName()), $tree['instance']->getNodeId(), $tree['instance']->getHasMember(), $tree['instance']->getNodeId(), ucwords($tree['instance']->getNodeName()) . "(" . count($children) . ")");

                    $this->jsTree = $this->jsTree . $tmp;
                    // $this->jsTree = $this->jsTree . '<li title="'. ucwords($tree['instance']->getNodeName()).'" id="' . $tree['instance']->getNodeId().'" data-jstree=\'{ "opened" : true}\' data-ic=\'{"hasMember":"'.$tree['instance']->getHasMember().'"}>\'<span class="categoryDroppable">' . ucwords($tree['instance']->getNodeName()) . '('.count ( $children ).")</span>\n";
                } else {

                    $tmp = sprintf('<li title="%s" id="%s" data-ic=\'{"hasMember":%s}\'><span id="%s" class="categoryDroppable"> %s </span>', ucwords($tree['instance']->getNodeName()), $tree['instance']->getNodeId(), $tree['instance']->getHasMember(), $tree['instance']->getNodeId(), ucwords($tree['instance']->getNodeName()) . "(" . count($children) . ")");

                    $this->jsTree = $this->jsTree . $tmp;

                    // $this->jsTree = $this->jsTree . '<li title="'. ucwords($tree['instance']->getNodeName()).'" id="' . $tree['instance']->getNodeId().'" data-ic=\'{"hasMember":"' . $tree['instance']->getHasMember() . '"}\'><span class="categoryDroppable">' . ucwords($tree['instance']->getNodeName()) . '('.count ( $children ).")</span>\n";
                }
            }

            $this->jsTree = $this->jsTree . '<ul>' . "\n";
            foreach ($children as $key => $value) {

                if (count($value['children']) > 0) {
                    $this->generateJSTreeNew($key, $expandAll);
                } else {

                    $tmp = sprintf('<li  class="categoryDroppable" title="%s" id="%s" data-jstree="{\'opened\' : true, \'disabled\':false}" data-ic="{\'hasMember\':%s}">%s</li>', ucwords($value['instance']->getNodeName()), $value['instance']->getNodeId(), $value['instance']->getHasMember(), $value['instance']->getNodeName());

                    // $this->jsTree = $this->jsTree . '<li class="categoryDroppable" title="'. ucwords($value['instance']->getNodeName()).'" id="' . $value['instance']->getNodeId().'" data-jstree=\'{}\' data-ic=\'{"hasMember":"'.$value ['instance']->getHasMember(). '"}\'>' . $value ['instance']->getNodeName() . '</li>' . "\n";

                    $this->jsTree = $this->jsTree . $tmp;

                    $this->generateJSTreeNew($key, $expandAll);
                }
            }
            $this->jsTree = $this->jsTree . '</ul>' . "\n";

            $this->jsTree = $this->jsTree . '</li>' . "\n";
        }

        return $this->jsTree;
    }

    /**
     *
     * @param unknown $root
     * @param unknown $expandAll
     * @return string|unknown
     */
    public function generateJSTreeForAddingMember($root, $expandAll)
    {
        $tree = $this->categories[$root];
        $children = $tree['children'];

        // inorder travesal
        if (count($children) > 0) {

            if ($tree['instance']->getNodeName() == "_ROOT_") {
                $this->jsTree = $this->jsTree . '<li id="' . $tree['instance']->getNodeId() . '" data-jstree=\'{ "opened" : true, "disabled":true}\' data-ic=\'{"hasMember":"0"}\'><span style="cursor:not-allowed">ITEM CATEGORY</span>';
            } else {
                if ($expandAll === true) {
                    if ($tree['instance']->getHasMember() == 0) {
                        $this->jsTree = $this->jsTree . '<li id="' . $tree['instance']->getNodeId() . '" data-jstree=\'{ "opened" : true, "disabled":true}\' data-ic=\'{"hasMember":"' . $tree['instance']->getHasMember() . '"}\'><span style="cursor:not-allowed">' . ucwords($tree['instance']->getNodeName()) . '(' . count($children) . ")</span>\n";
                    } else {
                        $this->jsTree = $this->jsTree . '<li id="' . $tree['instance']->getNodeId() . '" data-jstree=\'{ "opened" : true}\' data-ic=\'{"hasMember":"' . $tree['instance']->getHasMember() . '"}>\'' . ucwords($tree['instance']->getNodeName()) . '(' . count($children) . ")\n";
                    }
                } else {
                    if ($tree['instance']->getHasMember() == 0) {
                        $this->jsTree = $this->jsTree . '<li id="' . $tree['instance']->getNodeId() . '" data-jstree=\'{"disabled":true}\' data-ic=\'{"hasMember":"' . $tree['instance']->getHasMember() . '"}\'><span style="cursor:not-allowed">' . ucwords($tree['instance']->getNodeName()) . '(' . count($children) . ")</span>\n";
                    } else {
                        $this->jsTree = $this->jsTree . '<li id="' . $tree['instance']->getNodeId() . '" data-ic=\'{"hasMember":"' . $tree['instance']->getHasMember() . '"}\'>' . ucwords($tree['instance']->getNodeName()) . '(' . count($children) . ")\n";
                    }
                }
            }

            $this->jsTree = $this->jsTree . '<ul>' . "\n";
            foreach ($children as $key => $value) {

                if (count($value['children']) > 0) {
                    $this->generateJSTreeForAddingMember($key, $expandAll);
                } else {
                    if ($value['instance']->getHasMember() == 0) {
                        $this->jsTree = $this->jsTree . '<li id="' . $value['instance']->getNodeId() . '" data-jstree=\'{"disabled":true}\' data-ic=\'{"hasMember":"' . $value['instance']->getHasMember() . '"}\'><span style="cursor:not-allowed">' . $value['instance']->getNodeName() . '</span> </li>' . "\n";
                    } else {
                        $this->jsTree = $this->jsTree . '<li id="' . $value['instance']->getNodeId() . '" data-jstree=\'{}\' data-ic=\'{"hasMember":"' . $value['instance']->getHasMember() . '"}\'>' . $value['instance']->getNodeName() . ' </li>' . "\n";
                    }
                    $this->generateJSTreeForAddingMember($key, $expandAll);
                }
            }
            $this->jsTree = $this->jsTree . '</ul>' . "\n";

            $this->jsTree = $this->jsTree . '</li>' . "\n";
        }

        return $this->jsTree;
    }

    /**
     * with node ID
     *
     * @param unknown $root
     * @return string|unknown
     */
    public function generateJSTree1($root)
    {
        $tree = $this->categories[$root];
        $children = $tree['children'];

        // inorder travesal
        if (count($children) > 0) {
            $this->jsTree = $this->jsTree . '<li id="' . $tree['instance']->getNodeId() . '" data-jstree=\'{ "opened" : true}\'> ' . ucwords($tree['instance']->getNodeName()) . '(' . count($children) . ")\n";
            $this->jsTree = $this->jsTree . '<ul>' . "\n";
            foreach ($children as $key => $value) {

                if (count($value['children']) > 0) {
                    $this->generateJSTree1($key);
                } else {
                    $this->jsTree = $this->jsTree . '<li id="' . $value['instance']->getNodeId() . '" data-jstree=\'{}\'>' . $value['instance']->getNodeName() . ' </li>' . "\n";
                    $this->generateJSTree1($key);
                }
            }
            $this->jsTree = $this->jsTree . '</ul>' . "\n";

            $this->jsTree = $this->jsTree . '</li>' . "\n";
        }

        return $this->jsTree;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @return \Application\Service\DepartmentService
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }
}