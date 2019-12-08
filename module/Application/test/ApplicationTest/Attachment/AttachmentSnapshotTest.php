<?php
namespace ApplicationTest\Attachment;

use Ramsey\Uuid\Uuid;
use PHPUnit_Framework_TestCase;
use Doctrine\ORM\EntityManager;
use ApplicationTest\Bootstrap;
use Application\Application\DTO\Company\CompanyDTOAssembler;
use Application\Domain\Company\CompanySnapshotAssembler;
use Application\Domain\Attachment\AttachmentSnapshotAssembler;

class AttachmentSnapshotTest extends PHPUnit_Framework_TestCase
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
          AttachmentSnapshotAssembler::createProperities();
    }
}