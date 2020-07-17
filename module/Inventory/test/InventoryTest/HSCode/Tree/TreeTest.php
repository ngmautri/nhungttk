<?php
namespace InventoryTest\HSCode\Tree;

use Application\Domain\Util\Composite\Builder\AbstractBuilder;
use Doctrine\ORM\EntityManager;
use Inventory\Application\Service\HSCode\HSCodeTreeService;
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
             * @var AbstractBuilder $builder
             */
            $builder = Bootstrap::getServiceManager()->get(HSCodeTreeService::class);
            $builder->initCategory();
            $tree = $builder->createComposite(1, 0);
            var_dump($tree->generateJsTree());

            $timer = $stopWatch->stop("test");
            echo $timer;
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}