<?php
namespace ProcureTest\AP;

use Procure\Application\DTO\Pr\PrDTOAssembler;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Procure\Application\DTO\Ap\APInvoiceDTO;
use Procure\Application\DTO\Ap\APInvoiceDTOAssembler;
use Procure\Application\DTO\Ap\APInvoiceRowDTOAssembler;

class ApInvoiceRowDTOAssemblerTest extends PHPUnit_Framework_TestCase
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

            APInvoiceRowDTOAssembler::createDTOProperities();

           } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}