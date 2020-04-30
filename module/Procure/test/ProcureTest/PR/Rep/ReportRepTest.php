<?php
namespace ProcureTest\GR\Command;

use DoctrineORMModule\Options\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\DTO\Pr\PrHeaderDetailDTO;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Persistence\Doctrine\PrReportRepositoryImpl;
use PHPUnit_Framework_TestCase;

class RepTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new PrReportRepositoryImpl($doctrineEM);

            $result = $rep->getListWithCustomDTO(1, null, null, null, null, null, 0, 0, new PrHeaderDetailDTO());
            \var_dump($rep->getListTotal());
            // var_dump($result[1]);
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}