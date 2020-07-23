<?php
namespace ApplicationTest\UtilityTest;

use Application\Domain\Util\Tree\Test\TestTree;
use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;

class TreeTest extends PHPUnit_Framework_TestCase
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
        $builder = new TestTree();
        $builder->initTree();
        // \var_dump($builder->createTree(1, 0)->display(new SimpleFormatter()));

        \var_dump($builder->createTree(1, 0)->isRoot());
    }
}