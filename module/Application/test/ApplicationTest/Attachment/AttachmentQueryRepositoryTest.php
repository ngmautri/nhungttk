<?php
namespace ApplicationTest\Attachment;

use ApplicationTest\Bootstrap;
use Application\Infrastructure\AggregateRepository\AttachmentQueryRepImpl;
use PHPUnit_Framework_TestCase;

class AttachmentQueryRepositoryTest extends PHPUnit_Framework_TestCase
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
        $em = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
        $rep = new AttachmentQueryRepImpl($em);
        $snapshot = $rep->getById(189);
        var_dump($snapshot);
        
        
      }
}