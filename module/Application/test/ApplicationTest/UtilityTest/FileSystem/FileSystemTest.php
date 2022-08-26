<?php
namespace ApplicationTest\UtilityTest\FileSystem;

use Application\Application\Service\Attachment\AttachmentServiceImpl;
use Application\Domain\Util\FileSystem\FileHelper;
use Application\Domain\Util\FileSystem\FolderHelper;
use Application\Domain\Util\FileSystem\MimeType;
use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;

/**
 *
 * @author Nguyen Mau Tri - Ngmautri@gmail.com
 *        
 */
class FileSystemTest extends PHPUnit_Framework_TestCase
{

    protected $em;

    protected $serviceManager;

    /**
     *
     * @var EntityManager $em;
     */
    public function setUp()
    {}

    public function testOther()
    {
        $fname = FileHelper::generateName();
        var_dump($fname);
        var_dump(FileHelper::makePathFromFileName($fname));

        var_dump(FileHelper::generateNameAndPath());

        $curDir = dirname(__FILE__);
        $path_root = dirname($curDir);

        // FixMe
        $path = $path_root . '/TestData/beptu.jpg';
        $path = FolderHelper::linuxPath($path);
        // var_dump(file_exists($path)) . "\n";
        var_dump(MimeType::isImage($path));

        // $file = new File($path);
        // var_dump($file->info());
        // $dest = $path_root . '/Data/BFL.t3';

        // $file->copy($dest, true);
        // $file->delete();

        // $folder1 = $path_root . '/TestData';

        // $folder = new Folder($folder1);
        // $folder_content = $folder->read();

        // foreach ($folder_content[1] as $f) {
        // $file = new File($folder1 . "/" . $f);
        // echo $file->name() . "--" . $file->md5() . "--" . $file->size() . "\n";
        // }

        $sv = new AttachmentServiceImpl();
        $snapshot = $sv->createAttachmentFileFrom($path);
        var_dump($snapshot);
    }
}