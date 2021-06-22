<?php
namespace Inventory\Domain\Item\Composite\Tree;

use Application\Application\Command\Options\CmdOptions;
use Application\Domain\Util\Tree\AbstractTree;
use Application\Domain\Util\Tree\Node\AbstractBaseNode;
use Inventory\Domain\Warehouse\BaseWarehouse;
use Inventory\Domain\Warehouse\Location\BaseLocation;
use Webmozart\Assert\Assert;
use Exception;

/**
 *
 * @author Nguyen Mau Tri
 *
 */
class DefaultCompositeTree extends AbstractTree
{

    private $warehouse;

    public function __construct(BaseWarehouse $warehouse)
    {
        if (! $warehouse instanceof BaseWarehouse) {
            throw new \InvalidArgumentException("BaseWarehouse required!");
        }

        $this->warehouse = $warehouse;
    }

    public function insertLocation(AbstractBaseNode $node, AbstractBaseNode $parent, CmdOptions $options = null)
    {
        Assert::isInstanceOf($node, AbstractBaseNode::class);
        Assert::isInstanceOf($parent, AbstractBaseNode::class);

        try {
            $parent->add($node);
        } catch (Exception $e) {
            $f = 'Location {#%s-%s} exits already! ';
            throw new \InvalidArgumentException(\sprintf($f, $node->getNodeCode(), $node->getNodeName()));
        }
    }

    public function changeLocationCode(AbstractBaseNode $node, $newNumber, CmdOptions $options = null)
    {
        Assert::isInstanceOf($node, AbstractBaseNode::class);

        $newNode = new CompositeNode();
        $newNode->setNodeCode($newNumber);

        if ($newNode->equals($node)) {
            return; // nothing to change.
        }

        $oldNumber = $node->getNodeCode();

        if ($node->getRoot()->isNodeDescendant($newNode)) {
            $f = 'Location Code {#%s} exits already. Update imposible for {%s}! ';
            throw new \InvalidArgumentException(\sprintf($f, $newNumber, $oldNumber));
        }

        $node->changeCode($newNumber);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Util\Tree\AbstractTree::initTree()
     */
    public function initTree()
    {
        $results = $this->getWarehouse()->getLazyLocationCollection();
        $whCode = $this->getWarehouse()->getWhCode();
        $whName = $this->getWarehouse()->getWhName();

        // convert to Generic Component
        $node = new LocationNode();
        $node->setContextObject(null);

        $node->setId($whCode);
        $node->setNodeName($whName);
        $node->setNodeCode($whCode);

        $this->data[$whCode] = $node;
        $this->index[][] = $whCode;

        if ($results == null) {
            return $this;
        }

        foreach ($results as $row) {

            /** @var BaseLocation $row ; */

            // convert to Generic Component
            $node = new CompositeNode();
            $node->setContextObject($row);

            $node->setId($row->getId());
            $node->setNodeName($row->getLocationName());
            $node->setNodeCode($row->getLocationCode()); // important//
            $node->setNodeDescription($row->getRemarks());

            $id = $row->getLocationCode();
            $parent_id = $row->getParentCode();

            if ($parent_id == null) {
                $node->setParentCode($whCode);
                $this->data[$id] = $node;
                $this->index[$whCode][] = $id;
            } else {

                $node->setParentId($row->getParentCode());
                $node->setParentCode($row->getParentCode());

                $this->data[$id] = $node;
                $this->index[$parent_id][] = $id;
            }
        }
        return $this;
    }

    /**
     *
     * @return \Inventory\Domain\Warehouse\BaseWarehouse
     */
    public function getWarehouse()
    {
        return $this->warehouse;
    }
}