<?php
namespace ProcureTest\PR;

use Procure\Application\DTO\Po\PORowDTOAssembler;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Intl\ResourceBundle\CurrencyBundle;
use Symfony\Component\Intl\Intl;

class PoRowDTOAssemblerTest extends PHPUnit_Framework_TestCase
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

            //PORowDTOAssembler::createStoreMapping();
            $currencies = Intl::getCurrencyBundle()->getCurrencyNames();
            var_dump($currencies);
           } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}