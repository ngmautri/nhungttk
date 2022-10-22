<?php
namespace ApplicationTest\Attachment\Domain;

use ApplicationTest\Bootstrap;
use Application\Application\Command\Options\CmdOptions;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Attachment\BaseAttachmentSnapshot;
use Application\Domain\Attachment\Factory\AttachmentFactory;
use Application\Domain\Attachment\Service\AttachmentPostingService;
use Application\Domain\Attachment\Test\TestAttachmentCmdRep;
use Application\Domain\Company\CompanyVO;
use Application\Domain\Service\SharedService;
use PHPUnit_Framework_TestCase;

class FactoryTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            $rep = new TestAttachmentCmdRep();
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
            $sharedSpecsFactory = new ZendSpecificationFactory($doctrineEM);
            $postingService = new AttachmentPostingService($rep);
            $sharedService = new SharedService($sharedSpecsFactory, $postingService);

            $companyVO = new CompanyVO();
            $options = new CmdOptions($companyVO, 39, __METHOD__);

            $snapshot = new BaseAttachmentSnapshot();
            $snapshot->subject = "testdsf";
            $snapshot->createdBy = 39;

            $rootEntity = AttachmentFactory::createFrom($snapshot, $options, $sharedService);
            var_dump($rootEntity);
        } catch (\Exception $e) {
            echo $e->getMessage();
            echo "====================";
            echo $e->getTraceAsString();
        }
    }
}