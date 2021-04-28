<?php
namespace Application\Application\Service\Department\Tree\Output;

use Application\Application\DTO\Company\AccountChart\AccountForOptionDTO;
use Application\Domain\Company\Contracts\DefaultDepartment;
use Application\Domain\Util\Tree\Node\AbstractBaseNode;
use Application\Domain\Util\Tree\Output\AbstractFormatter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class AccountWithRootForOptionFormatter extends AbstractFormatter
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

        $n = new AccountForOptionDTO();
        $n->setAccountName($node->getNodeName());
        $n->setAccountCode($node->getNodeCode());

        if (! $node->isLeaf()) {

            $format = '<span style="color:black"> %s </span>';

            if ($level == 1) {
                $format = '<span style="color:navy; font-weight: bold;"> %s</span>';
            }

            if ($level == 2) {
                $format = '<span style="color:navy"> %s </span>';
            }

            if ($node->getNodeName() == DefaultDepartment::ROOT) {
                $txt = $txt . "" . $this->addLevel($level) . sprintf($format, "ROOT");
            } else {
                $txt = $txt . "" . $this->addLevel($level) . sprintf($format, $node->getNodeName());
            }

            $n->setAccountShowName($txt);

            $results[] = $n;

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

            if ($node->getNodeName() == DefaultDepartment::ROOT) {
                $txt = $txt . " " . $this->addLevel($level) . sprintf($format, "ROOT");
            } else {
                $txt = $txt . " " . $this->addLevel($level) . sprintf($format, $node->getNodeName());
            }

            $n->setAccountShowName($txt);
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
