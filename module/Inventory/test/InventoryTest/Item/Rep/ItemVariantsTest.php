<?php
namespace InventoryTest\Item\Rep;

use Application\Application\Command\Options\CmdOptions;
use Application\Infrastructure\Persistence\Domain\Doctrine\CompanyQueryRepositoryImpl;
use Doctrine\ORM\EntityManager;
use Inventory\Application\Service\SharedServiceFactory;
use Inventory\Infrastructure\Doctrine\ItemQueryRepositoryImpl;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class ItemVariantsTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testCanCreateVariant()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new CompanyQueryRepositoryImpl($doctrineEM);
            // $filter = new CompanyQuerySqlFilter();
            $company = $rep->getById(1);
            $companyVO = $company->createValueObject();

            $rep = new ItemQueryRepositoryImpl($doctrineEM);

            $id = 2427;
            $token = "4eyUIwcFv8_KxuYVvMdn4freRdKAyg1Q";

            $rootEntity = $rep->getRootEntityByTokenId($id, $token);

            $input = [];

            $data = array(
                'red',
                'RED',
                'black'
            );
            $input['color'] = $data;

            $data = array(
                'xs',
                's',
                'm',
                'l',
                'xl',
                '2xl'
            );
            $input['size'] = $data;

            $data = array(
                'cotton 100%',
                'cotton 60%; polyster 40%'
            );
            $input['material'] = $data;

            $userId = 39;
            \var_dump($input);

            $options = new CmdOptions($companyVO, $userId, __METHOD__);

            $sharedService = SharedServiceFactory::createForItem($doctrineEM);

            // $variant = $rootEntity->generateVariants($input, $options, $sharedService);

            // \var_dump($variant->count());
        } catch (InvalidArgumentException $e) {
            // var_dump($e->getMessage());
        }
    }
}