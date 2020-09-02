<?php
namespace InventoryTest\Transaction\Change;

use Application\Domain\Shared\Constants;
use Doctrine\ORM\EntityManager;
use InventoryTest\Bootstrap;
use PHPUnit_Framework_TestCase;

class ChangeTest extends PHPUnit_Framework_TestCase
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
        $root = realpath(dirname(dirname(dirname(dirname(__FILE__)))));
        $file = $root . "/InventoryTest/Data/itemId.php";

        /** @var EntityManager $doctrineEM ; */
        $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

        $items = include $file;

        foreach ($items as $id) {

            $criteria = array(
                'item' => $id
            );

            /**
             *
             * @var \Application\Entity\NmtInventoryTrx $entity ;
             */

            $results = $doctrineEM->getRepository('\Application\Entity\NmtInventoryTrx')->findBy($criteria);

            if (count($results) > 0) {
                foreach ($results as $entity) {
                    $entity->setDocStatus(Constants::DOC_STATUS_ARCHIVED);
                    $doctrineEM->persist($entity);
                }
            }
        }

        $doctrineEM->flush();
    }
}