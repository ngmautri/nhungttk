<?php
namespace InventoryTest\Item\Change;

use Doctrine\ORM\EntityManager;
use InventoryTest\Bootstrap;
use Inventory\Application\Command\GenericCmd;
use Inventory\Application\Command\TransactionalCmdHandlerDecorator;
use Inventory\Application\Command\Item\UpdateCmdHandler;
use Inventory\Application\Command\Item\Options\UpdateItemOptions;
use Inventory\Application\DTO\Item\ItemDTO;
use Inventory\Application\EventBus\EventBusService;
use Inventory\Infrastructure\Doctrine\ItemQueryRepositoryImpl;
use PHPUnit_Framework_TestCase;

class ChangeItemGroupTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    /**
     *
     * @var EntityManager $em;
     */
    protected $em;

    public function setUp()
    {}

    public function testOther()
    {
        $current_id = null;

        try {

            $root = realpath(dirname(dirname(dirname(dirname(__FILE__)))));
            $file = $root . "/InventoryTest/Data/itemId.php";

            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $items = include $file;
            foreach ($items as $item) {

                $eventBusService = Bootstrap::getServiceManager()->get(EventBusService::class);

                $companyId = 1;
                $userId = 39;

                $rootEntityId = $item;
                $version = '';

                $queryRep = new ItemQueryRepositoryImpl($doctrineEM);
                $rootEntity = $queryRep->getRootEntityById($rootEntityId);

                $version = $rootEntity->getRevisionNo();

                $current_id = $item;

                /**
                 *
                 * @var ItemDTO $dto ;
                 */
                $dto = $rootEntity->makeDTO();
                $dto->itemGroup = 13;

                if ($rootEntity->getCreatedOn() == null) {
                    $dto->createdOn = '2017-01-01';
                }

                $options = new UpdateItemOptions($rootEntity, $rootEntityId, '', $version, $userId, __METHOD__);

                $cmdHandler = new UpdateCmdHandler();
                $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
                $cmd = new GenericCmd($doctrineEM, $dto, $options, $cmdHandlerDecorator, $eventBusService);
                $cmd->execute();

                // var_dump($dto->getNotification());
            }

            $doctrineEM->flush();
        } catch (\Exception $e) {
            \var_dump($e->getMessage());
            \var_dump($current_id);
        }
    }
}