<?php
namespace InventoryTest\Item\Rep;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
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

            $rep = new ItemQueryRepositoryImpl($doctrineEM);

            $id = 2427;
            $token = "4eyUIwcFv8_KxuYVvMdn4freRdKAyg1Q";

            $rootEntity = $rep->getRootEntityByTokenId($id, $token);
            $input = new ArrayCollection();

            $data = array(
                'red',
                'yellow',
                'black',
                'green',
                'blue',
                'cayan'
            );
            $input->add($data);

            $data = array(
                'xs',
                's',
                'm',
                'l',
                'xl',
                '2xl',
                '3xl',
                '4xl',
                '5xl',
                'spe'
            );
            $input->add($data);

            $data = array(
                'cotton 100%',
                'cotton 60%; polyster 40%',
                'cotton 40%; polyster 60%'
            );
            $input->add($data);

            // \var_dump($input);

            \var_dump($rootEntity->generateVariants($input));
        } catch (InvalidArgumentException $e) {
            // var_dump($e->getMessage());
        }
    }
}