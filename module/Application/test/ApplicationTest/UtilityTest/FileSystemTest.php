<?php
namespace ApplicationTest\UtilityTest;

use Cake\Filesystem\File;
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
        $path = $curDir . '/data/Forklift.xlsx';

        echo $path;

        $file = new File($path);

        var_dump($file->exists());
    }
}