<?php
namespace EmailTest\ParserTest;

use Mail\MailParser;
use PHPUnit_Framework_TestCase;
use PhpMimeMailParser\Parser;
use PhpMimeMailParser\Charset;

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

        $file = $root . '/Email/1.msg';
        // echo $file;
        $parser = new Parser(new Charset("utf-8"));
        // 1. Specify a file path (string)
        // $parser->setPath($file);

        $parser->setText(file_get_contents($file));

        var_dump($parser->getRawHeader("subject"));

        $text = $parser->getMessageBody('text');
        // var_dump($text);

        $attachments = $parser->getAttachments();

        foreach ($attachments as $attachment) {
            // echo 'Filename : '.$attachment->getFilename().'<br />';
            // return logo.jpg
        }
    }
}