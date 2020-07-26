<?php
namespace InventoryTest\Transaction;

use Doctrine\ORM\EntityManager;
use InventoryTest\Bootstrap;
use Inventory\Application\DTO\Transaction\TrxDTOAssembler;
use PHPUnit_Framework_TestCase;

class TrxDTOAssemblerTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        /** @var EntityManager $em ; */
        $em = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

        TrxDTOAssembler::createGetMapping();
    }
}