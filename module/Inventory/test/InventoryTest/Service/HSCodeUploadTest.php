<?php
namespace InventoryTest\Service;

use Doctrine\ORM\EntityManager;
use InventoryTest\Bootstrap;
use Inventory\Application\Service\Upload\HSCode\HSCodeUpload;
use PHPUnit_Framework_TestCase;

class HSCodeUploadTest extends PHPUnit_Framework_TestCase
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
        $root = realpath(dirname(dirname(dirname(__FILE__))));
        $uploader = Bootstrap::getServiceManager()->get(HSCodeUpload::class);

        $file = $root . "/InventoryTest/Data/hscode-2002.xlsx";

        $uploader->doUploading($file);
    }
}