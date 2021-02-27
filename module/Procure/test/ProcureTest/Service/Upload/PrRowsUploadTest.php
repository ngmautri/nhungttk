<?php
namespace ProcureTest\Service\Upload;

use Application\Infrastructure\Doctrine\CompanyQueryRepositoryImpl;
use Doctrine\ORM\EntityManager;
use InventoryTest\Bootstrap;
use Procure\Application\Service\Upload\UploadPrRows;
use Procure\Infrastructure\Doctrine\PRQueryRepositoryImpl;
use PHPUnit_Framework_TestCase;

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

            $id = 1409;
            $token = "c18e2647-1311-4d9f-82cb-7aacbda0fb1e";

            $rootEntity = $rep->getRootEntityByTokenId($id, $token);

            $rep1 = new CompanyQueryRepositoryImpl($doctrineEM);

            $company = $rep1->getById($rootEntity->getCompany());
            $companyVO = $company->createValueObject();
            // \var_dump($companyVO);

            $uploader = new UploadPrRows($doctrineEM);

            $logger = Bootstrap::getServiceManager()->get('Applogger');
            $uploader->setLogger($logger);

            $file = $root . "/ProcureTest/Data/PrInput.xlsx";
            $uploader->doUploading($companyVO, $rootEntity, $file);
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }
}