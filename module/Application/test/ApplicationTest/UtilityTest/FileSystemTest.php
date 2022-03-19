<?php
namespace ApplicationTest\UtilityTest;

use Cake\Utility\Security;
use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;

class FileSystemTest extends PHPUnit_Framework_TestCase
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
        var_dump(Security::randomString(50));
    }
}