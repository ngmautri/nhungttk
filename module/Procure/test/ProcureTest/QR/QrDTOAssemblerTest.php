<?php
namespace ProcureTest\QR;

use Doctrine\ORM\EntityManager;
use Procure\Application\DTO\Qr\QrDTOAssembler;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class QrDTOAssemblerTest extends PHPUnit_Framework_TestCase
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
        try {

            $result = QrDTOAssembler::createDTOProperities();
            // var_dump($result);
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}