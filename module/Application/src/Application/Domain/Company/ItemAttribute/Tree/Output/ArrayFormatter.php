<?php
namespace Application\Domain\Util\Tree\Output;

use Application\Domain\Util\Tree\Node\AbstractBaseNode;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ArrayFormatter extends AbstractFormatter
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Util\Tree\Output\AbstractFormatter::format()
     */
    public function format(AbstractBaseNode $node, $level = 0)
    {
        $results = [];

        if (! $node->isLeaf()) {

            $results[] = [
                $node->getId(),
                $node->getNodeCode()
            ];

            foreach ($node->getChildren() as $child) {
                // recursive
                $results = \array_merge($results, $this->format($child, $level + 1));
            }
        } else {
            $results[] = [
                $node->getId(),
                $node->getNodeCode()
            ];
        }

        return $results;
    }
}
