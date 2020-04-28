<?php
namespace ProcureTest\QR;

use Procure\Application\DTO\Qr\QrRowDTOAssembler;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class QRRowDTOAssemblerTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    protected $em;

    public function setUp()
    {}

    public function testOther()
    {
        try {

            $result = QrRowDTOAssembler::createGetMapping();
            // var_dump($result);
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}