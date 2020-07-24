<?php
namespace Application\Domain\Util\Tree;

use Application\Domain\Util\Tree\Node\AbstractNode;
use Application\Domain\Util\Tree\Node\GenericNode;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractTree implements TreeInterface
{

    protected $nodes = array();

    protected $data = array();

    protected $index = array();

    abstract public function initTree();

    /**
     *
     * @param int $current_id
     * @param int $level
     * @param boolean $updateParent
     * @throws \RuntimeException
     * @return \Application\Domain\Util\Tree\Node\GenericNode
     */
    public function createTree($current_id, $level, $updateParent = true)
    {
        if ($this->data == null || $this->index == null) {
            throw new \RuntimeException("Input for tree buider not valid");
        }

        $node = $this->data[$current_id];

        if (! $node instanceof GenericNode) {
            throw new \RuntimeException("Invalid input. GenericNode not set.");
        }

        $node->setAllowChildren(true);

        if (isset($this->index[$current_id])) {
            // has children
            foreach ($this->index[$current_id] as $child_id) {

                // pre-order travesal
                $child = $this->createTree($child_id, $level + 1, false);
                $node->add($child);
            }
        }

        if ($updateParent) {
            $parent_id = $node->getParentId();
            if (! $parent_id == null) {

                $parent = $this->data[$parent_id];
                if ($parent instanceof GenericNode) {
                    $parent->setAllowChildren(true);
                    $parent->add($node);
                    $this->updateParent($parent);
                }
            }
        }

        return $node;
    }

    /**
     *
     * @param AbstractNode $currentNode
     */
    public function updateParent(AbstractNode $currentNode)
    {
        $parent_id = $currentNode->getParentId();
        if ($parent_id == null) {
            return $currentNode;
        }

        $parentData = $this->data[$parent_id];
        if ($parentData instanceof GenericNode) {
            $parent = new GenericNode();
            $parent->updateFrom($parentData);
            $parent->setAllowChildren(true);
            $parent->add($currentNode);
            $this->updateParent($parent);
        }

        return $currentNode;
    }
}
