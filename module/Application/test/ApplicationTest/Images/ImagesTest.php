<?php
namespace ApplicationTest\Images;

use Application\Domain\Util\FileSystem\FolderHelper;
use Cake\Filesystem\File;
use Doctrine\ORM\EntityManager;
use Intervention\Image\ImageManager;
use PHPUnit_Framework_TestCase;

class ImagesTest extends PHPUnit_Framework_TestCase
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
        $path = $curDir . '/Data/1.jpeg';
        $path = FolderHelper::linuxPath($path);
        $file = new File($path);
        // var_dump($file->info());

        // open an image file
        // create an image manager instance with favored driver
        $manager = new ImageManager(array(
            // 'driver' => 'imagick'
        ));

        // to finally create image instances
        $img = $manager->make($path);

        // write text at position
        $img->text('The quick brown fox jumps over the lazy dog.', 120, 100);

        // now you are able to resize the instance
        $img->resize(800, null, function ($constraint) {
            $constraint->aspectRatio();
        });

        $img->rotate(- 90);

        /*
         * // draw transparent text
         * $img->text('ngmautri', 0, 0, function ($font) {
         * $font->size(24);
         * $font->color('#fdf6e3');
         * $font->align('center');
         * $font->valign('top');
         * $font->angle(45);
         * });
         */

        // $img->blur();

        // and insert a watermark for example
        // $img->insert('public/watermark.png');

        // finally we save the image as a new file
        $img->save($curDir . '/Data/2.jpeg');
    }
}