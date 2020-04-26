<?php
namespace ApplicationTest\Company;

use ApplicationTest\Bootstrap;
use Application\Application\DTO\Company\CompanyDTOAssembler;
use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;

class CompanyDTOTest extends PHPUnit_Framework_TestCase
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
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
        $em = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

        CompanyDTOAssembler::createGetMapping();
    }
}