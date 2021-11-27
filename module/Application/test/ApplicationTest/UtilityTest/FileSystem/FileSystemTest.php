<?php
namespace ApplicationTest\UtilityTest\FileSystem;

use Application\Domain\Util\FileSystem\FolderHelper;
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
        $curDir = dirname(__FILE__);
        $path = dirname($curDir);

        $path = $path . '/data/Forklift';
        $path = FolderHelper::linuxPath($path);
        echo $path;

        $file = new File($path);

        var_dump($file->info());
    }
}