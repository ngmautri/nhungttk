<?php
namespace ProcureTest\DTO;

use PHPUnit_Framework_TestCase;
use Doctrine\ORM\EntityManager;
use Ramsey\Uuid\Uuid;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Model\Domain\PurchaseRequest\PurchaseRequest;
use Procure\Domain\APInvoice\Repository\Doctrine\DoctrineAPInvoiceRepository;
use Procure\Infrastructure\Persistence\DoctrinePRListRepository;
use Procure\Application\DTO\Pr\PrRowReportDTOAssembler;
use Procure\Application\DTO\Pr\PrRowDTOAssembler;

class PrRowDTOTest extends PHPUnit_Framework_TestCase
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
        //echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
        try {

                PrRowDTOAssembler::createMapping();

           } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}