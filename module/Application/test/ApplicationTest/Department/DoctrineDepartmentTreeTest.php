<?php
namespace ApplicationTest\Department;

use ApplicationTest\Bootstrap;
use Application\Application\Command\Options\CmdOptions;
use Application\Application\EventBus\EventBusService;
use Application\Application\Service\Department\Tree\DepartmentNode;
use Application\Application\Service\Department\Tree\DepartmentTree;
use Application\Infrastructure\Persistence\Domain\Doctrine\CompanyQueryRepositoryImpl;
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
        $node->setId("Maintenance");
        $node->setNodeName('Maintenance');
        $node->setNodeCode('Maintenance');

        // var_dump($root->isNodeDescendant($node));

        $n = $root->getNodeByName("rmqc");

        $rep = new CompanyQueryRepositoryImpl($doctrineEM);
        $companyVO = $rep->getById(1)->createValueObject();

        $options = new CmdOptions($companyVO, 39, __METHOD__);
        $builder->insertNode($node, $n, $options);
        \var_dump($builder->getRecordedEvents());
        /**
         *
         * @var EventBusService $eventBus ;
         */
        $eventBus = Bootstrap::getServiceManager()->get(EventBusService::class);
        // $eventBus->dispatch($builder->getRecordedEvents());
    }
}