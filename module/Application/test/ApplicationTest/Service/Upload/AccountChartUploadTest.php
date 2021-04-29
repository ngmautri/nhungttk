<?php
namespace ApplicationTest\Service\Upload;

use Application\Application\Command\Options\CreateMemberCmdOptions;
use Application\Application\Service\AccountChart\Upload\DefaultAccountChartUpload;
use Application\Infrastructure\Persistence\Domain\Doctrine\ChartQueryRepositoryImpl;
use Doctrine\ORM\EntityManager;
use InventoryTest\Bootstrap;
use PHPUnit_Framework_TestCase;

class AccountChartUploadTest extends PHPUnit_Framework_TestCase
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

            $rep = new \Application\Infrastructure\Persistence\Domain\Doctrine\CompanyQueryRepositoryImpl($doctrineEM);
            $company = $rep->getById(1);
            $companyVO = $company->createValueObject();

            $rep = new ChartQueryRepositoryImpl($doctrineEM);
            $chart = $rep->getById(15);

            $rootEntity = $chart;
            $rootEntityId = 15;
            $rootEntityToken = null;
            $version = null;
            $userId = 39;

            $options = new CreateMemberCmdOptions($companyVO, $rootEntity, $rootEntityId, $rootEntityToken, $version, $userId, __METHOD__);

            $uploader = new DefaultAccountChartUpload();
            $uploader->setDoctrineEM($doctrineEM);

            $logger = Bootstrap::getServiceManager()->get('Applogger');
            $uploader->setLogger($logger);

            $file = $root . "/ApplicationTest/Data/coa.xlsx";
            echo $file;
            $uploader->doUploading($companyVO, $rootEntity, $file, $options);
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }
}