<?php
namespace Application\Application\Service\Warehouse\Tree\Output;

use Application\Application\DTO\Company\Warehouse\WhLocationForOptionDTO;
use Application\Domain\Util\Tree\Node\AbstractBaseNode;
use Application\Domain\Util\Tree\Output\AbstractFormatter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class WhLocationForOptionFormatter extends AbstractFormatter
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

        $n = new WhLocationForOptionDTO();
        $n->setWhLocationName($node->getNodeName());
        $n->setWhLocationCode($node->getNodeCode());

        if (! $node->isLeaf()) {

            $format = '<span style="color:black"> %s </span>';

            if ($level == 1) {
                $format = '<span style="color:navy; font-weight: bold;"> %s</span>';
            }

            if ($level == 2) {
                $format = '<span style="color:navy"> %s </span>';
            }

            $txt = $txt . " " . $this->addLevel($level) . sprintf($format, $node->getNodeName());

            $n->setWhLocationShowName($txt);

            if ($level > 0) {
                $results[] = $n;
            }

            foreach ($node->getChildren() as $child) {

                // recursive
                $results = \array_merge($results, $this->format($child, $level + 1));
            }
        } else {

            $format = '<span style="color:black"> %s </span>';

            if ($level == 1) {
                $format = '<span style="color:navy; font-weight: bold;"> %s</span>';
            }

            if ($level == 2) {
                $format = '<span style="color:navy"> %s </span>';
            }

            $txt = $txt . " " . $this->addLevel($level) . sprintf($format, $node->getNodeName());
            $n->setWhLocationShowName($txt);
            if ($level > 0) {
                $results[] = $n;
            }
        }

        return $results;
    }

    private function addLevel($level)
    {
        if ($level == 1) {
            return "";
        }

        $space = "-";
        for ($i = 0; $i < $level - 1; $i ++) {
            $space = $space . $space;
        }

        return $space;
    }
}
