<?php
namespace ApplicationTest\Department;

use ApplicationTest\Bootstrap;
use Application\Application\EventBus\EventBusService;
use Application\Application\Service\Department\Tree\DepartmentNode;
use Application\Application\Service\Department\Tree\TestDepartmentTree;
use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;

class DepartmentTreeTest extends PHPUnit_Framework_TestCase
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
        $builder = new TestDepartmentTree();
        $builder->initTree();
        $root = $builder->createRoot();

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

        var_dump($root->getAllNodesName());
    }
}