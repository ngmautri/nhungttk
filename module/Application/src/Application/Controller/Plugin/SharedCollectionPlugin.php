<?php
namespace Application\Controller\Plugin;

use Application\Application\Service\Contracts\CommonCollectionInterface;
use Webmozart\Assert\Assert;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class SharedCollectionPlugin extends AbstractPlugin
{

    private $collection;

    public function __construct(CommonCollectionInterface $collection)
    {
        Assert::notNull($collection);
        $this->collection = $collection;
    }

    public function getUomCollection()
    {
        return $this->getCollection()->getUomCollection();
    }

    public function getUomGroupCollection()
    {
        return $this->getCollection()->getUomGroupCollection();
    }

    /**
     *
     * @return \Application\Application\Service\Contracts\CommonCollectionInterface
     */
    private function getCollection()
    {
        return $this->collection;
    }
}
