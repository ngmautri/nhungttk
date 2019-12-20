<?php
namespace ApplicationTest\Attachment;

use Ramsey\Uuid\Uuid;
use PHPUnit_Framework_TestCase;
use Doctrine\ORM\EntityManager;
use ApplicationTest\Bootstrap;
use Application\Application\DTO\Company\CompanyDTOAssembler;
use Application\Application\DTO\Attachment\AttachmentDTOAssembler;

class AttachmentDTOTest extends PHPUnit_Framework_TestCase
{


    public function setUp()
    {
        $root = realpath(dirname(dirname(dirname(__FILE__))));
        echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
       AttachmentDTOAssembler::createStoreMapping();
    }
}