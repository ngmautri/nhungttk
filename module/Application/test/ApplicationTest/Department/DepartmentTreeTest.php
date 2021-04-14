<?php
namespace ApplicationTest\Department;

use Application\Application\Eventbus\EventBusService;
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
        $node->setId("Accounting");
        $node->setParentId("Finance");
        $node->setNodeName('Accounting');
        $node->setNodeCode('Accounting');

        \var_dump($root->searchDescendant($node));

        // $builder->insertNode($node, $root);

        // echo $root->display();

        // \var_dump($builder->getRecordedEvents());

    /**
     *
     * @var EventBusService $eventBus ;
     */
        // $eventBus = Bootstrap::getServiceManager()->get(EventBusService::class);
        // $eventBus->dispatch($builder->getRecordedEvents());
    }
}