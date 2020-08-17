<?php
namespace InventoryTest\Item\Rep;

use Doctrine\ORM\EntityManager;
use Inventory\Infrastructure\Doctrine\TrxCmdRepositoryImpl;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class CmdRepTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new TrxCmdRepositoryImpl($doctrineEM);
            echo $rep->closeTrxOf(2427);
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}