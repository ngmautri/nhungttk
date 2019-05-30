<?php
namespace ApplicationTest\Model;

use Ramsey\Uuid\Uuid;
use PHPUnit_Framework_TestCase;
use Application\Domain\Company\CompanyId;
use Application\Domain\Company\Company;
use Application\Domain\Shared\Currency;
use ApplicationTest\Bootstrap;
use Doctrine\ORM\EntityManager;
use Application\Domain\Company\Doctrine\DoctrineCompanyRepository;
use Application\Domain\Exception\InvalidArgumentException;
use Application\Domain\Service\DoctrineSharedService;
use Application\Application\Specification\ZendSpecificationFactory;

class SpecficationTest extends PHPUnit_Framework_TestCase
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
            
            
            $factory = new \Application\Application\Specification\Zend\ZendSpecificationFactory();
            $spec = $factory->getEmailSpecification();
            var_dump($spec->isSatisfiedBy("2016-12-05dsfads@mascot.dk"));

    }
}