<?php
namespace Application\Application\Service\Warehouse\Tree\Output;

use Application\Application\DTO\Company\TreeNode\TreeNodeForOptionDTO;
use Application\Domain\Util\Tree\Node\AbstractBaseNode;
use Application\Domain\Util\Tree\Output\AbstractFormatter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PureWhLocationWithRootForOptionFormatter extends AbstractFormatter
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Util\Tree\Output\AbstractFormatter::format()
     */
    public function format(AbstractBaseNode $node, $level = 0)
    {
        $results = [];
        $txt = '';
        $n = new TreeNodeForOptionDTO();
        $n->setNodeCode($node->getNodeCode());
        $n->setNodetName($node->getNodeName());

        if (! $node->isLeaf()) {

            $format = '%s - %s';

            $txt = $txt . "" . $this->addLevel($level) . sprintf($format, $node->getNodeCode(), $node->getNodeName());

            $n->setNodeShowName($txt);

            $results[] = $n;

            foreach ($node->getChildren() as $child) {

                // recursive

                $results = \array_merge($results, $this->format($child, $level + 1));
            }
        } else {

            $format = '%s - %s';

            $txt = $txt . " " . $this->addLevel($level) . sprintf($format, $node->getNodeCode(), $node->getNodeName());

            $n->setNodeShowName($txt);
            $results[] = $n;
        }

        return $results;
    }

    private function addLevel($level)
    {
        if ($level == 0) {
            return "";
        }

        $space = "-";
        for ($i = 0; $i < $level - 1; $i ++) {
            $space = $space . $space;
        }

        return $space;
    }
}
