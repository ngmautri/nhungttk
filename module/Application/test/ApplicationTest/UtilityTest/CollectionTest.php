<?php
namespace ApplicationTest\UtilityTest;

use Application\Domain\Util\SimpleCollection;
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
    {
        $root = realpath(dirname(dirname(dirname(__FILE__))));
        echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
        $collection = new SimpleCollection();
        $collection->add(12);
        var_dump($collection->getAll());
    }
}