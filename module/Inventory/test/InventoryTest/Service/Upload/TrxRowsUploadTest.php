<?php
namespace InventoryTest\Service\Upload;

use Doctrine\ORM\EntityManager;
use InventoryTest\Bootstrap;
use Inventory\Application\Service\Upload\Transaction\TrxRowsUpload;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Infrastructure\Doctrine\TrxQueryRepositoryImpl;
use PHPUnit_Framework_TestCase;
use Procure\Application\Service\Upload\UploadPrRows;

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

        $id = 1416;
        $token = "1623f80f-c267-4d10-b2f4-0f908a0a2229";

        $rootEntity = $rep->getRootEntityByTokenId($id, $token);
        var_dump(count($rootEntity->getDocRows()));

        $uploader = new UploadPrRows();

        $file = $root . "/InventoryTest/Data/ob.xlsx";

        /**
         *
         * @var GenericTrx $trx
         */
        $trx = $uploader->doUploading($rootEntity, $file);
        var_dump(($trx->getDocRows()[0]));
    }
}