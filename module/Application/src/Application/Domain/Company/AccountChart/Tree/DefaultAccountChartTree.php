<?php
namespace Application\Domain\Company\AccountChart\Tree;

use Application\Application\Command\Options\CmdOptions;
use Application\Domain\Company\AccountChart\BaseAccount;
use Application\Domain\Company\AccountChart\BaseChart;
use Application\Domain\Util\Tree\AbstractTree;
use Application\Domain\Util\Tree\Node\AbstractBaseNode;
use Webmozart\Assert\Assert;
use Exception;

/**
 *
 * @author Nguyen Mau Tri
 *
 */
class DefaultAccountChartTree extends AbstractTree
{

    private $chart;

    public function __construct(BaseChart $chart)
    {
        if (! $chart instanceof BaseChart) {
            throw new \InvalidArgumentException("BaseChart required!");
        }

        $this->chart = $chart;
    }

    public function insertAccount(AbstractBaseNode $node, AbstractBaseNode $parent, CmdOptions $options = null)
    {
        Assert::isInstanceOf($node, AbstractBaseNode::class);
        Assert::isInstanceOf($parent, AbstractBaseNode::class);

        try {
            $parent->add($node);
        } catch (Exception $e) {
            $f = 'Account {#%s-%s} exits already! ';
            throw new \InvalidArgumentException(\sprintf($f, $node->getNodeCode(), $node->getNodeName()));
        }
    }

    public function renameAccountCode(AbstractBaseNode $node, $newCode, CmdOptions $options = null)
    {
        Assert::isInstanceOf($node, AbstractBaseNode::class);

        try {
            $oldCode = $node->getNodeCode();
            $node->rename($newCode);
        } catch (Exception $e) {
            $f = 'Account nummber {#%s} exits already. Update imposible for {%s}! ';
            throw new \InvalidArgumentException(\sprintf($f, $newCode, $oldCode));
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Util\Tree\AbstractTree::initTree()
     */
    public function initTree()
    {
        $results = $this->getChart()->getLazyAccountCollection();
        $chartCode = $this->getChart()->getCoaCode();
        $chartName = $this->getChart()->getCoaName();

        // convert to Generic Component
        $node = new AccountChartNode();
        $node->setContextObject(null);

        $node->setId($chartCode);
        $node->setNodeName($chartName);
        $node->setNodeCode($chartCode);

        $this->data[$chartCode] = $node;
        $this->index[][] = $chartCode;

        if ($results == null) {
            return $this;
        }

        foreach ($results as $row) {

            /** @var BaseAccount $row ; */

            $id = $row->getAccountNumber();
            $parent_id = $row->getParentAccountNumber();

            // convert to Generic Component
            $node = new AccountChartNode();
            $node->setContextObject($row);

            $node->setId($row->getId());
            $node->setNodeName($row->getAccountName());
            $node->setNodeCode($row->getAccountNumber());
            $node->setNodeDescription($row->getRemarks());

            if ($parent_id == null) {
                $node->setParentCode($chartCode);
                $this->data[$id] = $node;
                $this->index[$chartCode][] = $id;
            } else {

                $node->setParentId($row->getParentAccountNumber());
                $node->setParentCode($row->getParentAccountNumber());

                $this->data[$id] = $node;
                $this->index[$parent_id][] = $id;
            }
        }
        return $this;
    }

    /**
     *
     * @return \Application\Domain\Company\AccountChart\BaseChart
     */
    public function getChart()
    {
        return $this->chart;
    }
}