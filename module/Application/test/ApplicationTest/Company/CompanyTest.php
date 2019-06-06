<?php
namespace ApplicationTest\Company;

use Ramsey\Uuid\Uuid;
use PHPUnit_Framework_TestCase;
use Doctrine\ORM\EntityManager;
use ApplicationTest\Bootstrap;
use Application\Application\DTO\Company\CompanyDTOAssembler;
use Application\Domain\Company\CompanySnapshotAssembler;
use Application\Domain\Company\GenericCompany;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Infrastructure\AggregateRepository\DoctrineCompanyRepository;

class CompanyTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    /**
     *
     * @var EntityManager $em;
     */
    protected $em;

    public function setUp()
    {
        $root = realpath(dirname(dirname(dirname(__FILE__))));
        echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
        $em = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

        $data = array();

        $data["defaultCurrency"] = 1;
        $data["companyCode"] = 1;
        $data["companyName"] = "Mascot Laos";
        $data["defaultWarehouse"] = 5;

        /**
         *
         * @var \Application\Application\DTO\Company\CompanyDTO $dto
         */
        $dto = CompanyDTOAssembler::createDTOFromArray($data);

        // var_dump($dto);

        $snapshot = CompanySnapshotAssembler::createSnapshotFromDTO($dto);
        //var_dump($snapshot);
        
        $company = new GenericCompany();        
        $company->setSharedSpecificationFactory(new ZendSpecificationFactory($em));
        $company->makeFromSnapshot($snapshot);
        
        
        //var_dump($company->validate());
        
        $rep = new DoctrineCompanyRepository($em);
        $companySN = $rep->getById(1);
        var_dump($companySN);
        
        
      }
}