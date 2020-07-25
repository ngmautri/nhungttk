<?php
namespace Inventory\Infrastructure\Doctrine;

use Application\Domain\Util\Tree\Node\AbstractNode;
use Application\Domain\Util\Tree\Repository\TreeCmdRepositoryInterface;
use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemCategoryCmdRepositoryImp extends AbstractDoctrineRepository implements TreeCmdRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Util\Tree\Repository\TreeCmdRepositoryInterface::store()
     */
    public function store(AbstractNode $node)
    {
        $contextObject = $node->getContextObject();
    }
}