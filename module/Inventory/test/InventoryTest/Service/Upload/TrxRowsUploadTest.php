<?php
namespace InventoryTest\Service\Upload;

use Doctrine\ORM\EntityManager;
use InventoryTest\Bootstrap;
use Inventory\Application\Service\Upload\Transaction\TrxRowsUpload;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Infrastructure\Doctrine\TrxQueryRepositoryImpl;
use PHPUnit_Framework_TestCase;

class TrxRowsUploadTest extends PHPUnit_Framework_TestCase
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
        $root = realpath(dirname(dirname(dirname(dirname(__FILE__)))));
        echo $root;

        $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
        $rep = new TrxQueryRepositoryImpl($doctrineEM);

        $id = 1415;
        $token = "53c733c3-f9c4-411d-90f6-7ea596b4bf26";

        $rootEntity = $rep->getRootEntityByTokenId($id, $token);
        var_dump(count($rootEntity->getDocRows()));

        $uploader = Bootstrap::getServiceManager()->get(TrxRowsUpload::class);

        $file = $root . "/InventoryTest/Data/ob.xlsx";

        /**
         *
         * @var GenericTrx $trx
         */
        $trx = $uploader->doUploading($rootEntity, $file);
        var_dump(count($trx->getDocRows()));
    }
}