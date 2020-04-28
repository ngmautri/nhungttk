<?php
namespace ProcureTest\Qr\Rep;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Domain\AccountPayable\APDoc;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Doctrine\QRQueryRepositoryImpl;
use PHPUnit_Framework_TestCase;

class QueryRepTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new QRQueryRepositoryImpl($doctrineEM);

            $id = 341;
            $token = "gPufszEZUo_4K20B1bdzs3eYs47TZjSr";

            /**
             *
             * @var APDoc $rootEntity ;
             */
            // $rootEntity = $rep->getRootEntityByTokenId($id, $token);

            $rowId = 2102;
            $rootEntity = $rep->getHeaderIdByRowId(2102);

            var_dump($rootEntity);
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}