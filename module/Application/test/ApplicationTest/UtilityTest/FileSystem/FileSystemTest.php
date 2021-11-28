<?php
namespace ApplicationTest\UtilityTest\FileSystem;

use Application\Domain\Util\FileSystem\FileHelper;
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
        var_dump(FileHelper::makePathFromFileName("b3303a67-5e34-4ada-ba4b-df6ebf69fa76_61a372beba9c3"));

        var_dump(FileHelper::generateNameAndPath());

        $curDir = dirname(__FILE__);
        $path = dirname($curDir);

        $path = $path . '/data/BFL.t';
        $path = FolderHelper::linuxPath($path);
        // echo $path;

        $file = new File($path);

        var_dump($file->info());
    }
}