<?php
namespace InventoryTest\Service;

use Doctrine\ORM\EntityManager;
use Inventory\Application\Service\Upload\Transaction\OpenBalanceUpload;
use PHPUnit_Framework_TestCase;

class WarehouseDTOAssemblerTest extends PHPUnit_Framework_TestCase
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
        echo $root;

        $uploader = new OpenBalanceUpload();

        $file = $root . "/InventoryTest/Data/ob.xlsx";
        $uploader->doUploading($file);
    }
}