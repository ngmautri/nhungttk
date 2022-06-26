<?php
namespace Application\Application\Service\Department\Tree\Output;

use Application\Application\DTO\Common\FormOptionDTO;
use Application\Domain\Util\Tree\Node\AbstractBaseNode;
use Application\Domain\Util\Tree\Output\AbstractFormatter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DepartmentWithOneLevelForOptionFormatter1 extends AbstractFormatter
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

        $n = new FormOptionDTO();
        $n->setValue($node->getId());
        $n->setName($node->getNodeName());

        if (! $node->isLeaf()) {

            $format = '%s';

            if ($level == 1) {
                $format = '%s';
            }

            $txt = $txt . " " . $this->addLevel($level) . sprintf($format, $node->getNodeName());

            // $n->setDepartmentShowName($txt);

            if ($level == 1) {
                $results[] = $n;
            }

            foreach ($node->getChildren() as $child) {

                // recursive

                $results = \array_merge($results, $this->format($child, $level + 1));
            }
        } else {

            $format = '%s';

            if ($level == 1) {
                $format = '%s';
            }

            if ($level == 2) {
                $format = '%s';
            }

            $txt = $txt . " " . $this->addLevel($level) . sprintf($format, $node->getNodeName());
            // $n->setDepartmentShowName($txt);
            if ($level == 1) {
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
