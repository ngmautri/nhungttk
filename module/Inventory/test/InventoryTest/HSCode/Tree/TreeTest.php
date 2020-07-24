<?php
namespace InventoryTest\HSCode\Tree;

use Application\Domain\Util\Tree\AbstractTree;
use Doctrine\ORM\EntityManager;
use Inventory\Application\Service\HSCode\Tree\HSCodeTree;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use Symfony\Component\Stopwatch\Stopwatch;
use PHPUnit_Framework_TestCase;

class TreeTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $stopWatch = new Stopwatch();
            $stopWatch->start("test");

            // $indexer = new HSCodeSearchIndexImpl();

            /**
             *
             * @var AbstractTree $builder
             */
            $builder = Bootstrap::getServiceManager()->get(HSCodeTree::class);
            $builder->initTree();
            $tree = $builder->createTree(1, 0);
            var_dump($tree->getPath());

            $timer = $stopWatch->stop("test");
            echo $timer;
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}