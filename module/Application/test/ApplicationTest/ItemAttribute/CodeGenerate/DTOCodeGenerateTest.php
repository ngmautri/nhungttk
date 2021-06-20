<?php
namespace ApplicationTest\ItemAttribute\CodeGenerate;

use Application\Domain\Company\Brand\BrandSnapshot;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class DTOCodeGenerateTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {

            // $result = AttributeGroupSnapshotAssembler::createFormElementsExclude(AttributeGroupSnapshot::class);
            // $result = AttributeGroupSnapshotAssembler::createFormElementsFunctionExclude(AttributeGroupSnapshot::class);

            // $result = AttributeSnapshotAssembler::createFormElementsExclude(AttributeSnapshot::class);
            // $result = AttributeGroupSnapshotAssembler::createFormElementsFunctionExclude(AttributeSnapshot::class);

            // $result = AccountSnapshotAssembler::G(AccountSnapshot::class);

            // $result = GenericObjectAssembler::getMethodsComments(AppCoaAccount::class);
            $result = GenericObjectAssembler::createStoreMapping(BrandSnapshot::class);

            // \var_dump(($result));
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}