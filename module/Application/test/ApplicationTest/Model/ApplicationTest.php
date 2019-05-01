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

class ApplicationTest extends PHPUnit_Framework_TestCase
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
        try {

            /** @var EntityManager $em ; */
            $em = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            //$resources = $em->getRepository('Application\Entity\MlaUsers')->findall();
            //echo count($resources);

            //$company = new Company(new CompanyId(Uuid::uuid4()->toString()), "Mascot", new Currency("LAK"));
            //var_dump ($company);

            $uuid = "466829bf-b754-4341-aafe-11e3ac841847";
            $rep = new DoctrineCompanyRepository($em);
            var_dump($rep->getByUUID($uuid));
            
            //$rep = new DoctrineSharedService($em);
            //var_dump($rep->getCurrencyList());
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}