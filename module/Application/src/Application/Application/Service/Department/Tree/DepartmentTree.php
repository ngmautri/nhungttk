<?php
namespace Application\Application\Service\Department\Tree;

use Application\Domain\Company\Department\AbstractDepartmentTree;
use Doctrine\ORM\EntityManager;
use Inventory\Application\Service\MfgCatalog\Tree\MfgCatalogNode;
use Inventory\Infrastructure\Persistence\Doctrine\HSCodeReportRepositoryImpl;

/**
 *
 * @author Nguyen Mau Tri
 *
 */
class DepartmentTree extends AbstractDepartmentTree
{

    protected $doctrineEM;

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Util\Tree\AbstractTree::initTree()
     */
    public function initTree()
    {
        $rep = new HSCodeReportRepositoryImpl($this->getDoctrineEM());
        $results = $rep->getList();
        foreach ($results as $row) {

            /** @var \Application\Entity\InventoryHsCode $row ; */

            $id = $row->getId();
            $parent_id = $row->getParentId();

            // convert to Generic Component
            $genericComponent = new MfgCatalogNode();
            $genericComponent->setId($row->getId());
            $genericComponent->setParentId($row->getParentId());
            $genericComponent->setNodeName($row->getCodeDescription());
            $genericComponent->setNodeCode($row->getHsCode());
            $genericComponent->setNodeDescription($row->getCodeDescription());
            $genericComponent->setNodeDescription1($row->getCodeDescription1());
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