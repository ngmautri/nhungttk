<?php
namespace Application\Application\Service\Shared;

use Application\Application\Service\Contracts\CommonCollectionInterface;
use Application\Application\Service\Uom\UomService;
use Application\Infrastructure\Persistence\Filter\DefaultListSqlFilter;
use Application\Application\Service\Uom\UomGroupService;

/**
 * default collection
 *
 * @author Nguyen Mau Tri
 *
 */
class CommonCollection implements CommonCollectionInterface
{

    private $container;

    public function __construct(\Psr\Container\ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Application\Service\Contracts\CommonCollectionInterface::getUomCollection()
     */
    public function getUomCollection($companyId = null)
    {
        $container = $this->getContainer();

        /**
         *
         * @var UomService $service ;
         */
        $service = $container->get(UomService::class);
        $filter = new DefaultListSqlFilter();
        $filter->setCompanyId($companyId);

        return $service->getValueCollecion($filter);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Application\Service\Contracts\CommonCollectionInterface::getUomGroupCollection()
     */
    public function getUomGroupCollection($companyId = null)
    {
        $container = $this->getContainer();

        /**
         *
         * @var UomGroupService $service ;
         */
        $service = $container->get(UomGroupService::class);
        $filter = new DefaultListSqlFilter();
        $filter->setCompanyId($companyId);
        return $service->getValueCollecion($filter);
    }

    // =======================================

    /**
     *
     * @return mixed
     */
    private function getContainer()
    {
        return $this->container;
    }
}