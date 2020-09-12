<?php
namespace UserTest\User\Tree;

use Application\Domain\Util\Tree\AbstractTree;
use Doctrine\ORM\EntityManager;
use Procure\Domain\Exception\InvalidArgumentException;
use Symfony\Component\Stopwatch\Stopwatch;
use UserTest\Bootstrap;
use User\Application\Service\ACLRole\Tree\ACLRoleTree;
use PHPUnit_Framework_TestCase;
use Application\Domain\Util\Tree\Output\ArrayFormatter;

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
            $builder = Bootstrap::getServiceManager()->get(ACLRoleTree::class);
            $builder->initTree();
            $tree2 = $builder->createTree(8, 0);

            // var_dump($tree2->display(new ArrayFormatter()));

            var_dump($tree2->isRoot());

            $timer = $stopWatch->stop("test");
            echo $timer;
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}