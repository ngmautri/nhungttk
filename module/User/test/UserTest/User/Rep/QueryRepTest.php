<?php
namespace UserTest\Warehouse\Rep;

use Doctrine\ORM\EntityManager;
use UserTest\Bootstrap;
use User\Application\Service\ACLRole\Tree\ACLRoleTree;
use User\Infrastructure\Doctrine\UserQueryRepositoryImpl;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Application\Domain\Util\Tree\Output\ArrayFormatter;

class QueryRepTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new UserQueryRepositoryImpl($doctrineEM);

            $id = 39;

            $rootEntity = $rep->getById($id);

            $builder = Bootstrap::getServiceManager()->get(ACLRoleTree::class);
            \var_dump($rootEntity->isParentOf(47, $rep, $builder));

            // $parents = $rootEntity->getParentRoles($builder);
            // \var_dump($parents);
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}