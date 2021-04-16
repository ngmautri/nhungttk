<?php
namespace Application\Domain\Util\Tree\Node;

use Application\Domain\Util\Tree\Output\AbstractFormatter;
use Application\Domain\Util\Tree\Output\JsTreeFormatter;
use Application\Domain\Util\Tree\Output\NodeArrayFormatter;
use Application\Domain\Util\Tree\Output\NodeCodeFormatter;
use Application\Domain\Util\Tree\Output\NodeNameFormatter;
use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class AbstractBaseNode extends AbstractNode
{

    protected $visited = [];

    protected $chilrendId = [];

    protected $found = false;

    public function getChildCount()
    {
        $total = 1;
        foreach ($this->children as $child) {
            $total = $total + $child->getChildCount();
        }

        return $total;
    }

    public function isLeaf()
    {
        return $this->getChildren()->count() == 0;
    }

    /**
     *
     * @param AbstractFormatter $formatter
     * @return string
     */
    public function display(AbstractFormatter $formatter = null)
    {
        // default formatter
        if ($formatter == null) {
            $formatter = new JsTreeFormatter();
        }

        $results = $formatter->format($this);
        return $results;
    }

    /**
     *
     * @param AbstractNode $node
     * @throws \InvalidArgumentException
     */
    public function add(AbstractNode $node)
    {
        if (! $this->getAllowChildren()) {
            throw new \InvalidArgumentException("Children not allowed!");
        }

        if ($node == null) {
            throw new \InvalidArgumentException("Child node not set!");
        }
        if ($this->isNodeAncestor($node)) {
            throw new \InvalidArgumentException("Can not add ancestor node! " . $node->getNodeName());
        }
        $node->setParent($this);
        $node->setParentId($this->getId());

        if ($this->getRoot()->isNodeDescendant($node)) {
            $f = 'Node {%s} exits already! ';
            throw new \InvalidArgumentException(\sprintf($f, $node->getNodeName()));
        } else {
            $this->getChildren()->attach($node);
        }
    }

    /**
     *
     * @param AbstractNode $node
     * @return boolean
     */
    public function has(AbstractNode $node)
    {
        if ($node == null) {
            return false;
        }

        if ($this->getChildCount() == 0) {
            return false;
        }

        foreach ($this->getChildren() as $child) {

            if ($child == $node) {
                return true;
            }
        }
        return false;
    }

    public function getNodeByName($name)
    {
        $result = new ArrayCollection($this->display(new NodeArrayFormatter()));

        foreach ($result as $r) {

            /**
             *
             * @var AbstractBaseNode $r ;
             */
            if (\strtolower(trim($r->getNodeName())) == \strtolower(trim($name))) {

                return $r;
            }
        }

        return null;
    }

    /**
     *
     * @param AbstractNode $node
     */
    public function remove(AbstractNode $node)
    {
        if ($node == null) {
            throw new \InvalidArgumentException("Null 'node' argument.");
        }
        if (! $node->getParent()->equals($this)) {
            throw new InvalidArgumentException("The given 'node' is not a child of this node.");
        }

        $this->children->detach($node);
        $node->setParent(null);
    }

    /**
     *
     * @throws \InvalidArgumentException
     */
    public function removeFromParent()
    {
        if ($this->isRoot()) {
            throw new \InvalidArgumentException("Root 'node' argument.");
        }

        $this->getParent()->remove($this);
        $this->setParent(null);
    }

    /**
     *
     * @param string $newName
     * @throws \InvalidArgumentException
     */
    public function rename($newName)
    {
        if ($newName == null) {
            throw new \InvalidArgumentException("Null 'Node Name' argument.");
        }

        $node = new GenericNode();
        $node->setNodeName($newName);

        if ($this->getRoot()->isNodeDescendant($node)) {
            $f = 'Node {%s} exits already! ';
            throw new \InvalidArgumentException(\sprintf($f, $node->getNodeName()));
        } else {
            $this->setNodeName($newName);
        }
    }

    /**
     *
     * @param AbstractNode $input
     */
    public function updateFrom(AbstractNode $input)
    {
        if (! $input instanceof AbstractNode) {
            return;
        }

        $this->setId($input->getId());
        $this->setParentId($input->getParentId());
        $this->setNodeCode($input->getNodeCode());
        $this->setNodeCode1($input->getNodeCode1());
        $this->setNodeDescription($input->getNodeDescription());
        $this->setNodeDescription1($input->getNodeDescription1());
        $this->setNodeLabel($input->getNodeLabel());
        $this->setNodeLabel1($input->getNodeLabel1());
        $this->setNodeName($input->getNodeName());
        $this->setNodeName1($input->getNodeName1());
        $this->setParentCode($input->getParentCode());
        $this->setParentLabel($input->getParentLabel());
    }

    /**
     *
     * @return \Application\Domain\Util\Tree\Node\AbstractBaseNode
     */
    public function getRoot()
    {
        /**
         *
         * @var \Application\Domain\Util\Tree\Node\AbstractBaseNode $current ;
         */
        $current = $this;
        $check = $current->getParent();

        while ($check != null) {
            $current = $check;
            $check = $current->getParent();
        }

        return $current;
    }

    /**
     *
     * @return boolean
     */
    public function isRoot()
    {
        return $this->getParent() == null;
    }

    /**
     *
     * @param AbstractNode $node
     * @return boolean
     */
    public function isNodeAncestor(AbstractNode $node)
    {
        if ($node == null) {
            return false;
        }

        $current = $this;

        while ($current != null && ! $current->equals($node)) {
            $current = $current->getParent();
        }

        return $node->equals($current);
    }

    /**
     *
     * @param AbstractNode $node
     * @return boolean
     */
    public function isNodeDescendant(AbstractBaseNode $node)
    {
        $result = $this->getAllNodes();

        foreach ($result as $r) {

            if ($node->equals($r)) {
                return true;
            }
        }

        return false;
    }

    public function getAllNodes()
    {
        return new ArrayCollection($this->display(new NodeArrayFormatter()));
    }

    public function getAllNodesName()
    {
        return new ArrayCollection($this->display(new NodeNameFormatter()));
    }

    public function getAllNodesCode()
    {
        return new ArrayCollection($this->display(new NodeCodeFormatter()));
    }

    /**
     *
     * @deprecated
     * @param AbstractNode $node
     * @return boolean
     */
    public function searchDescendant(AbstractNode $node)
    {
        if ($node == null) {
            return false;
        }

        if ($this->getChildCount() == 0) {
            if ($node->equals($this)) {
                return true;
            }
        }

        if ($this->getChildCount() > 0) {

            foreach ($this->getChildren() as $child) {
                // echo $child->getNodeName() . "==Current==\n";
                if ($node->equals($child)) {
                    return true;
                }
                $child->searchDescendant($node);
            }
        }

        // return false;
    }

    /**
     * Depth-First Search
     *
     * @param AbstractNode $node
     * @return boolean
     */
    public function dfs(AbstractNode $node)
    {
        if ($node == null) {
            return false;
        }

        $this->visited[] = $node->getNodeName();

        if ($this->getChildCount() > 0) {

            foreach ($this->getChildren() as $child) {
                if (! $this->visited[$child->getNodeName()]) {
                    $this->dfs($node);
                }
            }
        }
    }

    /**
     *
     * @deprecated
     * @param number $level
     * @return \Application\Domain\Util\Tree\Node\AbstractBaseNode[][]|NULL[][]
     */
    public function preorderTravel($level = 0)
    {
        $results = [];

        if (! $this->isLeaf()) {

            $results[] = [
                $this
            ];

            foreach ($this->getChildren() as $child) {
                // recursive
                $results = \array_merge($results, $child->preorderTravel($level + 1));
            }
        } else {
            $results[] = [
                $node->getId(),
                $node->getNodeCode()
            ];
        }

        return $results;
    }

    /**
     *
     * @return array
     */
    public function getPath()
    {
        return $this->getPathToRoot($this, 0);
    }

    public function getPathArray()
    {
        $pathArray = [];
        $path = $this->getPath();
        if ($path == null) {
            return;
        }

        foreach ($path as $n) {
            $pathArray[] = [
                $n->getId(),
                $n->getNodeCode()
            ];
        }

        return $pathArray;
    }

    /**
     *
     * @param AbstractBaseNode $node
     * @return boolean
     */
    public function isNodeRelated(AbstractBaseNode $node)
    {
        if ($node == null) {
            return false;
        }

        return $node->getRoot() == $this->getRoot();
    }

    /**
     *
     * @return void|NULL[]
     */
    public function getPathId()
    {
        $pathArray = [];
        $path = $this->getPath();
        if ($path == null) {
            return;
        }

        foreach ($path as $n) {
            $pathArray[] = $n->getId();
        }

        return $pathArray;
    }

    /**
     *
     * @param AbstractNode $node
     * @param int $depth
     * @return array
     */
    protected function getPathToRoot(AbstractNode $node = null, $depth)
    {
        if ($node->isRoot()) {
            return null;
        }

        $path = [];

        if ($node == null) {
            if ($depth == 0) {
                return null;
            }

            return $path;
        }

        $path = $this->getPathToRoot($node->getParent(), $depth + 1);
        $path[$depth] = $node;

        return $path;
    }

    /**
     *
     * @param AbstractNode $node
     * @param int $depth
     * @return array
     */
    protected function getPathFromRoot(AbstractNode $node, $depth)
    {
        $path = [];

        if ($node == null) {
            if ($depth == 0) {
                return null;
            }

            return $path;
        }

        $path = $this->getPathFromRoot($node->getParent(), $depth + 1);
        $path[$depth] = $node;

        return $path;
    }

    public function showPathTo()
    {
        return $this->_showPathTo($this->getParent());
    }

    private function _showPathTo(AbstractNode $node = null)
    {
        if ($node == null) {
            return "";
        }

        if ($node->getParent() !== null) {
            $format = "<br>" . '<i class="fa fa-caret-up" aria-hidden="true"></i> %s %s';
            return $this->_showPathTo($node->getParent()) . \sprintf($format, $node->getNodeCode(), $node->getNodeName());
        }
    }

    /**
     *
     * @return multitype:
     */
    public function getVisited()
    {
        return $this->visited;
    }

    /**
     *
     * @return multitype:
     */
    public function getChilrendId()
    {
        return $this->chilrendId;
    }
}
