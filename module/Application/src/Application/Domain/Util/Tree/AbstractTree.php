<?php
namespace Application\Domain\Util\Tree;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Util\Tree\Event\TreeNodeInserted;
use Application\Domain\Util\Tree\Event\TreeNodeRemoved;
use Application\Domain\Util\Tree\Node\AbstractBaseNode;
use Application\Domain\Util\Tree\Node\AbstractNode;
use Application\Domain\Util\Tree\Node\GenericNode;
use Psr\Log\LoggerInterface;

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

    protected $root;

    private $recordedEvents;

    private $notification;

    private $logger;

    abstract public function initTree();

    public function addEvent($e)
    {
        if ($e == null) {
            return;
        }

        $events = $this->getRecordedEvents();
        $events[] = $e;
        $this->recordedEvents = $events;
    }

    public function getRecordedEvents()
    {
        if ($this->recordedEvents == null) {
            return array();
        }

        return $this->recordedEvents;
    }

    public function clearEvents()
    {
        $this->recordedEvents = null;
    }

    protected function logException(\Exception $e)
    {
        if ($this->logger != null) {
            $this->logger->alert(sprintf('[%s:%s] %s', $e->getFile(), $e->getLine(), $e->getMessage()));
        }
    }

    protected function logInfo($message)
    {
        if ($this->logger != null) {
            $this->logger->info($message);
        }
    }

    protected function logAlert($message)
    {
        if ($this->logger != null) {
            $this->logger->alert($message);
        }
    }

    /**
     *
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

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

        $this->root = $node;

        return $this->root;
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

    public function insertNode(AbstractBaseNode $node, AbstractBaseNode $parent)
    {
        $parent->add($node);
        $target = $node;
        $defaultParams = new DefaultParameter();
        $params = null;
        $event = new TreeNodeInserted($target, $defaultParams, $params);
        $this->addEvent($event);
        return $this;
    }

    /**
     *
     * @param AbstractBaseNode $node
     * @param AbstractBaseNode $parent
     * @return \Application\Domain\Util\Tree\Event\TreeNodeInserted
     */
    public function removeNodeFromParent(AbstractBaseNode $node)
    {
        $node->removeFromParent();

        $target = $node;
        $defaultParams = null;
        $params = null;
        $event = new TreeNodeRemoved($target, $defaultParams, $params);
        $this->addEvent($event);
        return $this;
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
