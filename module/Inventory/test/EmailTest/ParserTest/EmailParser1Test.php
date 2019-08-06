<?php
namespace EmailTest\ParserTest;

use Mail\MailParser;
use PHPUnit_Framework_TestCase;
use PhpMimeMailParser\Parser;
use PhpMimeMailParser\Charset;

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
        
            $file = $root . '/Email/1.msg';
            //echo $file;
            $emailPath = $root . '/Email/1.msg';
            $emailParser = new MailParser(file_get_contents($emailPath));
          var_dump( $emailParser->getTo());
            
    }
}