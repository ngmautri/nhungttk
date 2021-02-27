<?php
namespace ProcureTest\Service\Upload;

use Application\Infrastructure\Doctrine\CompanyQueryRepositoryImpl;
use Doctrine\ORM\EntityManager;
use InventoryTest\Bootstrap;
use Procure\Application\Service\Upload\UploadFactory;
use Procure\Infrastructure\Doctrine\POQueryRepositoryImpl;
use PHPUnit_Framework_TestCase;

class PoRowsUploadTest extends PHPUnit_Framework_TestCase
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
            $rep = new POQueryRepositoryImpl($doctrineEM);

            $id = 537;
            $token = "5e6e851d-f6eb-4b9b-b4fe-11043a304931";

            $rootEntity = $rep->getPODetailsById($id, $token);

            $rep1 = new CompanyQueryRepositoryImpl($doctrineEM);

            $company = $rep1->getById($rootEntity->getCompany());
            $companyVO = $company->createValueObject();
            // \var_dump($companyVO);

            $uploader = UploadFactory::create($rootEntity->getDocType(), $doctrineEM);
            $logger = Bootstrap::getServiceManager()->get('Applogger');
            $uploader->setLogger($logger);

            $file = $root . "/ProcureTest/Data/PoInput.xlsx";
            $uploader->doUploading($companyVO, $rootEntity, $file);
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }
}