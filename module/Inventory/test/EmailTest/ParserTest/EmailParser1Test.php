<?php
namespace EmailTest\ParserTest;

use Mail\MailParser;
use PHPUnit_Framework_TestCase;
use PhpMimeMailParser\Parser;
use PhpMimeMailParser\Charset;
use Faker\Factory;

class EmailParser1Test extends PHPUnit_Framework_TestCase
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

        $faker = Factory::create();

        $values = array();

        for ($i = 0; $i < 10; $i ++) {
            // get a random digit, but also null sometimes
            $values[] = $faker->city();
        }

        var_dump($values);
    }
}