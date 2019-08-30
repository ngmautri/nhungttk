<?php
namespace ProcureTest\Service;

use Doctrine\ORM\EntityManager;
use Procure\Application\DTO\Ap\APInvoiceDTOAssembler;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Procure\Application\Service\FXService;
use ProcureTest\Bootstrap;

class FXServiceTest extends PHPUnit_Framework_TestCase
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
        // echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
        /** @var EntityManager $doctrineEM ; */
        $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

        $sv = new FXService($doctrineEM);
        $sv->setDoctrineEM($doctrineEM);
        
        var_dump($sv->checkAndReturnFX(248, 2,8690));
    }
}