<?php
namespace ProcureTest\Service\Upload;

use Doctrine\ORM\EntityManager;
use InventoryTest\Bootstrap;
use Inventory\Application\Service\Upload\Transaction\TrxRowsUpload;
use Inventory\Domain\Transaction\GenericTrx;
use PHPUnit_Framework_TestCase;
use Procure\Infrastructure\Doctrine\PRQueryRepositoryImpl;
use Procure\Application\Service\Upload\UploadPrRows;
use Application\Infrastructure\Doctrine\CompanyQueryRepositoryImpl;

class PrRowsUploadTest extends PHPUnit_Framework_TestCase
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
        try {

            $root = realpath(dirname(dirname(dirname(dirname(__FILE__)))));

            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
            $rep = new PRQueryRepositoryImpl($doctrineEM);

            $id = 1404;
            $token = "445a04f4-9e2c-4318-ae0d-41a176ff38e4";

            $rootEntity = $rep->getRootEntityByTokenId($id, $token);

            $rep1 = new CompanyQueryRepositoryImpl($doctrineEM);

            $company = $rep1->getById($rootEntity->getCompany());
            $companyVO = $company->createValueObject();
            \var_dump($companyVO);

            $uploader = new UploadPrRows($doctrineEM);
            $file = $root . "/ProdureTest/Data/PrInput.xlsx";
            $uploader->doUploading($companyVO, $rootEntity, $file);
        } catch (\Exception $e) {
            var_dump($e->getTraceAsString());
            var_dump($e->getMessage());
        }
    }
}