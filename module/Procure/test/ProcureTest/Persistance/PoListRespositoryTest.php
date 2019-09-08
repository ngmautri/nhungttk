<?php
namespace ProcureTest\Model;

use PHPUnit_Framework_TestCase;
use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Persistence\Doctrine\POListRepository;

class PoListRespository extends PHPUnit_Framework_TestCase
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
        try {

            /** @var EntityManager $em ; */
            $em = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
            // echo Uuid::uuid4()->toString();

            $rep = new POListRepository($em);

            $result = $rep->getPoList();
            var_dump(count($result));
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}