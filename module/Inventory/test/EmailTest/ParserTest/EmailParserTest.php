<?php
namespace EmailTest\ParserTest;

use PHPUnit_Framework_TestCase;
use Doctrine\ORM\EntityManager;
use InventoryTest\Bootstrap;
use Inventory\Domain\Exception\InvalidArgumentException;
use Ramsey\Uuid\Uuid;
use Inventory\Domain\Item\Factory\InventoryItemFactory;
use Inventory\Application\Service\Item\ItemCRUDService;
use Inventory\Domain\Item\ItemType;
use Mail\MailParser;

class EmailParserTest extends PHPUnit_Framework_TestCase
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
        echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
            $root = realpath(dirname(dirname(dirname(__FILE__))));
        
            $file = $root . '/Email/DKSH Lao account.msg';
            $email = new MailParser(file_get_contents($file));
            //var_dump($email);
        
    }
}