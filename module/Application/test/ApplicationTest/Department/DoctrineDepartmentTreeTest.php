<?php
namespace ApplicationTest\Department;

use ApplicationTest\Bootstrap;
use Application\Application\Eventbus\EventBusService;
use Application\Application\Service\Department\Tree\DepartmentNode;
use Application\Application\Service\Department\Tree\DepartmentTree;
use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;

class DoctrineDepartmentTreeTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    /**
     *
     * @var EntityManager $em;
     */
    protected $em;

    public function setUp()
    {}

    public function testOther()
    {
        $eventBus = Bootstrap::getServiceManager()->get(EventBusService::class);
        $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

        $builder = new DepartmentTree();
        $builder->setDoctrineEM($doctrineEM);

        $builder->initTree();
        $root = $builder->createTree(1, 0);

        $node = new DepartmentNode();
        $node->setId("Controlling1");
        $node->setParentId("Finance");
        $node->setNodeName('Controlling1');
        $node->setNodeCode('Controlling1');

        // var_dump($root->isNodeDescendant($node));

        $n = $root->getNodeByName("finance");
        $builder->insertNode($node, $n);

        /**
         *
         * @var EventBusService $eventBus ;
         */
        $eventBus = Bootstrap::getServiceManager()->get(EventBusService::class);
        $eventBus->dispatch($builder->getRecordedEvents());

        echo $root->display();
    }
}