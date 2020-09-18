<?php
namespace InventoryTest\Command;

use Doctrine\ORM\EntityManager;
use InventoryTest\Bootstrap;
use Inventory\Application\Command\GenericCmd;
use Inventory\Application\Command\TransactionalCmdHandlerDecorator;
use Inventory\Application\Command\Transaction\CreateHeaderCmdHandler;
use Inventory\Application\Command\Transaction\Options\TrxCreateOptions;
use Inventory\Application\DTO\Transaction\TrxDTO;
use Inventory\Domain\Transaction\Contracts\TrxType;
use PHPUnit_Framework_TestCase;

class CreateCmdTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $companyId = 1;
            $userId = 0;
            $localCurrencyId = 2;

            $dto = new TrxDTO();
            $dto->movementType = TrxType::GI_FOR_COST_CENTER;
            $dto->docNumber = "SDD";
            $dto->docDate = "2020-04-02";
            $dto->docCurrency = 248;
            $dto->vendor = 229;
            $dto->warehouse = 5;
            $dto->grDate = "2020-04-02";
            $dto->postingDate = "2020-04-02";
            $dto->pmtTerm = 1;

            $options = new TrxCreateOptions($companyId, $localCurrencyId, $userId, __METHOD__);

            $cmdHandler = new CreateHeaderCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new GenericCmd($doctrineEM, $dto, $options, $cmdHandlerDecorator);
            $cmd->execute();
            var_dump($dto->getNotification());
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}