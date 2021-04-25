<?php
namespace Application\Domain\Company\AccountChart\Tree;

use Application\Domain\Company\AccountChart\BaseAccount;
use Application\Domain\Company\AccountChart\BaseChart;
use Application\Domain\Util\Tree\AbstractTree;

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
            $node->setNodeName($row->getAccountNumber());
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