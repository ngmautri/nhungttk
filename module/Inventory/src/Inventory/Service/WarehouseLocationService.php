<?php
namespace Inventory\Service;

use Application\Utility\AbstractCategory;
use Doctrine\ORM\EntityManager;
use Application\Entity\NmtApplicationDepartment;

/**
 *
 * @author Nguyen Mau Tri
 *        
 */
class WarehouseLocationService extends AbstractCategory
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
            ->getRepository('Application\Entity\NmtInventoryWarehouseLocation')
            ->findBy(array(), array(
            'locationCode' => 'ASC'
        ));

        foreach ($nodes as $row) {

            /** @var \Application\Entity\NmtInventoryWarehouseLocation $row ; */

            $id = $row->getId();
            $parent_id = $row->getParentId();
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

            if ($tree['instance']->getLocationName() == "_ROOT_") {
                $this->jsTree = $this->jsTree . '<li id="' . $tree['instance']->getId() . '" data-jstree=\'{ "opened" : true, "disabled":true}\' data-ic=\'{"hasMember":"0"}\'><span  style="cursor:text">ITEM CATEGORY</span>';
            } else {
                if ($expandAll === true) {
                    $this->jsTree = $this->jsTree . '<li id="' . $tree['instance']->getId() . '" data-jstree=\'{ "opened" : true}\' data-ic=\'{"hasMember":"' . $tree['instance']->getHasMember() . '"}>\'' . ucwords($tree['instance']->getLocationName()) . '(' . count($children) . ")\n";
                } else {
                    $this->jsTree = $this->jsTree . '<li id="' . $tree['instance']->getId() . '" data-ic=\'{"hasMember":"' . $tree['instance']->getHasMember() . '"}\'>' . ucwords($tree['instance']->getLocationName()) . '(' . count($children) . ")\n";
                }
            }

            $this->jsTree = $this->jsTree . '<ul>' . "\n";
            foreach ($children as $key => $value) {

                if (count($value['children']) > 0) {
                    $this->generateJSTreeNew($key, $expandAll);
                } else {
                    $this->jsTree = $this->jsTree . '<li id="' . $value['instance']->getId() . '" data-jstree=\'{}\' data-ic=\'{"hasMember":"' . $value['instance']->getHasMember() . '"}\'>' . $value['instance']->getLocationName() . ' </li>' . "\n";
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