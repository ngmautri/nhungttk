<?php
namespace ApplicationTest\UtilityTest;

use Application\Utility\Composite\Composite;
use Application\Utility\Composite\Leaf;
use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;

class CompositeTest extends PHPUnit_Framework_TestCase
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
        $tree = new Composite();
        $branch1 = new Composite();
        $branch1->add(new Leaf());
        $branch1->add(new Leaf());
        $branch2 = new Composite();
        $branch2->add(new Leaf());
        $tree->add($branch1);
        $tree->add($branch2);

        print_r($tree->generateJsTree());
    }
}