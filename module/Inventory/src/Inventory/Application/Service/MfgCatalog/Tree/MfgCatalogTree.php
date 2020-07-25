<?php
namespace Inventory\Application\Service\MfgCatalog\Tree;

use Application\Domain\Util\Tree\AbstractTree;
use Application\Entity\NmtInventoryItemCategory;
use Doctrine\ORM\EntityManager;
use Inventory\Infrastructure\Persistence\Doctrine\ItemCategoryQueryRepositoryImpl;

/**
 *
 * @author Nguyen Mau Tri
 *        
 */
class MfgCatalogTree extends AbstractTree
{

    protected $doctrineEM;

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Util\Tree\AbstractTree::initTree()
     */
    public function initTree()
    {
        $rep = new ItemCategoryQueryRepositoryImpl($this->getDoctrineEM());
        $results = $rep->getRoot(74);
        foreach ($results as $row) {

            /** @var NmtInventoryItemCategory $row ; */

            $id = $row->getNodeId();
            $parent_id = $row->getNodeParentId();

            // convert to Generic Component
            $genericComponent = new MfgCatalogNode();
            $genericComponent->setId($row->getNodeId());
            $genericComponent->setParentId($row->getNodeParentId());
            $genericComponent->setNodeName($row->getNodeName());
            $genericComponent->setNodeCode($row->getNodeName());
            $genericComponent->setNodeDescription($row->getRemarks());
            $this->data[$id] = $genericComponent;
            $this->index[$parent_id][] = $id;
        }
        return $this;
    }

    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @return \Application\Service\DepartmentService
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }
}