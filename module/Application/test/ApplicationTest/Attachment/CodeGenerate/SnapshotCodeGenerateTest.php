<?php
namespace ApplicationTest\ItemAttribute\CodeGenerate;

use Application\Application\Contracts\GenericSnapshotAssembler;
use Application\Domain\Attachment\AbstractAttachmentFile;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class SnapshotCodeGenerateTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {

            // $result = GenericSnapshotAssembler::findMissingProps(HrAttachment::class, AbstractAttachment::class);
            // $result = GenericSnapshotAssembler::findMissingProps(HrAttachmentFile::class, AbstractAttachmentFile::class);

            $result = GenericSnapshotAssembler::createAllSnapshotProps(AbstractAttachmentFile::class);

            // \var_dump(($result));
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}