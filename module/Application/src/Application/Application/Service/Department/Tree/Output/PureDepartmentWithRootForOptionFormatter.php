<?php
namespace Application\Application\Service\Department\Tree\Output;

use Application\Application\DTO\Company\Department\DepartmentForOptionDTO;
use Application\Domain\Company\Contracts\DefaultDepartment;
use Application\Domain\Util\Tree\Node\AbstractBaseNode;
use Application\Domain\Util\Tree\Output\AbstractFormatter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PureDepartmentWithRootForOptionFormatter extends AbstractFormatter
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

        $n = new DepartmentForOptionDTO();
        $n->setDepartmentName($node->getNodeName());
        $n->setDepartmentCode($node->getNodeCode());

        if (! $node->isLeaf()) {

            $format = '%s';

            if ($level == 1) {
                $format = '%s';
            }

            if ($level == 2) {
                $format = '%s';
            }

            if ($node->getNodeName() == DefaultDepartment::ROOT) {
                $txt = $txt . "" . $this->addLevel($level) . sprintf($format, "ROOT");
            } else {
                $txt = $txt . "" . $this->addLevel($level) . sprintf($format, $node->getNodeName());
            }

            $n->setDepartmentShowName($txt);

            $results[] = $n;

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

            if ($node->getNodeName() == DefaultDepartment::ROOT) {
                $txt = $txt . " " . $this->addLevel($level) . sprintf($format, "ROOT");
            } else {
                $txt = $txt . " " . $this->addLevel($level) . sprintf($format, $node->getNodeName());
            }

            $n->setDepartmentShowName($txt);
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
            $space = $space . $space;
        }

        return $space;
    }
}
