<?php
namespace Application\Domain\Util\Tree;

use Application\Domain\Util\Tree\Event\TreeNodeInserted;
use Application\Domain\Util\Tree\Node\AbstractBaseNode;
use Application\Domain\Util\Tree\Node\AbstractNode;
use Application\Domain\Util\Tree\Node\GenericNode;
use Application\Domain\Util\Tree\Repository\TreeCmdRepositoryInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractTree implements TreeInterface
{

    /**
     *
     * @var \SplStack;
     */
    protected $childrenStack;

    protected $nodes = array();

    protected $data = array();

    protected $index = array();

    protected $nextNode;

    /**
     *
     * @return SplStack;
     */
    public function getChildrenStack()
    {
        if ($this->childrenStack == null) {
            return new \SplStack();
        }
        return $this->childrenStack;
    }

    /**
     *
     * @param SplStack; $childrenStack
     */
    public function setChildrenStack(\SplStack $childrenStack)
    {
        $this->childrenStack = $childrenStack;
    }

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

    /**
     *
     * @param AbstractBaseNode $node
     * @param AbstractBaseNode $parent
     * @param TreeCmdRepositoryInterface $repository
     * @throws \InvalidArgumentException
     * @return \Application\Domain\Util\Tree\Event\TreeNodeInserted
     */
    public function insertNode(AbstractBaseNode $node, AbstractBaseNode $parent, TreeCmdRepositoryInterface $repository)
    {
        if ($repository == null) {
            throw new \InvalidArgumentException("TreeCmdInterface not found");
        }
        $parent->add($node);
        $target = $node;
        $defaultParams = null;
        $params = null;
        return new TreeNodeInserted($target, $defaultParams, $params);
    }

    /**
     *
     * @param AbstractBaseNode $node
     * @param AbstractBaseNode $parent
     * @return \Application\Domain\Util\Tree\Event\TreeNodeInserted
     */
    public function removeNode(AbstractBaseNode $node, AbstractBaseNode $parent, TreeCmdRepositoryInterface $repository)
    {
        $parent->remove($node);
        $target = $node;
        $defaultParams = null;
        $params = null;
        return new TreeNodeInserted($target, $defaultParams, $params);
    }

    public function depthFirstSearch(AbstractBaseNode $node)
    {
        $children = $this->getChildrenStack()->top();
    }

    public function _depthFirstSearch($children)
    {
        $children = $this->getChildrenStack()->top();
    }
}
