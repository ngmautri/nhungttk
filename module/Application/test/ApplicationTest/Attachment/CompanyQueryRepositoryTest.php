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
use Application\Infrastructure\AggregateRepository\DoctrineCompanyQueryRepository;

class CompanyQueryRepositoryTest extends PHPUnit_Framework_TestCase
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
        $rep = new DoctrineCompanyQueryRepository($em);
        $companySN = $rep->getById(1);
        var_dump($companySN);
        
        
      }
}