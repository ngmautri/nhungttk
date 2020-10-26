<?php
namespace Application\Application\Service\Shared;

use Application\Application\Service\Contracts\CommonCollectionInterface;
use Application\Application\Service\Uom\UomService;
use Application\Infrastructure\Persistence\Filter\DefaultListSqlFilter;

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
    public function getUomCollection()
    {
        $container = $this->getContainer();

        /**
         *
         * @var UomService $service ;
         */
        $service = $container->get(UomService::class);
        return $service->getValueCollecion(new DefaultListSqlFilter());
    }

    /**
     *
     * @return mixed
     */
    private function getContainer()
    {
        return $this->container;
    }
}