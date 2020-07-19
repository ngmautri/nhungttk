<?php
namespace Inventory\Application\Service\HSCode;

use Application\Domain\Util\Composite\GenericComponent;
use Application\Domain\Util\Composite\Builder\AbstractBuilder;
use Doctrine\ORM\EntityManager;
use Inventory\Infrastructure\Persistence\Doctrine\HSCodeReportRepositoryImpl;

/**
 *
 * @author Nguyen Mau Tri
 *        
 */
class HSCodeTreeService extends AbstractBuilder
{

    protected $doctrineEM;

    /**
     *
     * {@inheritdoc}
     * @see \Application\Utility\AbstractCategory::initCategory()
     */
    public function initCategory()
    {
        $rep = new HSCodeReportRepositoryImpl($this->getDoctrineEM());
        $results = $rep->getList();
        foreach ($results as $row) {

            /** @var \Application\Entity\InventoryHsCode $row ; */

            $id = $row->getId();
            $parent_id = $row->getParentId();

            // convert to Generic Component
            $genericComponent = new GenericComponent();
            $genericComponent->setId($row->getId());
            $genericComponent->setParenId($row->getParentId());
            $genericComponent->setComponentName($row->getCodeDescription());
            $genericComponent->setComponentCode($row->getHsCode());
            $genericComponent->setComponentDescription($row->getCodeDescription());
            $genericComponent->setComponentDescription1($row->getCodeDescription1());

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