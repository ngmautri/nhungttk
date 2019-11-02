<?php
namespace ApplicationTest\UtilityTest;

use ApplicationTest\Bootstrap;
use Doctrine\ORM\EntityManager;
use User\Infrastructure\Persistence\DoctrineUserRepository;
use PHPUnit_Framework_TestCase;
use Application\Utility\Composite\Composite;
use Application\Utility\Composite\Leaf;

class CompositeTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    /**
     *
     * @var EntityManager $em;
     */
    protected $em;

    public function setUp()
    {
        echo "Test starting";
        
        $root = realpath(dirname(dirname(dirname(__FILE__))));
        echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
        $root = realpath(dirname(dirname(dirname(__FILE__))));
        echo $root;
        require ($root . '/Bootstrap.php');
        
        $tree = new Composite();
        $branch1 = new Composite;
        $branch1->add(new Leaf());
        $branch1->add(new Leaf());
        $branch2 = new Composite;
        $branch2->add(new Leaf());
        $tree->add($branch1);
        $tree->add($branch2);
        
        print_r($tree->generateJsTree());
    }
}