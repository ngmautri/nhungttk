<?php
namespace ApplicationTest\UtilityTest;

use Application\Domain\Util\Composite\Builder\TestBuilder;
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
        $builder = new TestBuilder();
        $builder->initCategory();
        \var_dump($builder->createComposite(2, 0)->display());
    }
}