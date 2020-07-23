<?php
namespace Application\Domain\Util\Tree\Node;

use Application\Domain\Util\Tree\Output\AbstractFormatter;
use Application\Domain\Util\Tree\Output\JsTreeFormatter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractBaseNode extends AbstractNode
{

    public function getChildCount()
    {
        return count($this->getChildren());
    }

    public function isLeaf()
    {
        return $this->getChildCount() == 0;
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

        if ($this->has($node)) {
            throw new \InvalidArgumentException("Child exits already! " . $node->getNodeName());
        }

        $this->getChildren()->attach($node);
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

    /**
     *
     * @param AbstractNode $node
     */
    public function remove(AbstractNode $node)
    {
        $this->children->detach($node);
        $node->setParent(null);
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
     * Root node has no parent.
     *
     * @return \Application\Domain\Util\Tree\AbstractNode;
     */
    public function getRoot()
    {
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

        while ($current != null && $current != $node) {
            $current = $current->getParent();
        }

        return $current == $node;
    }

    /**
     *
     * @return array
     */
    public function getPath()
    {
        return $this->getPathToRoot($this, 0);
    }

    /**
     *
     * @param AbstractNode $node
     * @param int $depth
     * @return array
     */
    protected function getPathToRoot(AbstractNode $node = null, $depth)
    {
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
}
